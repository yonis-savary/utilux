<template>
    <div class="flex flex-col gap-3">
        <div class="flex flex-row items-center justify-between">
            <h1 class="text-2xl">Subjects</h1>
    
            <n-popover trigger="click">
                <template #trigger>
                    <n-button type="primary">
                        <n-icon>
                            <Plus/>
                        </n-icon>
                    </n-button>
                </template>
                <div class="flex flex-col gap-3">
                    <b>New Subject</b>
                    <n-input v-model:value="newSubjectName" placeholder="Subject Name" />
                    <n-button
                        :disabled="titles.includes(newSubjectName) || !newSubjectName"
                        type="primary"
                        @click="saveNewSubject"
                    >Save</n-button>
                </div>
            </n-popover>
        </div>
        <div class="flex flex-col gap-3">
            <SubjectComponent
                v-for="subject in subjectsStore.subjects"
                :subject="subject"
                :is-selected="expendedSubject === subject"
                @add-issue="issue => subject.issues.push(issue)"
                @focus="expendedSubject = subject"
                @blur="expendedSubject = undefined"
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
const expendedSubject = ref<Subject|undefined>()

const titles = computed(() => subjectsStore.subjects.map(x => x.name));

const saveNewSubject = () => {
    if (!newSubjectName.value)
        return;

    subjectsStore.subjects.push({
        name: newSubjectName.value,
        issues: []
    })
}

</script>