<template>
    <div class="background" ref="backgroundElement"></div>
</template>

<style> 

.background {
    width: 100vw;
    height: 100vh;
    background-size: cover;
}
</style>

<script setup lang="ts">
import { getConfigRef } from '../Helpers/Config';
import { onMounted, ref, watch } from 'vue';

const backgroundElement = ref<HTMLElement>();

onMounted(() => {
    const backgroundSrc = getConfigRef('interface.background');
    
    watch(backgroundSrc, () => {
        const element = backgroundElement.value;
        if (!element)
            return console.error('Could not find element for backgroundElement');

        const src = backgroundSrc.value;
        if (src) {
            element.style.backgroundColor = ''
            const url = src.startsWith('/') ? `local-file://${encodeURIComponent(src).replace(/%2F/g, '/')}` : src;
            element.style.backgroundImage = `url('${url}')`
        } else {
            element.style.backgroundColor = 'white'
            element.style.backgroundImage = ''
        }
    }, {immediate: true});
})


</script>