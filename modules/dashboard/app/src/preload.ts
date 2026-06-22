// See the Electron documentation for details on how to use preload scripts:
// https://www.electronjs.org/docs/latest/tutorial/process-model#preload-scripts
import { contextBridge } from "electron";
import { ConfigAPI } from "./APIs/Config";
import { JiraAPI } from "./APIs/Jira";
import { SystemAPI } from "./APIs/System";
import { GitlabMergeRequestsAPI } from "./APIs/GitlabMergeRequest";
import { SubjectAPI } from "./APIs/Subject";

contextBridge.exposeInMainWorld('electronAPI', {
    config: ConfigAPI,
    jira: JiraAPI,
    system: SystemAPI,
    gitlabMergeRequests: GitlabMergeRequestsAPI,
    subjects: SubjectAPI
})