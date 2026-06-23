import { defineStore } from "pinia";
import { MergeRequestsResult } from "../Types/GitlabMergeRequests";
import { computed } from "vue";
import { useRefreshedRef } from "../Helpers/useRefreshedRef";

const api = window.electronAPI.gitlabMergeRequests;

export const useGitlabMergeRequestsStore = defineStore('gitlab-mr', () => {

    const data = useRefreshedRef<MergeRequestsResult>(
        () => api.activeMergeRequests(),
        { immediate: true, cached: true, cacheKey: 'gitlabmergerequests', interval: 3_600_000 }
    );

    const issuesKeys = computed(() => data.value
        ? data.value.mergeRequests.map(mr => mr.title.replace(/.+, /, ''))
        : []
    );

    const mergeRequestsForIssue = (key: string) => computed(() => data.value
        ? data.value.mergeRequests.filter(mr => mr.title.endsWith(key))
        : []
    )

    return {
        data,
        refresh: data.refresh,
        mergeRequestsForIssue,
        issuesKeys
    }
})
