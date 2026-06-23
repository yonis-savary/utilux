<template>
    <div class="flex flex-col gap-3">
        <div class="flex flex-row justify-between items-center">
            <div class="flex flex-row items-center gap-3">
                <n-icon size="30">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" class="w-full" fill="currentColor"><path d="m19.6 66.5 19.7-11 .3-1-.3-.5h-1l-3.3-.2-11.2-.3L14 53l-9.5-.5-2.4-.5L0 49l.2-1.5 2-1.3 2.9.2 6.3.5 9.5.6 6.9.4L38 49.1h1.6l.2-.7-.5-.4-.4-.4L29 41l-10.6-7-5.6-4.1-3-2-1.5-2-.6-4.2 2.7-3 3.7.3.9.2 3.7 2.9 8 6.1L37 36l1.5 1.2.6-.4.1-.3-.7-1.1L33 25l-6-10.4-2.7-4.3-.7-2.6c-.3-1-.4-2-.4-3l3-4.2L28 0l4.2.6L33.8 2l2.6 6 4.1 9.3L47 29.9l2 3.8 1 3.4.3 1h.7v-.5l.5-7.2 1-8.7 1-11.2.3-3.2 1.6-3.8 3-2L61 2.6l2 2.9-.3 1.8-1.1 7.7L59 27.1l-1.5 8.2h.9l1-1.1 4.1-5.4 6.9-8.6 3-3.5L77 13l2.3-1.8h4.3l3.1 4.7-1.4 4.9-4.4 5.6-3.7 4.7-5.3 7.1-3.2 5.7.3.4h.7l12-2.6 6.4-1.1 7.6-1.3 3.5 1.6.4 1.6-1.4 3.4-8.2 2-9.6 2-14.3 3.3-.2.1.2.3 6.4.6 2.8.2h6.8l12.6 1 3.3 2 1.9 2.7-.3 2-5.1 2.6-6.8-1.6-16-3.8-5.4-1.3h-.8v.4l4.6 4.5 8.3 7.5L89 80.1l.5 2.4-1.3 2-1.4-.2-9.2-7-3.6-3-8-6.8h-.5v.7l1.8 2.7 9.8 14.7.5 4.5-.7 1.4-2.6 1-2.7-.6-5.8-8-6-9-4.7-8.2-.5.4-2.9 30.2-1.3 1.5-3 1.2-2.5-2-1.4-3 1.4-6.2 1.6-8 1.3-6.4 1.2-7.9.7-2.6v-.2H49L43 72l-9 12.3-7.2 7.6-1.7.7-3-1.5.3-2.8L24 86l10-12.8 6-7.9 4-4.6-.1-.5h-.3L17.2 77.4l-4.7.6-2-2 .2-3 1-1 8-5.5Z"/></svg>
                </n-icon>
                <h1 class="text-2xl">Claude</h1>
            </div>
            <n-button @click="refresh">
                <n-icon><Refresh/></n-icon>
            </n-button>
        </div>

        <div v-if="usage" class="flex flex-col gap-3">
            <n-card size="small">
                <div class="flex flex-row justify-between mb-1">
                    <span class="font-semibold">Session</span>
                    <span class="text-xs text-gray-500">resets {{ usage.session.resetsAt }}</span>
                </div>
                <n-progress
                    type="line"
                    :percentage="usage.session.percent"
                    :color="usage.session.percent > 90 ? '#e74c3c' : usage.session.percent > 70 ? '#f39c12' : '#5865f2'"
                    :show-indicator="false"
                />
                <div class="text-sm text-gray-400 mt-1">{{ usage.session.percent }}% used</div>
            </n-card>

            <n-card size="small">
                <div class="flex flex-row justify-between mb-1">
                    <span class="font-semibold">Week</span>
                    <span class="text-xs text-gray-500">resets {{ usage.week.resetsAt }}</span>
                </div>
                <n-progress
                    type="line"
                    :percentage="usage.week.percent"
                    :color="usage.week.percent > 90 ? '#e74c3c' : usage.week.percent > 70 ? '#f39c12' : '#5865f2'"
                    :show-indicator="false"
                />
                <div class="text-sm text-gray-400 mt-1">{{ usage.week.percent }}% used</div>
            </n-card>

            <div class="flex flex-row gap-3">
                <n-card size="small" class="flex-1 text-center">
                    <div class="text-2xl font-bold">{{ usage.last24h.requests }}</div>
                    <div class="text-xs text-gray-400 mt-1">requests / 24h</div>
                    <div class="text-xs text-gray-500">{{ usage.last24h.sessions }} sessions</div>
                </n-card>
                <n-card size="small" class="flex-1 text-center">
                    <div class="text-2xl font-bold">{{ usage.last7d.requests }}</div>
                    <div class="text-xs text-gray-400 mt-1">requests / 7j</div>
                    <div class="text-xs text-gray-500">{{ usage.last7d.sessions }} sessions</div>
                </n-card>
            </div>
        </div>

        <n-empty v-else-if="usage === null" description="Claude CLI indisponible" class="mt-4"/>
    </div>
</template>

<script setup lang="ts">
import { Refresh } from '@vicons/tabler';
import { useRefreshedRef } from '../../Helpers/useRefreshedRef';

const usage = useRefreshedRef(
    () => window.electronAPI.system.getClaudeUsage(),
    { immediate: true, cached: true, cacheKey: 'system-claude', interval: 300_000 }
);
const refresh = usage.refresh;
</script>
