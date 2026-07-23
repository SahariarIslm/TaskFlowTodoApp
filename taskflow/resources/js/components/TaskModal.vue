<template>
    <div v-if="show" class="fixed inset-0 bg-black/50 flex items-center justify-center p-4 z-50">
        <div class="bg-white rounded-lg max-w-md w-full p-6 shadow-xl">
            <h3 class="text-lg font-bold mb-4">{{ isEdit ? 'Edit Task' : 'Create Task' }}</h3>
            <form @submit.prevent="submit" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium">Title</label>
                    <input v-model="form.title" required class="w-full border rounded px-3 py-2 mt-1" />
                </div>
                <div>
                    <label class="block text-sm font-medium">Description</label>
                    <textarea v-model="form.description" rows="3" class="w-full border rounded px-3 py-2 mt-1"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium">Status</label>
                        <select v-model="form.status" class="w-full border rounded px-3 py-2 mt-1">
                            <option value="todo">To Do</option>
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Priority</label>
                        <select v-model="form.priority" class="w-full border rounded px-3 py-2 mt-1">
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium">Due Date</label>
                        <input v-model="form.due_date" type="date" class="w-full border rounded px-3 py-2 mt-1" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Assignee</label>
                        <input v-model="form.assignee" class="w-full border rounded px-3 py-2 mt-1" />
                    </div>
                </div>
                <div class="flex justify-end gap-2 mt-6">
                    <button type="button" @click="$emit('close')" class="px-4 py-2 border rounded">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded">Save</button>
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
