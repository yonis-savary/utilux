<template>
    <div v-if="!isConfigured" class="flex flex-col items-center justify-center m-auto pb-20">
        <h1 class="text-2xl">Ouch!</h1>
        <p>Something seems wrong, your Gitlab connection is not configured</p>
        <p>To configure it, look for "Gitlab connection" in the settings</p>
        <n-button @click="reloadIsConfigured">
            Retry
        </n-button>
    </div>
    <div v-else>

        <div class="flex flex-col gap-3">
            <div class="flex flex-row justify-between items-center">
                <h1 class="text-2xl">Open Gitlab Merge Requests ({{ mergeRequestStore.data?.mergeRequests.length ?? 0 }})</h1>
                <n-button @click="mergeRequestStore.refresh">
                    <n-icon><Refresh/></n-icon>
                </n-button>
            </div>
            <MergeRequest
                v-for="mergeRequest in mergeRequestStore.data?.mergeRequests"
                :merge-request="mergeRequest"
            />
        </div>


    </div>
</template>

<script setup lang="ts">
import { useGitlabMergeRequestsStore } from '../Stores/GitlabMergeRequestsStore';
import { onMounted, ref } from 'vue';
import { Refresh } from '@vicons/tabler';
import MergeRequest from './MergeRequest/MergeRequest.vue';


const isConfigured = ref(false);
const reloadIsConfigured = async () => {
    isConfigured.value = await window.electronAPI.gitlabMergeRequests.isConfigured()
};
onMounted(reloadIsConfigured)


const mergeRequestStore = useGitlabMergeRequestsStore();

</script>