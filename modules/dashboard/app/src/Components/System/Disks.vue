<template>
    <div class="flex flex-col gap-3">
        <div class="flex flex-row items-center gap-3">
            <n-icon size="30">
                <Server/>
            </n-icon>
            <h1 class="text-2xl">Disks</h1>
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
import { Server } from '@vicons/tabler';
import { useRefreshedRef } from '../../Helpers/useRefreshedRef';

const disks = useRefreshedRef(
    () => window.electronAPI.system.getDisksUsage(),
    { immediate: true, cached: true, cacheKey: 'system-disks', interval: 300_000 }
);

const formatBytes = (bytes: number): string => {
    if (bytes >= 1_000_000_000) return (bytes / 1_000_000_000).toFixed(1) + ' Gb';
    if (bytes >= 1_000_000) return (bytes / 1_000_000).toFixed(1) + ' Mb';
    return (bytes / 1_000).toFixed(1) + ' Kb';
};
</script>
