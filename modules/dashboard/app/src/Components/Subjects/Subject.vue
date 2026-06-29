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
                    <small v-if="subject.issues.length">{{ subject.issues.join(', ') }}</small>
                </div>

                <div class="ml-auto flex flex-row items-center gap-2">
                    <n-tag :type="statusTagType" size="small" round>{{ statusLabel }}</n-tag>

                    <n-select
                        v-model:value="subject.status"
                        :options="statusOptions"
                        size="small"
                        style="width: 130px"
                    />

                    <n-popover trigger="click">
                        <template #trigger>
                            <n-button size="small">
                                <n-icon><Plus/></n-icon>
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

                    <n-popover trigger="click">
                        <template #trigger>
                            <n-button size="small" type="error">
                                <n-icon><Trash/></n-icon>
                            </n-button>
                        </template>
                        <div class="flex flex-col gap-3">
                            <b>Delete "{{ subject.name }}"?</b>
                            <small>This action is irreversible.</small>
                            <n-button type="error" @click="emits('delete')">Confirm</n-button>
                        </div>
                    </n-popover>
                </div>
            </div>

            <div class="flex flex-col gap-4" v-if="isSelected">

                <div class="flex flex-col gap-2" v-if="issues.length">
                    <Issue v-for="issue in issues" :issue="issue" :issues="[]" />
                </div>

                <div class="flex flex-col gap-2">
                    <b class="text-sm">Comments</b>
                    <div
                        v-if="subject.comments.length"
                        class="flex flex-col gap-2"
                    >
                        <div
                            v-for="(comment, i) in subject.comments"
                            :key="i"
                            class="flex flex-col gap-1 p-2 rounded border border-gray-200 dark:border-gray-700"
                        >
                            <span class="text-sm whitespace-pre-wrap">{{ comment.text }}</span>
                            <small class="opacity-50">{{ formatDate(comment.createdAt) }}</small>
                        </div>
                    </div>
                    <div class="flex flex-row gap-2">
                        <n-input
                            v-model:value="newComment"
                            type="textarea"
                            placeholder="Add a comment..."
                            :autosize="{ minRows: 2, maxRows: 6 }"
                            class="flex-1"
                            @keydown.ctrl.enter="submitComment"
                        />
                        <n-button :disabled="!newComment" type="primary" @click="submitComment">
                            <n-icon><Send/></n-icon>
                        </n-button>
                    </div>
                </div>

                <div class="flex flex-col gap-2">
                    <div class="flex flex-row items-center justify-between">
                        <b class="text-sm">Attachments</b>
                        <n-button size="small" @click="openFolder">
                            <n-icon><Folder/></n-icon>
                        </n-button>
                    </div>
                    <div v-if="attachments.length" class="flex flex-col gap-1">
                        <div
                            v-for="file in attachments"
                            :key="file"
                            class="flex flex-row items-center gap-2 cursor-pointer hover:opacity-70 p-1 rounded"
                            @click="openFile(file)"
                        >
                            <n-icon><File/></n-icon>
                            <span class="text-sm">{{ file }}</span>
                        </div>
                    </div>
                    <small v-else class="opacity-50">No files — open the folder to add some.</small>
                </div>

            </div>
        </div>
    </n-card>
</template>

<script setup lang="ts">
import { Minus, Plus, Trash, Send, Folder, File } from '@vicons/tabler';
import { useIssuesStore } from '../../Stores/IssuesStore';
import { useSubjectsStore } from '../../Stores/SubjectsStore';
import { Subject } from '../../Types/Subject';
import { computed, ref, watch } from 'vue';
import Issue from '../Issue/Issue.vue';

const api = window.electronAPI.subjects
const issuesStore = useIssuesStore();
const subjectsStore = useSubjectsStore();

const emits = defineEmits<{
    (e: 'focus'): void
    (e: 'blur'): void
    (e: 'add-issue', issue: string): void
    (e: 'delete'): void
}>()

const props = defineProps<{
    subject: Subject
    isSelected: boolean
}>()

const issuesToAdd = ref('');
const newComment = ref('');
const attachments = ref<string[]>([]);

const statusOptions = [
    { label: 'Active', value: 'active' },
    { label: 'Standby', value: 'standby' },
    { label: 'Finished', value: 'finished' },
]

const statusTagType = computed(() => ({
    active: 'success',
    standby: 'warning',
    finished: 'default',
} as const)[props.subject.status])

const statusLabel = computed(() => ({
    active: 'Active',
    standby: 'Standby',
    finished: 'Finished',
})[props.subject.status])

const issues = computed(() =>
    issuesStore.issues?.filter(issue => props.subject.issues.includes(issue.key)) ?? []
)

const addIssues = () => {
    issuesToAdd.value
        .split(' ')
        .filter(x => x)
        .forEach(key => emits('add-issue', key))
    issuesToAdd.value = ''
}

const submitComment = () => {
    if (!newComment.value.trim()) return;
    subjectsStore.addComment(props.subject, newComment.value.trim())
    newComment.value = ''
}

const openFolder = () => api.openAttachmentsFolder(props.subject.name)

const openFile = (filename: string) => api.openAttachment(props.subject.name, filename)

const loadAttachments = async () => {
    attachments.value = await api.listAttachments(props.subject.name)
}

const formatDate = (iso: string) =>
    new Date(iso).toLocaleString('fr-FR', { dateStyle: 'short', timeStyle: 'short' })

watch(() => props.isSelected, (selected) => {
    if (selected) loadAttachments()
}, { immediate: true })
</script>
