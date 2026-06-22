import { refDebounced } from "@vueuse/core";
import { Ref, ref, watch } from "vue"

export const cachedRef = <T>(cacheKey: string) : Ref<T|undefined> => {
    const value = ref<T>()

    try {
        const initialValueString = localStorage.getItem(cacheKey);
        if (initialValueString)
            value.value = (JSON.parse(initialValueString) as T)
    } catch {
        value.value = undefined
    }

    const debouncedValue = refDebounced(value, 500);

    watch(debouncedValue, () => {
        if (!value.value)
            return;
        const rawValue = JSON.stringify(value.value)
        localStorage.setItem(cacheKey, rawValue);
    }, {immediate: true, deep: true})

    return value;
}