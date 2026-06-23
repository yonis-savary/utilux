<template>
    <div class="grid grid-cols-2 gap-10">
        <Disks/>
        <Docker/>
        <Gxt/>
        <Claude/>


    </div>
</template>

<script setup lang="ts">
import Disks from './Disks.vue';
import Docker from './Docker.vue';
import Gxt from './Gxt.vue';
import Claude from './Claude.vue';

</script>