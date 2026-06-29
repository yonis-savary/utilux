import { ipcMain, shell } from "electron";
import { join } from "path";
import { mkdirSync, readdirSync, readFileSync, writeFileSync, existsSync, rmSync } from "fs";
import { Subject } from "../Types/Subject";

const BASE_DIR = join(process.env.HOME || '', '.local/share/utilux/dashboard/subjects');

const slugify = (name: string): string =>
    name.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');

const subjectDir = (name: string) => join(BASE_DIR, slugify(name));
const subjectFile = (name: string) => join(subjectDir(name), 'subject.json');
const attachmentsDir = (name: string) => join(subjectDir(name), 'attachments');

const ensureSubjectDirs = (name: string) => {
    mkdirSync(subjectDir(name), { recursive: true });
    mkdirSync(attachmentsDir(name), { recursive: true });
};

const readSubjectFromDir = (dirPath: string): Subject | null => {
    const file = join(dirPath, 'subject.json');
    if (!existsSync(file)) return null;
    try {
        return JSON.parse(readFileSync(file, 'utf-8'));
    } catch {
        return null;
    }
};

export const registerSubjectHandlers = () => {
    mkdirSync(BASE_DIR, { recursive: true });

    ipcMain.handle('subjects:get-all', (): Subject[] => {
        return readdirSync(BASE_DIR, { withFileTypes: true })
            .filter(d => d.isDirectory())
            .map(d => readSubjectFromDir(join(BASE_DIR, d.name)))
            .filter((s): s is Subject => s !== null);
    });

    ipcMain.handle('subjects:save', (_, subject: Subject) => {
        ensureSubjectDirs(subject.name);
        writeFileSync(subjectFile(subject.name), JSON.stringify(subject, null, 2));
    });

    ipcMain.handle('subjects:save-all', (_, subjects: Subject[]) => {
        for (const subject of subjects) {
            ensureSubjectDirs(subject.name);
            writeFileSync(subjectFile(subject.name), JSON.stringify(subject, null, 2));
        }
    });

    ipcMain.handle('subjects:delete', (_, name: string) => {
        const dir = subjectDir(name);
        if (existsSync(dir)) rmSync(dir, { recursive: true });
    });

    ipcMain.handle('subjects:list-attachments', (_, name: string): string[] => {
        const dir = attachmentsDir(name);
        if (!existsSync(dir)) return [];
        return readdirSync(dir).filter(f => !f.startsWith('.'));
    });

    ipcMain.handle('subjects:open-attachment', (_, name: string, filename: string) => {
        shell.openPath(join(attachmentsDir(name), filename));
    });

    ipcMain.handle('subjects:open-attachments-folder', (_, name: string) => {
        const dir = attachmentsDir(name);
        mkdirSync(dir, { recursive: true });
        shell.openPath(dir);
    });
};
