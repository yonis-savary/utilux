<template>
    <n-card>
        <div class="flex flex-col gap-3">
            <div class="flex flex-row items-center gap-3">
                <n-icon class="cursor-pointer" size="25">
                    <Minus v-if="isSelected" @click="emits('blur')"/>
                    <Plus v-else @click="emits('focus')"/>
                </n-icon>

                <div class="flex flex-col gap-0">
                    <b>{{ subject.name }}</b>
                    <small>{{ subject.issues.join(', ') }}</small>
                </div>
                <div class="ml-auto">
                    <n-popover trigger="click">
                        <template #trigger>
                            <n-button>
                                <n-icon>
                                    <Plus/>
                                </n-icon>
                            </n-button>
                        </template>
                        <div class="flex flex-col gap-3">
                            <b>Add issues</b>
                            <n-input v-model:value="issuesToAdd" placeholder="Issue key(s)" />
                            <n-button
                                :disabled="!issuesToAdd"
                                type="primary"
                                @click="addIssues"
                            >Save</n-button>
                        </div>
                    </n-popover>
                </div>
            </div>
            <div class="flex flex-col gap-3" v-if="isSelected">
                <Issue v-for="issue in issues" :issue="issue" :issues="[]" />
            </div>
        </div>
    </n-card>
</template>

<script setup lang="ts">
import { Minus, Plus } from '@vicons/tabler';
import { useIssuesStore } from '../../Stores/IssuesStore';
import { Subject } from '../../Types/Subject';
import { computed, ref } from 'vue';
import Issue from '../Issue/Issue.vue';

const issuesStore = useIssuesStore();

const emits = defineEmits<{
    (e: 'focus'): void
    (e: 'blur'): void
    (e: 'add-issue', issue: string): void
}>()

const props = defineProps<{
    subject: Subject
    isSelected: boolean
}>()

const issuesToAdd = ref('');

const addIssues = () => {
    issuesToAdd.value
        .split(' ')
        .filter(x => x)
        .forEach(key => emits('add-issue', key))
}

const issues = computed(() => 
    issuesStore.issues?.filter(issue => props.subject.issues.includes(issue.key)) ?? []
)
</script>