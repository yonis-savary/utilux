import { ipcMain, shell } from "electron";
import { execSync, execFileSync } from "child_process";
import { join } from "path";
import { DiskUsage, DockerStats, GxtRepository, ClaudeUsage } from "../Types/System";

export const registerSystemHandlers = () => {
  ipcMain.handle('system:open-external', (_, link: string) => {
    shell.openExternal(link)
  })

  ipcMain.handle('system:disks-usage', (): DiskUsage[] => {
    const output = execSync('df -Pk').toString();
    return output
      .trim()
      .split('\n')
      .slice(1)
      .map(line => {
        const [filesystem, total, used, free, , mountpoint] = line.trim().split(/\s+/);
        const totalBytes = parseInt(total) * 1024;
        const usedBytes = parseInt(used) * 1024;
        return {
          filesystem,
          mountpoint,
          total: totalBytes,
          used: usedBytes,
          free: parseInt(free) * 1024,
          usedPercent: totalBytes > 0 ? Math.round((usedBytes / totalBytes) * 100) : 0,
        };
      })
      .filter(disk => disk.filesystem.startsWith('/dev/'))
      .filter(disk => !disk.mountpoint.startsWith('/boot'))
  })

  ipcMain.handle('system:docker-stats', (): DockerStats | null => {
    try {
      const runningContainers = parseInt(
        execSync('docker ps -q 2>/dev/null | wc -l', { shell: '/bin/sh', encoding: 'utf8' }).trim()
      );
      const totalVolumes = parseInt(
        execSync('docker volume ls -q 2>/dev/null | wc -l', { shell: '/bin/sh', encoding: 'utf8' }).trim()
      );
      return { runningContainers, totalVolumes };
    } catch (err) {
      console.warn("[service docker] Error", err)
      return null;
    }
  });


  ipcMain.handle('system:gxt-repos', (): GxtRepository[] => {
    const home = process.env.HOME || '';
    const baseDir = join(home, '.local/share/utilux/gxt/repositories');
    try {
      const output = execSync(
        `find "${baseDir}" -maxdepth 5 -type d -iname ".git" 2>/dev/null | sed 's/\\/.git//g' | sort`,
        { shell: '/bin/sh', encoding: 'utf8' }
      );
      return output.trim().split('\n').filter(Boolean).map(fullPath => {
        const relative = fullPath.replace(baseDir + '/', '');
        const parts = relative.split('/');
        return {
          name: parts[parts.length - 1],
          path: fullPath,
          relativeDir: parts.length >= 4 
            ? parts.slice(-2).join('/') 
            : fullPath.replace(/.+\//, ''),
        };
      });
    } catch (err) {
      console.warn("[service gxt] Error", err)
      return [];
    }
  });

  ipcMain.handle('system:claude-usage', (): ClaudeUsage | null => {
    try {
      const output = execSync('claude -p /usage', { encoding: 'utf8', timeout: 15000 });

      const sessionMatch = output.match(/Current session:\s*(\d+)%\s*used\s*·\s*resets\s*(.+)/);
      const weekMatch = output.match(/Current week[^:]*:\s*(\d+)%\s*used\s*·\s*resets\s*(.+)/);
      const last24hMatch = output.match(/Last 24h\s*·\s*(\d+)\s*requests\s*·\s*(\d+)\s*sessions/);
      const last7dMatch = output.match(/Last 7d\s*·\s*(\d+)\s*requests\s*·\s*(\d+)\s*sessions/);

      if (!(sessionMatch || weekMatch)) return null;

      return {
        session: sessionMatch
          ? { percent: parseInt(sessionMatch[1]), resetsAt: sessionMatch[2].trim() }
          : { percent: 0, resetsAt: null },
        week: weekMatch
          ? { percent: parseInt(weekMatch[1]), resetsAt: weekMatch[2].trim() }
          : { percent: 0, resetsAt: null },
        last24h: last24hMatch ? { requests: parseInt(last24hMatch[1]), sessions: parseInt(last24hMatch[2]) } : { requests: 0, sessions: 0 },
        last7d: last7dMatch ? { requests: parseInt(last7dMatch[1]), sessions: parseInt(last7dMatch[2]) } : { requests: 0, sessions: 0 },
      };
    } catch (err) {
      console.warn("[service Claude] Error", err)
      return null;
    }
  });

  ipcMain.handle('system:open-path', (_, path: string) => {
    shell.openPath(path);
  });

  ipcMain.handle('system:open-vscode', (_, path: string) => {
    try {
      execFileSync('code', [path]);
    } catch {
      // code not in PATH
    }
  });
}