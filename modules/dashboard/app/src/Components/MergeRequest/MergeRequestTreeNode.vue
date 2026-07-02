<template>
    <div class="mr-tree-node-content">
        <n-popover trigger="hover">
            <template #trigger>
                <div
                    class="mr-tree-sphere"
                    :class="lastPipeline?.status ?? 'none'"
                    @click="openMergeRequest"
                >
                    <n-icon size="16">
                        <Check v-if="lastPipeline?.status === 'success'"/>
                        <Hourglass v-else-if="lastPipeline?.status === 'running'"/>
                        <CircleX v-else-if="lastPipeline?.status === 'failed'"/>
                        <GitMerge v-else/>
                    </n-icon>
                </div>
            </template>
            {{ mergeRequest.references.full }}
        </n-popover>
        <small class="mr-tree-branch-label" :title="mergeRequest.source_branch">{{ mergeRequest.source_branch }}</small>
    </div>
</template>

<style>

.mr-tree-node-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
}

.mr-tree-sphere {
    width: 2.75em;
    height: 2.75em;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    border: 2px solid currentColor;
    background: rgba(255, 255, 255, 0.05);
    transition: transform 0.15s ease;
}

.mr-tree-sphere:hover {
    transform: scale(1.1);
}

.mr-tree-sphere.success {
    color: #42c34d;
}
.mr-tree-sphere.running {
    color: #00a2ff;
}
.mr-tree-sphere.failed {
    color: #ff2200;
}
.mr-tree-sphere.none {
    color: #888888;
}

</style>

<script setup lang="ts">
import { Check, CircleCheck, CircleX, GitMerge, Hourglass } from '@vicons/tabler';
import { useGitlabMergeRequestsStore } from '../../Stores/GitlabMergeRequestsStore';
import { MergeRequest, Pipeline } from '../../Types/GitlabMergeRequests';
import { computed } from 'vue';

const props = defineProps<{
    mergeRequest: MergeRequest
}>()

const openMergeRequest = () => {
    window.electronAPI.system.openExternal(props.mergeRequest.web_url)
}

const mergeRequestStore = useGitlabMergeRequestsStore();

const pipelines = computed<Pipeline[]>(() =>
    (mergeRequestStore.data?.pipelines[props.mergeRequest.iid] ?? [])
    .sort((a, b) => a.updated_at < b.updated_at ? -1 : 1)
);

const lastPipeline = computed<Pipeline|undefined>(() => pipelines.value[pipelines.value.length - 1] ?? undefined)
</script>
