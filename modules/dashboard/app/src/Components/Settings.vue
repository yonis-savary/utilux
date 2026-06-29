<template>
    <div class="text-2xl bold">Configuration</div>
    <p>
        In this configuration panel, you can edit every service constants and behaviors. <br> <b>Everything you type is automatically saved</b>
    </p>

    <n-alert title="Env variables" type="info">
      Note: you can use the {{ envSyntax }} syntax to use environment variables
    </n-alert>

    <n-alert title="Env variables are terminal-only" type="warning">
      Env variables cannot be used by the app if launched through a .desktop file (or any desktop shortcut), if you use them, you have to launch the app through your terminal
    </n-alert>

    <n-divider/>

    <div v-for="element in configurationDescription">

        <div v-if="element.type === 'title'" class="text-xl bold">{{ element.description ?? '<Err: No description>' }}</div>

        <n-divider v-else-if="element.type === 'divider'" />

        <div v-else-if="['input', 'password'].includes(element.type)">
            <small v-if="element.description">{{ element.description }}</small>
            <SettingTextInput 
                :type="element.type === 'password' ? 'password': 'text'"
                v-if="element.name" :name="element.name" :placeholder="element.placeholder" 
            />
            <span v-else>Err: no 'name' attribute given</span>
        </div>
    </div>


</template>

<script setup lang="ts">
import { ref } from 'vue';
import SettingTextInput from './Settings/SettingTextInput.vue';

const envSyntax = `{{ VAR_NAME }}`

type ElementType = 'title'|'input'|'password'|'divider'
type ConfigElement = {
    type: ElementType,
    description?: string,
    name?: string,
    placeholder?: string
}

const configurationDescription = ref<ConfigElement[]>([

    {
        type: 'title',
        description: 'Interface'
    },
    {
        type: 'input',
        name: 'interface.background',
        description: 'Custom background path (url, local path...) ',
        placeholder: "/home/you/Pictures/awesome-wallpaper.png"
    },

    { type: 'divider' },

    {
        type: 'title',
        description: 'Jira Connection'
    },
    {
        type: 'input',
        name: 'jira.origin',
        description: 'API Endpoint, should ends with /rest/api/v3',
        placeholder: "https://some-org.atlassian.net/rest/api/3"
    },
    {
        type: 'input',
        name: 'jira.email',
        description: 'Email used to connect to Jira',
        placeholder: 'john.doe@some-org.com'
    },
    {
        type: 'password',
        name: 'jira.token',
        description: 'Personnal API Token (https://id.atlassian.com/manage-profile/security/api-tokens)',
        placeholder: '...'
    },

    { type: 'divider' },

    {
        type: 'title',
        description: 'Gitlab Connection'
    },
    {
        type: 'input',
        name: 'gitlab.origin',
        description: 'API Endpoint, should ends with /api/v4',
        placeholder: "https://gitlab.some-org.com/api/v4"
    },

    {
        type: 'password',
        name: 'gitlab.token',
        description: 'Personnal Access Token (generated in gitlab account settings)',
        placeholder: '...'
    },
])


</script>