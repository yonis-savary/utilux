<template>
    <n-card :class="class">
        <div class="flex flex-row items-center gap-3">
            <a @click="openMergeRequest"  class="cursor-pointer">
                <n-icon size="20">
                    <external-link/>
                </n-icon>
            </a>
            <div class="flex flex-col gap-0">
                <small>{{ mergeRequest.references.full.replace(/!.+$/, '') }}</small>
                <b>{{ mergeRequest.title }}</b>
            </div>

            <div class="flex flex-col gap-0 ml-auto items-end">
                <div class="flex flex-row gap-1 items-center">
                    {{ mergeRequest.source_branch }}
                    <n-icon>
                        <ArrowRight/>
                    </n-icon>
                    {{ mergeRequest.target_branch }}
                </div>
                <small class="text-green-600 font-bold" v-if="approversNames.length">Approved by {{ approversNames.join(', ') }}</small>
            </div>
            <n-icon :class="['pipeline', lastPipeline?.status ?? '?']" size="25">
                <CircleCheck v-if="lastPipeline?.status === 'success'"/>
                <Hourglass v-else-if="lastPipeline?.status === 'running'"/>
                <CircleX v-else-if="lastPipeline?.status === 'failed'"/>
            </n-icon>
        </div>
    </n-card>
</template>

<style>

.compact {
    --n-padding-top: 6px !important;
    --n-padding-left: 12px !important;
    --n-padding-right: 6px !important;
    --n-padding-bottom: 6px !important;
}

</style>

<style>


.pipeline.success {
    color: #42c34d;
}
.pipeline.running {
    color: #00a2ff;
}
.pipeline.failed {
    color: #ff2200;
}
</style>

<script setup lang="ts">
import { ArrowRight, Check, CircleCheck, CircleX, ExternalLink, GitPullRequest, Hourglass } from '@vicons/tabler';
import { useGitlabMergeRequestsStore } from '../../Stores/GitlabMergeRequestsStore';
import { Approval, MergeRequest, Pipeline } from '../../Types/GitlabMergeRequests';
import { computed, ref } from 'vue';


const props = defineProps<{
    class?: string
    mergeRequest: MergeRequest
}>()
const openMergeRequest = () => {
    const link = props.mergeRequest.web_url
    window.electronAPI.system.openExternal(link)
}

const mergeRequestStore = useGitlabMergeRequestsStore();

const pipelines = computed<Pipeline[]>(() => 
   (mergeRequestStore.data?.pipelines[props.mergeRequest.iid] ?? [])
   .sort((a,b) => a.updated_at < b.updated_at ? -1:1)
);

const lastPipeline = computed<Pipeline|undefined>(() => pipelines.value[pipelines.value.length-1] ?? undefined)

const lastPipelineApprovals = computed<Approval|undefined>(() => mergeRequestStore.data?.approvals[props.mergeRequest.iid] ?? undefined);

const approversNames = computed(() => 
    lastPipelineApprovals.value
        ? lastPipelineApprovals.value.approved_by.map(approver => approver.user.name)
        : []
)


</script>