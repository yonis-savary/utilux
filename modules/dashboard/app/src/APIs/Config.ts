import { ipcRenderer } from "electron";

export const ConfigAPI = {
  get: (key: string) => ipcRenderer.invoke('config:get', key),
  set: (key: string, value: any) => ipcRenderer.invoke('config:set', key, value),
  delete: (key: string) => ipcRenderer.invoke('config:delete', key),
  getAll: () => ipcRenderer.invoke('config:getAll')
}