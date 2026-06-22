import { ipcMain } from "electron";
import { getConfigValue } from "./ConfigHandlers";

const isGitlabMergeRequestConfigured = () => {
  return !! (
    getConfigValue('gitlab.origin') &&
    getConfigValue('gitlab.token')
  );
}

const gitlabFetch = async (path: string, body: object = {}) => {
  const origin = getConfigValue('gitlab.origin');
  const token  = getConfigValue('gitlab.token');

  const params = new URLSearchParams();
  for (const [key, value] of Object.entries(body)) {
    params.append(key, value);
  }

  console.log(`${origin}${path}?${params.toString()}`)

  const res = await fetch(`${origin}${path}?${params.toString()}`, {
    headers: {
      'PRIVATE-TOKEN': token,
      'Accept': 'application/json',
    },
  });

  if (!res.ok) throw new Error(`Gitlab API error: ${res.status}`);
  return res.json();
};

const getActiveMergeRequests = async () => {
  const userId = (await gitlabFetch('/user'))['id'];
  const mergeRequests = await gitlabFetch('/merge_requests', {'assignee_id': userId, 'state': 'opened', 'scope': 'all'});

  const pipelines = {};
  const approvals = {};
  if (mergeRequests.length < 10) {
    for (const mr of mergeRequests)
    {
        const mrIid = mr['iid'];
        pipelines[mrIid] = await gitlabFetch(`/projects/${mr['project_id']}/merge_requests/${mr['iid']}/pipelines`);
        approvals[mrIid] = await gitlabFetch(`/projects/${mr['project_id']}/merge_requests/${mr['iid']}/approvals`);
    }
  }

  return {mergeRequests, pipelines, approvals};
}

export const registerGitlabMergeRequestHandlers = () => {
  ipcMain.handle('gitlab-merge-request:is-configured', () => isGitlabMergeRequestConfigured())
  ipcMain.handle('gitlab-merge-request:active-merge-requests', () => getActiveMergeRequests());
}
