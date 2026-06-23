export interface DiskUsage {
  filesystem: string;
  mountpoint: string;
  total: number;
  used: number;
  free: number;
  usedPercent: number;
}

export interface DockerStats {
  runningContainers: number;
  totalVolumes: number;
}

export interface GxtRepository {
  name: string;
  path: string;
  relativeDir: string;
}

export interface ClaudeUsage {
  session: { percent: number; resetsAt: string };
  week: { percent: number; resetsAt: string };
  last24h: { requests: number; sessions: number };
  last7d: { requests: number; sessions: number };
}