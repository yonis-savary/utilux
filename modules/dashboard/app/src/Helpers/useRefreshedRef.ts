import { refDebounced } from "@vueuse/core";
import { ref, Ref, watch, onScopeDispose } from "vue";

type BaseOptions = {
    immediate?: boolean
    interval?: number
}

type WithCache    = { cached: true;  cacheKey: string }
type WithoutCache = { cached?: false; cacheKey?: never }

type Options = BaseOptions & (WithCache | WithoutCache)

type RefreshableRef<T> = Ref<T> & {
    refresh: () => Promise<void>
    stop: () => void
}

export const useRefreshedRef = <T>(
    getter: () => T | Promise<T>,
    options: Options = {}
): RefreshableRef<T | undefined> => {
    const inner = ref<T | undefined>(undefined);
    let intervalId: ReturnType<typeof setInterval> | null = null;
    let hasCache = false;

    if (options.cached) {
        try {
            const stored = localStorage.getItem(options.cacheKey);
            if (stored !== null) {
                inner.value = JSON.parse(stored) as T;
                hasCache = true;
            }
        } catch {
            inner.value = undefined;
        }

        const debounced = refDebounced(inner, 500);
        watch(debounced, () => {
            if (inner.value === undefined) return;
            localStorage.setItem(options.cacheKey, JSON.stringify(inner.value));
        }, { deep: true });
    }

    const refresh = async () => {
        inner.value = await getter();
    };

    const stop = () => {
        if (intervalId !== null) {
            clearInterval(intervalId);
            intervalId = null;
        }
    };

    const skipInitial = options.immediate !== false && options.cached && hasCache;
    if (options.immediate !== false && !skipInitial) refresh();
    if (options.interval) intervalId = setInterval(refresh, options.interval);

    onScopeDispose(stop);

    const result = inner as RefreshableRef<T | undefined>;
    result.refresh = refresh;
    result.stop = stop;
    return result;
};
