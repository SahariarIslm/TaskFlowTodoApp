<template>
    <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/55 p-4 backdrop-blur-sm">
        <div class="tf-panel max-h-[90vh] w-full max-w-2xl overflow-y-auto p-6 shadow-[0_30px_90px_rgba(15,23,42,0.24)]">
            <div class="mb-6 flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.08em] text-slate-400">Task</p>
                    <h3 class="mt-1 text-2xl font-bold text-[#0f172b]">{{ isEdit ? 'Edit Task' : 'Create Task' }}</h3>
                </div>
                <button type="button" @click="$emit('close')" class="flex h-10 w-10 items-center justify-center rounded-2xl text-slate-500 hover:bg-slate-100" aria-label="Close modal">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="m6 6 12 12M18 6 6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                    </svg>
                </button>
            </div>

            <form @submit.prevent="submit" class="space-y-5">
                <div>
                    <label class="tf-label">Title</label>
                    <input v-model="form.title" required class="tf-input" placeholder="Launch planning sprint" />
                </div>
                <div>
                    <label class="tf-label">Description</label>
                    <textarea v-model="form.description" rows="4" class="tf-textarea" placeholder="Add context, requirements, or acceptance notes"></textarea>
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
                        <label class="tf-label">Due Date</label>
                        <input v-model="form.due_date" type="date" class="tf-input" />
                    </div>
                    <div>
                        <label class="tf-label">Assignee</label>
                        <input v-model="form.assignee" class="tf-input" placeholder="Alex Morgan" />
                    </div>
                </div>
                <div class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:justify-end">
                    <button type="button" @click="$emit('close')" class="tf-secondary-btn">Cancel</button>
                    <button type="submit" class="tf-primary-btn">{{ isEdit ? 'Save changes' : 'Create task' }}</button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { reactive, watch } from 'vue';

const props = defineProps({ show: Boolean, isEdit: Boolean, taskData: Object });
const emit = defineEmits(['close', 'save']);

const blank = { title: '', description: '', status: 'todo', priority: 'medium', due_date: '', assignee: '' };
const form = reactive({ ...blank });

watch(
    () => props.show,
    (isOpen) => {
        if (isOpen) Object.assign(form, props.taskData ? { ...blank, ...props.taskData } : blank);
    }
);

const submit = () => emit('save', { ...form });
</script>
