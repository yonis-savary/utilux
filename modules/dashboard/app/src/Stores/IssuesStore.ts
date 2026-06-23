import { defineStore } from "pinia";
import { useRefreshedRef } from "../Helpers/useRefreshedRef";
import { Issue } from "../Types/Issue";

const jira = window.electronAPI.jira;

export const useIssuesStore = defineStore('jira-issues', () => {

    const issues = useRefreshedRef<Issue[]>(
        () => jira.activeIssues(),
        { immediate: true, cached: true, cacheKey: 'jiraissues', interval: 3_600_000 }
    );

    return {
        issues,
        refresh: issues.refresh
    }
})
