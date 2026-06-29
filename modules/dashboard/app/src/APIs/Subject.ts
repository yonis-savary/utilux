import { ipcRenderer } from "electron";
import { Subject } from "../Types/Subject";

export const SubjectAPI = {
    save: (subject: Subject) => ipcRenderer.invoke('subjects:save', subject),
    saveAll: (subjects: Subject[]) => ipcRenderer.invoke('subjects:save-all', subjects),
    getAll: (): Promise<Subject[]> => ipcRenderer.invoke('subjects:get-all'),
    delete: (name: string) => ipcRenderer.invoke('subjects:delete', name),
    listAttachments: (name: string): Promise<string[]> => ipcRenderer.invoke('subjects:list-attachments', name),
    openAttachment: (name: string, filename: string) => ipcRenderer.invoke('subjects:open-attachment', name, filename),
    openAttachmentsFolder: (name: string) => ipcRenderer.invoke('subjects:open-attachments-folder', name),
}
