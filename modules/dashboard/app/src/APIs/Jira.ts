import { ipcRenderer } from "electron";

export const JiraAPI = {
  isConfigured: (key: string) => ipcRenderer.invoke('jira:is-configured', key),
  activeIssues: () => ipcRenderer.invoke('jira:active-issues'),
}