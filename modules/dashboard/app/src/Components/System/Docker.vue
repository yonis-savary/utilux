<template>
    <div class="flex flex-col gap-3">
        <div class="flex flex-row justify-between items-center">
            <div class="flex flex-row items-center gap-3">
                <n-icon size="30"><BrandDocker/></n-icon>
                <h1 class="text-2xl">Docker</h1>
            </div>
            <n-button @click="refresh">
                <n-icon><Refresh/></n-icon>
            </n-button>
        </div>

        <div v-if="stats" class="flex flex-col gap-2">
            <div class="flex flex-row gap-3">
                <n-card size="small" class="flex-1 text-center">
                    <div class="text-3xl font-bold">{{ stats.runningContainers }}</div>
                    <div class="text-sm text-gray-400 mt-1">containers running</div>
                </n-card>
                <n-card size="small" class="flex-1 text-center">
                    <div class="text-3xl font-bold">{{ stats.totalVolumes }}</div>
                    <div class="text-sm text-gray-400 mt-1">volumes</div>
                </n-card>
            </div>
        </div>
        <n-empty v-else-if="stats === null" description="Docker unavailable" class="mt-2"/>
    </div>
</template>

<script setup lang="ts">
import { BrandDocker, Refresh } from '@vicons/tabler';
import { useRefreshedRef } from '../../Helpers/useRefreshedRef';

const stats = useRefreshedRef(
    () => window.electronAPI.system.getDockerStats(),
    { immediate: true, cached: true, cacheKey: 'system-docker', interval: 300_000 }
);
const refresh = stats.refresh;
</script>
