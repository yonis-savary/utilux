import { ipcMain } from "electron";
import { join } from "path";
import { mkdirSync, readFileSync, writeFileSync, existsSync } from "fs";

const BASE_DIR = join(process.env.HOME || '', '.config/utilux');
const CONFIG_FILE = join(BASE_DIR, 'dashboard-config.json');

const interpolatedCache: Record<string, any> = {};

const readConfig = (): Record<string, any> => {
  if (!existsSync(CONFIG_FILE)) return {};
  try {
    return JSON.parse(readFileSync(CONFIG_FILE, 'utf-8'));
  } catch {
    return {};
  }
};

const writeConfig = (data: Record<string, any>) => {
  mkdirSync(BASE_DIR, { recursive: true });
  writeFileSync(CONFIG_FILE, JSON.stringify(data, null, 2));
};

export const registerConfigHandlers = () => {
  ipcMain.handle('config:get', (_, key: string) => {
    delete interpolatedCache[key]
    return readConfig()[key]
  })
  ipcMain.handle('config:set', (_, key: string, value: any) => {
    const data = readConfig();
    data[key] = value;
    writeConfig(data);
  })
  ipcMain.handle('config:delete', (_, key: string) => {
    const data = readConfig();
    delete data[key];
    writeConfig(data);
    delete interpolatedCache[key]
  })
  ipcMain.handle('config:getAll', () => readConfig())
}


export const getConfigValue = (key: string) => {
  if (key in interpolatedCache)
    return interpolatedCache[key]

  let value = readConfig()[key] ?? undefined
  if (typeof value !== 'string')
    return interpolatedCache[key] = value;

  const matches = value.matchAll(/\{\{ ?([^ ]+) ?\}\}/gi)
  for (const [all, varName] of matches) {
    value = value.replace(all, process.env[varName] ?? '')
  }

  return interpolatedCache[key] = value
}
