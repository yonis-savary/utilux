import { app, BrowserWindow, protocol } from 'electron';
import path from 'node:path';
import fs from 'node:fs';
import started from 'electron-squirrel-startup';
import { registerConfigHandlers } from './APIs/ConfigHandlers';
import { registerJiraHandlers } from './APIs/JiraHandlers';
import { registerSystemHandlers } from './APIs/SystemHandlers';
import { registerGitlabMergeRequestHandlers } from './APIs/GitlabMergeRequestHandlers';
import { registerSubjectHandlers } from './APIs/SubjectHandler';

protocol.registerSchemesAsPrivileged([
  { scheme: 'local-file', privileges: { standard: true, secure: true, supportFetchAPI: true, stream: true } }
]);

// Handle creating/removing shortcuts on Windows when installing/uninstalling.
if (started) {
  app.quit();
}

const createWindow = () => {
  // Create the browser window.
  const mainWindow = new BrowserWindow({
    width: 1280,
    height: 720,
    icon: app.isPackaged
      ? path.join(process.resourcesPath, 'assets/img/utilux-512.png')
      : path.join(__dirname, '../../assets/img/utilux-512.png'),
    webPreferences: {
      preload: path.join(__dirname, 'preload.js'),
    },
  });

  // and load the index.html of the app.
  if (MAIN_WINDOW_VITE_DEV_SERVER_URL) {
    mainWindow.loadURL(MAIN_WINDOW_VITE_DEV_SERVER_URL);
  } else {
    mainWindow.loadFile(
      path.join(__dirname, `../renderer/${MAIN_WINDOW_VITE_NAME}/index.html`),
    );
  }

  mainWindow.menuBarVisible = false

  // Open the DevTools on on npm commands.
  if (!app.isPackaged)
    mainWindow.webContents.openDevTools();

  // Allow debugging
  mainWindow.webContents.on('before-input-event', (event, input) => {
    if (input.key === 'F12') mainWindow.webContents.toggleDevTools();
  });
};

// This method will be called when Electron has finished
// initialization and is ready to create browser windows.
// Some APIs can only be used after this event occurs.
app.on('ready', () => {
  protocol.handle('local-file', (request) => {
    const filePath = decodeURIComponent(request.url.slice(('local-file://'.length)-1));
    try {
      const data = fs.readFileSync(filePath);
      const ext = path.extname(filePath).slice(1).toLowerCase();
      const mime: Record<string, string> = { jpg: 'image/jpeg', jpeg: 'image/jpeg', png: 'image/png', gif: 'image/gif', webp: 'image/webp' };
      return new Response(data, { headers: { 'content-type': mime[ext] ?? 'application/octet-stream' } });
    } catch(err) {
      console.error(err)
      return new Response('Not found', { status: 404 });
    }
  });

  registerConfigHandlers();
  registerJiraHandlers();
  registerSystemHandlers();
  registerGitlabMergeRequestHandlers();
  registerSubjectHandlers();
  
  createWindow();
});

// Quit when all windows are closed, except on macOS. There, it's common
// for applications and their menu bar to stay active until the user quits
// explicitly with Cmd + Q.
app.on('window-all-closed', () => {
  if (process.platform !== 'darwin') {
    app.quit();
  }
});

app.on('activate', () => {
  // On OS X it's common to re-create a window in the app when the
  // dock icon is clicked and there are no other windows open.
  if (BrowserWindow.getAllWindows().length === 0) {
    createWindow();
  }
});

// In this file you can include the rest of your app's specific main process
// code. You can also put them in separate files and import them here.