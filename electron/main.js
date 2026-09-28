const { app, BrowserWindow, Menu } = require('electron');
const path = require('path');

let mainWindow;

function createWindow() {
  mainWindow = new BrowserWindow({
    width: 1366,
    height: 868,
    minWidth: 1024,
    minHeight: 700,
    title: 'Edificar — Portal Life Church',
    icon: path.join(__dirname, '../public/favicon.png'),
    webPreferences: {
      preload: path.join(__dirname, 'preload.js'),
      nodeIntegration: false,
      contextIsolation: true
    },
    autoHideMenuBar: false
  });

  const defaultDevUrl = 'http://127.0.0.1:8000';
  const prodUrl = 'http://146.235.224.99/edificar';
  const appUrl = process.env.APP_URL || defaultDevUrl;

  mainWindow.webContents.on('did-fail-load', (event, errorCode, errorDescription, validatedURL) => {
    if (validatedURL.includes('127.0.0.1') || validatedURL.includes('localhost')) {
      console.log(`[Electron] Dev server unavailable at ${validatedURL}. Falling back to production: ${prodUrl}`);
      mainWindow.loadURL(prodUrl);
    }
  });

  mainWindow.loadURL(appUrl);

  mainWindow.on('closed', function () {
    mainWindow = null;
  });
}

app.whenReady().then(() => {
  createWindow();

  app.on('activate', function () {
    if (BrowserWindow.getAllWindows().length === 0) createWindow();
  });
});

app.on('window-all-closed', function () {
  if (process.platform !== 'darwin') app.quit();
});
