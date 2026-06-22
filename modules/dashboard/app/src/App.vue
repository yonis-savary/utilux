<template>
    <div class="app-container">
        <background/>
        <div class="app-body">
            <n-config-provider :theme="darkTheme">
                <n-message-provider>
                <n-space vertical>
                    <n-tabs
                    key="appNav"
                    type="line"
                    animated
                    placement="left"
                    >
                        <n-tab-pane v-for="item in items" :name="item.name">
                            <template #tab>
                                <n-icon size="30" :component="item.icon"/>
                            </template>
                            <div class="component-holder">
                                <component key="mainComponent" :is="item.component"/>
                            </div>
                        </n-tab-pane>
                    </n-tabs>
                </n-space>
        
                </n-message-provider>
            </n-config-provider>
        </div>
    </div>
</template>

<style>
.app-container {
    position: relative;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
}

.app-container > * {
    position: absolute;
    top: 0;
    left: 0;

}

.app-body {
    z-index: 2;
    background: rgba(0,0,0, 0.7);
}
</style>

<script setup lang="ts">
import { darkTheme } from 'naive-ui'
import { ref } from 'vue';
import {BrandGitlab, ChartCandle, CheckupList, Folder, Home, List, Notebook} from '@vicons/tabler'
import HomeComponent from './Components/Home.vue';
import Issues from './Components/Issues.vue';
import Subjects from './Components/Subjects.vue';
import Settings from './Components/Settings.vue';
import Notes from './Components/Notes.vue';
import GitlabMergeRequests from './Components/GitlabMergeRequests.vue';
import Background from './Components/Background.vue';

const items = ref([
    {
        name: 'home',
        icon: Home,
        component: HomeComponent
    },
    {
        name: 'issues',
        icon: CheckupList,
        component: Issues
    },
    {
        name: 'gitlab',
        icon: BrandGitlab,
        component: GitlabMergeRequests
    },
    {
        name: 'subjects',
        icon: Folder,
        component: Subjects
    },
    {
        name: 'Notes',
        icon: Notebook,
        component: Notes
    },
    {
        name: 'settings',
        icon: ChartCandle,
        component: Settings
    }
])
</script>