import { ipcRenderer } from "electron";

export const SystemAPI = {
  openExternal: (link: string) => ipcRenderer.invoke('system:open-external', link),
}