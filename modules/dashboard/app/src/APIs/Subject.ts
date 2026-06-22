import { ipcRenderer } from "electron";
import { Subject } from "../Types/Subject";

export const SubjectAPI = {
  save: (subject: Subject) => ipcRenderer.invoke('subjects:save', subject),
  saveAll: (subjects: Subject[]) => ipcRenderer.invoke('subjects:save-all', subjects),
  getAll: () => ipcRenderer.invoke('subjects:get-all'),
}