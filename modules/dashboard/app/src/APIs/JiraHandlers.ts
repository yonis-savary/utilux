import { ipcMain } from "electron";
import { getConfigValue } from "./ConfigHandlers";

const isJiraConfigured = () => {
  return !! (
    getConfigValue('jira.email') &&
    getConfigValue('jira.origin') &&
    getConfigValue('jira.token')
  );
}

const jiraFetch = async (path: string, body: object = {}) => {
  const origin = getConfigValue('jira.origin');
  const email  = getConfigValue('jira.email');
  const token  = getConfigValue('jira.token');
  const credentials = Buffer.from(`${email}:${token}`).toString('base64');

  const params = new URLSearchParams();
  for (const [key, value] of Object.entries(body)) {
    params.append(key, value);
  }

  console.log(`${origin}/${path}?${params.toString()}`)

  const res = await fetch(`${origin}${path}?${params.toString()}`, {
    headers: {
      'Authorization': `Basic ${credentials}`,
      'Accept': 'application/json',
    },
  });

  if (!res.ok) throw new Error(`Jira API error: ${res.status}`);
  return res.json();
};

const getActiveIssues = async () => {
  return (await jiraFetch('/search/jql', {
    'fields': 'status,summary,parent,issuetype', 
    'jql': 'assignee = currentUser() AND status NOT IN (Annule, "Annulé", Clôturé, Done, Finished, "Résolu Automatiquement", Terminé, Success) ORDER BY priority DESC, created DESC'
  }))['issues']
}

export const registerJiraHandlers = () => {
  ipcMain.handle('jira:is-configured', () => isJiraConfigured())
  ipcMain.handle('jira:active-issues', () => getActiveIssues());
}
