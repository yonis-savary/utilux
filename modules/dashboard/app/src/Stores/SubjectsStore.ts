import { useDebounceFn } from "@vueuse/core";
import { defineStore } from "pinia";
import { Subject, SubjectComment } from "src/Types/Subject";
import { ref, watch } from "vue";

const api = window.electronAPI.subjects

export const useSubjectsStore = defineStore('subjects', () => {
    const subjects = ref<Subject[]>([])
    let cancelNextTickWatch = false;

    const save = useDebounceFn(() => {
        if (!subjects.value.length) return;
        const d = JSON.parse(JSON.stringify([...subjects.value]))
        api.saveAll(d)
    }, 500)

    watch(subjects, () => {
        if (cancelNextTickWatch)
            return cancelNextTickWatch = false
        save()
    }, { deep: true });

    const refresh = async () => {
        cancelNextTickWatch = true;
        subjects.value = await api.getAll()
    }

    const create = (name: string) => {
        const subject: Subject = {
            name,
            status: 'active',
            issues: [],
            comments: [],
            createdAt: new Date().toISOString(),
        }
        subjects.value.push(subject)
        api.save(subject)
    }

    const remove = async (name: string) => {
        await api.delete(name)
        subjects.value = subjects.value.filter(s => s.name !== name)
    }

    const addComment = (subject: Subject, text: string) => {
        const comment: SubjectComment = { text, createdAt: new Date().toISOString() }
        subject.comments.push(comment)
    }

    refresh();

    return {
        subjects,
        create,
        remove,
        addComment,
        refresh,
    }
})
