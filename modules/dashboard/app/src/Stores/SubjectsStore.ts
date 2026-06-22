import { useDebounceFn } from "@vueuse/core";
import { defineStore } from "pinia";
import { Subject } from "src/Types/Subject";
import { ref, watch } from "vue";

const api = window.electronAPI.subjects

export const useSubjectsStore = defineStore('subjects', () => {
    const subjects = ref<Subject[]>([])
    let cancelNextTickWatch = false;
    const save = useDebounceFn(() => {
        if (!subjects.value.length) 
            return;
        const d = JSON.parse(JSON.stringify([...subjects.value]))
        api.saveAll(d)
    }, 500)

    watch(subjects, () => {
        if (cancelNextTickWatch)
            return cancelNextTickWatch = false
        save()
    }, {deep: true});

    const refresh = async () => {
        cancelNextTickWatch = true;
        subjects.value = await api.getAll()
    }

    const create = (name: string) => {
        subjects.value.push({ name, issues: [] })
    }

    refresh();

    return {
        create,
        subjects
    }
})