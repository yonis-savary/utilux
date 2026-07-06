<template>
    <n-tabs type="line" animated v-model:value="selectedProject">
        <n-tab-pane
            v-for="[project, items] in projectGroups"
            :key="project"
            :name="project"
            :tab="project"
        >
            <MergeRequestTree :merge-requests="items"/>
        </n-tab-pane>
        <template #suffix>
            <n-button
                v-if="matchedRepo"
                size="small"
                title="Open in VSCode"
                @click="openCode(matchedRepo.path)"
            >
                <template #icon><n-icon><BrandVisualStudio/></n-icon></template>
                Open in VSCode
            </n-button>
        </template>
    </n-tabs>
</template>

<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import { MergeRequest } from '../../Types/GitlabMergeRequests';
import { BrandVisualStudio } from '@vicons/tabler';
import { useRefreshedRef } from '../../Helpers/useRefreshedRef';
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

const selectedProject = ref<string>('')
watch(projectGroups, groups => {
    if (!groups.some(([project]) => project === selectedProject.value)) {
        selectedProject.value = groups[0]?.[0] ?? ''
    }
}, { immediate: true })

const gxtRepos = useRefreshedRef(
    () => window.electronAPI.system.getGxtRepositories(),
    { immediate: true, cached: true, cacheKey: 'system-gxt', interval: 300_000 }
);

const matchedRepo = computed(() => {
    const basename = selectedProject.value.replace(/^.*\//, '')
    return gxtRepos.value?.find(repo => repo.name.toLowerCase() === basename.toLowerCase())
})

const openCode = (path: string) => window.electronAPI.system.openVSCode(path)
</script>
