<template>
    <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/45 p-4">
        <div class="w-full max-w-[700px] overflow-hidden rounded-2xl bg-white shadow-[0_30px_90px_rgba(15,23,42,0.28)]">
            <div class="flex items-start justify-between gap-4 border-b border-slate-100 px-6 py-5">
                <div>
                    <h3 class="text-lg font-bold text-[#0f172b]">{{ isEdit ? 'Edit' : 'Create New Task' }}</h3>
                    <p class="mt-1 text-xs text-slate-500">{{ isEdit ? `Editing ${taskCode}` : 'Add a new task to your project' }}</p>
                </div>
                <button type="button" @click="$emit('close')" class="flex h-8 w-8 items-center justify-center rounded-full bg-slate-100 text-slate-500 hover:bg-slate-200" aria-label="Close modal">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="m6 6 12 12M18 6 6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                    </svg>
                </button>
            </div>

            <form @submit.prevent="submit" class="space-y-5 px-6 py-5">
                <div>
                    <label class="tf-label">Title</label>
                    <input v-model="form.title" required class="tf-input" placeholder="e.g. Redesign the onboarding flow" />
                </div>
                <div>
                    <label class="tf-label">Description</label>
                    <textarea v-model="form.description" rows="4" class="tf-textarea" placeholder="Add a detailed description of this task..."></textarea>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="tf-label">Status</label>
                        <select v-model="form.status" class="tf-select">
                            <option value="todo">To Do</option>
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                    <div>
                        <label class="tf-label">Priority</label>
                        <select v-model="form.priority" class="tf-select">
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                        </select>
                    </div>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="tf-label">Assignee</label>
                        <select v-model="form.assignee" class="tf-select">
                            <option value="">Unassigned</option>
                            <option v-for="user in users" :key="user.id" :value="user.name">{{ user.name }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="tf-label">Due Date</label>
                        <input v-model="form.due_date" type="date" class="tf-input" />
                    </div>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="tf-label">Project</label>
                        <select v-model="form.project" class="tf-select">
                            <option>Platform</option>
                            <option>Growth</option>
                            <option>Infrastructure</option>
                            <option>Design</option>
                        </select>
                    </div>
                    <div>
                        <label class="tf-label">Tags</label>
                        <input v-model="form.tags" class="tf-input" placeholder="Design, Frontend, Bug..." />
                    </div>
                </div>

                <div class="flex items-center justify-between border-t border-slate-100 pt-5">
                    <button type="button" @click="$emit('close')" class="h-10 rounded-2xl px-4 text-sm font-medium text-slate-600 hover:bg-slate-100">Cancel</button>
                    <div class="flex items-center gap-3">
                        <button v-if="isEdit" type="button" @click="$emit('delete')" class="inline-flex h-10 items-center gap-2 rounded-2xl px-4 text-sm font-semibold text-rose-600 hover:bg-rose-50">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6M10 11v5M14 11v5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            Delete
                        </button>
                        <button type="submit" class="tf-primary-btn">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path v-if="isEdit" d="M12 20h9M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                <path v-else d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                            </svg>
                            {{ isEdit ? 'Save Changes' : 'Create Task' }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { computed, reactive, watch } from 'vue';

const props = defineProps({ show: Boolean, isEdit: Boolean, taskData: Object, users: { type: Array, default: () => [] } });
const emit = defineEmits(['close', 'save', 'delete']);

const blank = { title: '', description: '', status: 'todo', priority: 'medium', due_date: '', assignee: '', project: 'Platform', tags: '' };
const form = reactive({ ...blank });
const taskCode = computed(() => (props.taskData?.id ? `TF-${String(props.taskData.id).padStart(3, '0')}` : 'TF-001'));

watch(
    () => props.show,
    (isOpen) => {
        if (isOpen) {
            const taskData = props.taskData
                ? {
                      ...blank,
                      ...props.taskData,
                      tags: Array.isArray(props.taskData.tags) ? props.taskData.tags.join(', ') : props.taskData.tags || '',
                  }
                : blank;

            Object.assign(form, taskData);
        }
    }
);

const submit = () =>
    emit('save', {
        title: form.title,
        description: form.description,
        status: form.status,
        priority: form.priority,
        due_date: form.due_date,
        assignee: form.assignee,
        project: form.project,
        tags: form.tags,
    });
</script>
