import { defineStore } from "pinia";
import { Issue } from "../Types/Issue";
import { cachedRef } from "../Helpers/CachedRef";

const jira = window.electronAPI.jira;

export const useIssuesStore = defineStore('jira-issues', () => {

    const issues = cachedRef<Issue[]>('jiraissues')

    const refresh = async () => {
        issues.value = await jira.activeIssues()
    }

    if (!issues.value) 
        refresh();

    return {
        issues,
        refresh
    }
})


