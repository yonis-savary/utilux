import { ipcMain, shell } from "electron";

export const registerSystemHandlers = () => {
  ipcMain.handle('system:open-external', (_, link: string) => {
    shell.openExternal(link)
  })
}