<template>
    <div class="flex flex-col gap-1">

        <div class="flex flex-col gap-0">
            <n-card size="small" :class="['jira-border', 'jira-bg', issue.fields.status.statusCategory.colorName]">
            <div class="flex flex-row items-center gap-3 cursor-pointer">
                <n-icon size="25" @click="openIssue"  class="cursor-pointer">
                    <img v-if="issue.fields.issuetype?.iconUrl" width="24" :src="issue.fields.issuetype?.iconUrl">
                    <external-link v-else/>
                </n-icon>
                <div class="flex flex-col">
                    <h2><b>{{ issue.key }}</b>: {{ issue.fields.summary }}</h2>
                </div>

                <div :class="['ml-auto', 'jira-fg', issue.fields.status.statusCategory.colorName]">
                    {{ issue.fields.status.name }}
                </div>
            </div>
            </n-card>
            <div v-for="mergeRequest in mergeRequests" class="flex flex-row items-center gap-3 pl-3">
                <n-icon size="30">
                    <GitPullRequest/>
                </n-icon>
                <MergeRequest class="compact" :merge-request="mergeRequest" />
            </div>
        </div>

        <small v-if="childrens.length" @click="expandedChildrens = !expandedChildrens" class="ml-5 underline cursor-pointer">
        + {{ childrens.length }} children issues
        </small>

        <div v-if="childrens.length && expandedChildrens" class="flex flex-col gap-3 pl-10">
            <Issue v-for="children in childrens" :issue="children" :issues="props.issues" />
        </div>
    </div>

</template>


<script setup lang="ts">
import { ExternalLink, GitPullRequest, Minus, Plus } from '@vicons/tabler';
import { Issue as IssueType } from '../../Types/Issue';
import { computed, ref } from 'vue';
import { useGitlabMergeRequestsStore } from '../../Stores/GitlabMergeRequestsStore';
import MergeRequest from '../MergeRequest/MergeRequest.vue';

const gitlabMergeRequestStore = useGitlabMergeRequestsStore();

const props = defineProps<{
    issue: IssueType,
    issues: IssueType[]
}>()

const expandedChildrens = ref(true);

const childrens = computed(() => props.issues.filter(issue => issue.fields.parent?.key === props.issue.key))

const openIssue = () => {
    const link = props.issue.self.replace(/\/rest.+/, '/browse/' + props.issue.key)
    window.electronAPI.system.openExternal(link)
}

const mergeRequests = gitlabMergeRequestStore.mergeRequestsForIssue(props.issue.key);

</script>