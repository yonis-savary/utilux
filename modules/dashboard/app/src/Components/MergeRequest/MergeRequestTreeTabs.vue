<template>
    <n-tabs type="line" animated>
        <n-tab-pane
            v-for="[project, items] in projectGroups"
            :key="project"
            :name="project"
            :tab="project"
        >
            <MergeRequestTree :merge-requests="items"/>
        </n-tab-pane>
    </n-tabs>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { MergeRequest } from '../../Types/GitlabMergeRequests';
import MergeRequestTree from './MergeRequestTree.vue';

const props = defineProps<{
    mergeRequests: MergeRequest[]
}>()

const projectName = (mergeRequest: MergeRequest) =>
    mergeRequest.references.full.replace(/!.+$/, '')

const projectGroups = computed<[string, MergeRequest[]][]>(() => {
    const groups = new Map<string, MergeRequest[]>()
    for (const mergeRequest of props.mergeRequests) {
        const key = projectName(mergeRequest)
        if (!groups.has(key)) groups.set(key, [])
        groups.get(key)!.push(mergeRequest)
    }
    return [...groups.entries()]
})
</script>
