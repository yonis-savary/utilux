import { ipcMain } from "electron";
import Store from 'electron-store'

const store = new Store<Record<string, any>>();
const interpolatedCache: Record<string, any> = {};

export const registerConfigHandlers = () => {
  ipcMain.handle('config:get', (_, key: string) => {
    delete interpolatedCache[key]
    return store.get(key)
  })
  ipcMain.handle('config:set', (_, key: string, value: any) => store.set(key, value))
  ipcMain.handle('config:delete', (_, key: string) => {
    store.delete(key)
    delete interpolatedCache[key]
  })
  ipcMain.handle('config:getAll', () => store.store)
}


export const getConfigValue = (key: string) => {
  if (key in interpolatedCache) 
    return interpolatedCache[key]

  let value = (store.get(key) ?? undefined)
  if (typeof value !== 'string')
    return interpolatedCache[key] = value;

  const matches = value.matchAll(/\{\{ ?([^ ]+) ?\}\}/gi)
  for (const [all, varName] of matches) {
    value = value.replace(all, process.env[varName] ?? '')
  }

  return interpolatedCache[key] = value
}