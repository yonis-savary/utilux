import { useDebounceFn } from "@vueuse/core";
import { ipcMain } from "electron";
import Store from 'electron-store'
import { Subject } from "../Types/Subject";
import { ref, watch } from "vue";

const store = new Store<Record<string, any>>({name: 'dashboard-subject'});

const subjects = ref<Record<string,Subject>>({})

;(async() => {
  const subjectArr = (await store.get('subjects')) ?? []
  subjects.value = Object.fromEntries(subjectArr.map((x: Subject) => [x.name, x]))
})();

const writeSubjects = useDebounceFn(() => {
  store.set('subjects', Object.values(subjects.value))
}, 500)

watch(subjects, writeSubjects, {deep: true})

export const registerSubjectHandlers = () => {
  ipcMain.handle('subjects:save', (_, value: Subject) => {
    subjects.value[value.name] = value;
  })
  ipcMain.handle('subjects:save-all', (_, subjectsArr: Subject[]) => {
    subjects.value = Object.fromEntries(subjectsArr.map((x: Subject) => [x.name, x]))
  })
  ipcMain.handle('subjects:get-all', (_) => Object.values(
    JSON.parse(JSON.stringify(subjects.value))
  ))
}