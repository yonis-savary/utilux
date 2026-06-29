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
                                <n-divider v-if="item.name === 'divider'"/>
                                <n-icon v-else size="30" :component="item.icon"/>
                            </template>
                            <div class="component-holder">
                                <component v-if="item.component" key="mainComponent" :is="item.component"/>
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

[data-name="divider" ] {
    height: 1em;
    pointer-events: none;
}

</style>

<script setup lang="ts">
import { darkTheme } from 'naive-ui'
import { ref } from 'vue';
import {BrandGitlab, ChartCandle, CheckupList, Cpu, Dashboard, DevicesPc, Folder, Gauge, Home, List, Note, Notebook} from '@vicons/tabler'
import HomeComponent from './Components/Home.vue';
import Issues from './Components/Issues.vue';
import Subjects from './Components/Subjects.vue';
import Settings from './Components/Settings.vue';
import Notes from './Components/Notes.vue';
import GitlabMergeRequests from './Components/GitlabMergeRequests.vue';
import Background from './Components/Background.vue';
import SystemHome from './Components/System/Home.vue';
import Jira from './Components/Icons/Jira.vue';

const items = ref([
    {
        name: 'System',
        icon: DevicesPc,
        component: SystemHome
    },
    {
        name: 'divider',
    },
    {
        name: 'work-home',
        icon: Dashboard,
        component: HomeComponent
    },
    {
        name: 'issues',
        icon: Jira,
        component: Issues
    },
    {
        name: 'gitlab',
        icon: BrandGitlab,
        component: GitlabMergeRequests
    },
    {
        name: 'subjects',
        icon: CheckupList,
        component: Subjects
    },
    {
        name: 'settings',
        icon: ChartCandle,
        component: Settings
    }
])
</script>