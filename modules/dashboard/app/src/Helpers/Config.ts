import { refDebounced } from '@vueuse/core';
import { ref, watch } from 'vue';

const config = window.electronAPI.config;

export const getConfigRef = (key: string) => {
    const value = ref();
    const debouncedValue = refDebounced(value, 200);

    watch(debouncedValue, () => {
        if (value.value === '')
            config.delete(key)
        else
            config.set(key, value.value)
    })

    ;(async () => {
        value.value = await config.get(key)
    })();

    return value;
}