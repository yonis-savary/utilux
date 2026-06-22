import { ipcRenderer } from "electron";

export const GitlabMergeRequestsAPI = {
  isConfigured: (key: string) => ipcRenderer.invoke('gitlab-merge-request:is-configured', key),
  activeMergeRequests: () => ipcRenderer.invoke('gitlab-merge-request:active-merge-requests'),
}