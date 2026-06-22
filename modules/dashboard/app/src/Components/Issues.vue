<template>
    <div v-if="!isConfigured" class="flex flex-col items-center justify-center m-auto pb-20">
        <h1 class="text-2xl">Ouch!</h1>
        <p>Something seems wrong, your Jira connection is not configured</p>
        <p>To configure it, look for "Jira connection" in the settings</p>
        <n-button @click="reloadIsConfigured">
            Retry
        </n-button>
    </div>
    <div v-else>

        <div class="flex flex-col gap-3">
            <div class="flex flex-row justify-between items-center">
                <h1 class="text-2xl">Current Jira Issues ({{ issuesStore.issues?.length ?? 0 }})</h1>
                <n-button @click="issuesStore.refresh">
                    <n-icon><Refresh/></n-icon>
                </n-button>
            </div>

            <Issue v-for="root in rootIssues" :issue="root" :issues="issuesStore.issues ?? []" />
        </div>

    </div>
</template>

<script setup lang="ts">
import { Refresh } from '@vicons/tabler';
import { useIssuesStore } from '../../src/Stores/IssuesStore';
import { computed, onMounted, ref } from 'vue';
import Issue from './Issue/Issue.vue';


const isConfigured = ref(false);
const reloadIsConfigured = async () => {
    isConfigured.value = await window.electronAPI.jira.isConfigured()
};
onMounted(reloadIsConfigured)

const issuesStore = useIssuesStore();

const issuesKeys = computed(() => 
    issuesStore.issues?.map(issue => issue.key) ?? []
)

const rootIssues = computed(() => issuesStore.issues?.filter(x => !(
    x.fields.parent?.key && issuesKeys.value.includes(x.fields.parent?.key)
)) ?? [])

</script>