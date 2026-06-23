<template>
    <div class="flex flex-col gap-3">
        <div class="flex flex-row items-center gap-3">
            <n-icon size="30">
                <BrandGit/>
            </n-icon>
            <h1 class="text-2xl">Gxt</h1>
        </div>

        <div v-if="repos?.length" class="grid grid-cols-2 gap-2">
            <n-card v-for="repo in repos" :key="repo.path" size="small" class="repo-card">
                <template #default>
                    <span class="font-semibold truncate">{{ repo.relativeDir }}</span>
                </template>
                <template #action>
                    <n-button size="small" quaternary @click="openFolder(repo.path)" title="Ouvrir le dossier">
                        <template #icon><n-icon><Folder/></n-icon></template>
                    </n-button>
                    <n-button size="small" quaternary @click="openCode(repo.path)" title="Ouvrir dans VSCode">
                        <template #icon><n-icon><BrandVisualStudio/></n-icon></template>
                    </n-button>
                </template>
            </n-card>
        </div>
        <n-empty v-else-if="repos !== undefined" description="Aucun repository gxt trouvé" class="mt-4"/>
    </div>
</template>

<style>
.repo-card {
    min-width: max-content;
}

.repo-card .n-card__action {
    display: flex;
    flex-direction: row;
    padding: 0;
}

.repo-card .n-card__action > * {
    flex: 1
}

</style>

<script setup lang="ts">
import { BrandGit, Folder, BrandVisualStudio } from '@vicons/tabler';
import { useRefreshedRef } from '../../Helpers/useRefreshedRef';

const repos = useRefreshedRef(
    () => window.electronAPI.system.getGxtRepositories(),
    { immediate: true, cached: true, cacheKey: 'system-gxt', interval: 300_000 }
);

const openFolder = (path: string) => window.electronAPI.system.openPath(path);
const openCode = (path: string) => window.electronAPI.system.openVSCode(path);
</script>
