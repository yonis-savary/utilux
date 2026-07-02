<template>
    <div class="mr-tree-node-content">
        <div class="mr-tree-branch-marker">
            <n-icon size="16">
                <GitBranch/>
            </n-icon>
        </div>
        <small class="mr-tree-branch-label" :title="branchName">{{ branchName }}</small>
    </div>
</template>

<style>

.mr-tree-node-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
}

.mr-tree-branch-marker {
    width: 2.75em;
    height: 2.75em;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #888888;
    border: 2px dashed currentColor;
}

.mr-tree-branch-label {
    max-width: 100%;
    white-space: wrap;
    opacity: 0.75;
    text-align: center;
}
</style>

<script setup lang="ts">
import { GitBranch } from '@vicons/tabler';

defineProps<{
    branchName: string
}>()
</script>
