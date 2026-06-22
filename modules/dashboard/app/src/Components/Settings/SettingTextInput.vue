<template>
    <n-input v-model:value="configValue" type="text" :placeholder="placeholder" />
</template>

<script setup lang="ts">
import { getConfigRef } from '../../Helpers/Config';


const props = defineProps<{
    name: string,
    placeholder?: string|undefined
}>()

const configValue = getConfigRef(props.name);

</script>