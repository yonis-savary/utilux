import { ipcRenderer } from "electron";
import { DiskUsage, DockerStats, GxtRepository, ClaudeUsage } from "../Types/System";

export const SystemAPI = {
  openExternal: (link: string) => ipcRenderer.invoke('system:open-external', link),
  getDisksUsage: (): Promise<DiskUsage[]> => ipcRenderer.invoke('system:disks-usage'),
  getDockerStats: (): Promise<DockerStats | null> => ipcRenderer.invoke('system:docker-stats'),
  getGxtRepositories: (): Promise<GxtRepository[]> => ipcRenderer.invoke('system:gxt-repos'),
  openPath: (path: string): Promise<void> => ipcRenderer.invoke('system:open-path', path),
  openVSCode: (path: string): Promise<void> => ipcRenderer.invoke('system:open-vscode', path),
  getClaudeUsage: (): Promise<ClaudeUsage | null> => ipcRenderer.invoke('system:claude-usage'),
}