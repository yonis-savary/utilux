<template>
    <h1 class="text-3xl font-bold">Work Dashboard</h1>

    <div class="flex flex-row gap-3 items-center">

        <n-card>
            <div class="text-3xl">{{ gitlabMergeRequestsStore.data?.mergeRequests.length }}</div>
            Open Merge-Requests
        </n-card>
        <n-card>
            <div class="text-3xl">{{ issuesStore.issues?.length ?? 0 }}</div>
            Open Jira issues
        </n-card>

    </div>

    <n-divider/>
    <h2 class="text-2xl font-bold">Active Issues</h2>
    <Issue v-for="issue in activeIssues" :issue="issue" :issues="issuesStore.issues ?? []"/>
</template>

<script setup lang="ts">
import { useGitlabMergeRequestsStore } from '../Stores/GitlabMergeRequestsStore';
import { useIssuesStore } from '../Stores/IssuesStore';
import { computed } from 'vue';
import Issue from './Issue/Issue.vue';


const gitlabMergeRequestsStore = useGitlabMergeRequestsStore();
const issuesStore = useIssuesStore();

const activeIssues = computed(() => issuesStore.issues?.filter(x => gitlabMergeRequestsStore.issuesKeys.includes(x.key)) ?? [])

</script>