import { defineStore } from "pinia";
import { MergeRequestsResult } from "../Types/GitlabMergeRequests";
import { computed } from "vue";
import { cachedRef } from "../Helpers/CachedRef";

const api = window.electronAPI.gitlabMergeRequests;

export const useGitlabMergeRequestsStore = defineStore('gitlab-mr', () => {

    const data = cachedRef<MergeRequestsResult>('gitlabmergerequests');

    const issuesKeys = computed(() => data.value 
        ? data.value.mergeRequests.map(mr => mr.title.replace(/.+, /, ''))
        : []
    );

    const mergeRequestsForIssue = (key: string) => computed(() => data.value 
        ? data.value.mergeRequests.filter(mr => mr.title.endsWith(key))
        : []
    )

    const refresh = async () => {
        data.value = await api.activeMergeRequests()
    }

    if (!data.value?.mergeRequests)
        refresh();

    return {
        data,
        refresh,
        mergeRequestsForIssue,
        issuesKeys
    }
})


