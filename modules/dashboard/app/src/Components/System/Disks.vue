<template>
    <div class="flex flex-col gap-3">
        <div class="flex flex-row justify-between items-center">
            <div class="flex flex-row items-center gap-3">
                <n-icon size="30"><Server/></n-icon>
                <h1 class="text-2xl">Disks</h1>
            </div>
            <n-button @click="refresh">
                <n-icon><Refresh/></n-icon>
            </n-button>
        </div>
    
        <div v-if="disks?.length" class="flex flex-col gap-3">
            <n-card v-for="disk in disks" :key="disk.mountpoint" size="small">
                <div class="flex flex-row justify-between mb-1">
                    <span class="font-semibold">{{ disk.mountpoint }}</span>
                    <span class="text-sm text-gray-400">{{ disk.filesystem }}</span>
                </div>
                <n-progress
                    type="line"
                    :percentage="disk.usedPercent"
                    :color="disk.usedPercent > 90 ? '#e74c3c' : disk.usedPercent > 70 ? '#f39c12' : '#27ae60'"
                    :show-indicator="false"
                />
                <div class="flex flex-row justify-between mt-1 text-sm text-gray-400">
                    <span>{{ formatBytes(disk.used) }} used ({{ disk.usedPercent }}%)</span>
                    <span>{{ formatBytes(disk.free) }} free / {{ formatBytes(disk.total) }}</span>
                </div>
            </n-card>
        </div>
        <n-empty v-else-if="disks !== undefined" description="No disk found (sad)" class="mt-4"/>
    </div>

</template>

<script setup lang="ts">
import { Refresh, Server } from '@vicons/tabler';
import { useRefreshedRef } from '../../Helpers/useRefreshedRef';

const size_kb = 1000;
const size_mb = 1000 * size_kb;
const size_gb = 1000 * size_mb;
const size_tb = 1000 * size_gb;

const disks = useRefreshedRef(
    () => window.electronAPI.system.getDisksUsage(),
    { immediate: true, cached: true, cacheKey: 'system-disks', interval: 300_000 }
);
const refresh = disks.refresh;

const formatBytes = (bytes: number): string => {
    if (bytes >= size_tb) return (bytes / size_tb).toFixed(1) + ' TB';
    if (bytes >= size_gb) return (bytes / size_gb).toFixed(1) + ' GB';
    if (bytes >= size_mb) return (bytes / size_mb).toFixed(1) + ' MB';
    return (bytes / size_kb).toFixed(1) + ' KB';
};
</script>




