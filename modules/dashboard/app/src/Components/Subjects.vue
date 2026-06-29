<template>
    <div class="flex flex-col gap-3">
        <div class="flex flex-row items-center justify-between">
            <h1 class="text-2xl">Subjects</h1>

            <div class="flex flex-row gap-2 items-center">
                <n-select
                    v-model:value="statusFilter"
                    :options="statusOptions"
                    size="small"
                    style="width: 140px"
                />
                <n-popover trigger="click" ref="createPopover">
                    <template #trigger>
                        <n-button type="primary">
                            <n-icon><Plus/></n-icon>
                        </n-button>
                    </template>
                    <div class="flex flex-col gap-3">
                        <b>New Subject</b>
                        <n-input
                            v-model:value="newSubjectName"
                            placeholder="Subject Name"
                            @keyup.enter="saveNewSubject"
                        />
                        <n-button
                            :disabled="titles.includes(newSubjectName) || !newSubjectName"
                            type="primary"
                            @click="saveNewSubject"
                        >Save</n-button>
                    </div>
                </n-popover>
            </div>
        </div>

        <div class="flex flex-col gap-3">
            <SubjectComponent
                v-for="subject in filteredSubjects"
                :key="subject.name"
                :subject="subject"
                :is-selected="expendedSubject === subject"
                @add-issue="issue => subject.issues.push(issue)"
                @focus="expendedSubject = subject"
                @blur="expendedSubject = undefined"
                @delete="subjectsStore.remove(subject.name)"
            />
        </div>
    </div>
</template>

<script setup lang="ts">
import { Plus } from '@vicons/tabler';
import { useSubjectsStore } from '../Stores/SubjectsStore';
import { computed, ref } from 'vue';
import { Subject } from 'src/Types/Subject';
import SubjectComponent from './Subjects/Subject.vue';

const subjectsStore = useSubjectsStore();

const newSubjectName = ref('');
const expendedSubject = ref<Subject | undefined>();
const statusFilter = ref<string | null>(null);

const statusOptions = [
    { label: 'All', value: null },
    { label: 'Active', value: 'active' },
    { label: 'Standby', value: 'standby' },
    { label: 'Finished', value: 'finished' },
]

const titles = computed(() => subjectsStore.subjects.map(x => x.name));

const filteredSubjects = computed(() =>
    statusFilter.value
        ? subjectsStore.subjects.filter(s => s.status === statusFilter.value)
        : subjectsStore.subjects
);

const saveNewSubject = () => {
    if (!newSubjectName.value || titles.value.includes(newSubjectName.value)) return;
    subjectsStore.create(newSubjectName.value)
    newSubjectName.value = ''
}
</script>
