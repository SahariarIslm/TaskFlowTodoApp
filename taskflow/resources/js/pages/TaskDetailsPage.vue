<template>
    <DashboardLayout>
        <template #header>Task Details</template>
        <div v-if="task" class="bg-white rounded-lg shadow-sm border p-8 max-w-2xl">
            <div class="flex justify-between items-start mb-6">
                <h2 class="text-2xl font-bold text-gray-800">{{ task.title }}</h2>
                <div class="space-x-3">
                    <button @click="showModal = true" class="text-blue-600 text-sm">Edit</button>
                    <button @click="showConfirm = true" class="text-red-600 text-sm">Delete</button>
                </div>
            </div>
            <p class="text-gray-600 mb-6">{{ task.description || 'No description provided.' }}</p>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div><span class="text-gray-500">Status:</span> <span class="font-medium">{{ task.status }}</span></div>
                <div><span class="text-gray-500">Priority:</span> <span class="font-medium capitalize">{{ task.priority }}</span></div>
                <div><span class="text-gray-500">Due Date:</span> <span class="font-medium">{{ task.due_date || 'N/A' }}</span></div>
                <div><span class="text-gray-500">Assignee:</span> <span class="font-medium">{{ task.assignee || 'Unassigned' }}</span></div>
            </div>
            <router-link to="/dashboard" class="inline-block mt-8 text-indigo-600 text-sm">&larr; Back to Dashboard</router-link>
        </div>

        <TaskModal :show="showModal" is-edit :task-data="task" @close="showModal = false" @save="handleSave" />
        <ConfirmModal :show="showConfirm" title="Delete Task" message="This will permanently delete the task." @cancel="showConfirm = false" @confirm="handleDelete" />
    </DashboardLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from '../plugins/axios';
import DashboardLayout from '../layouts/DashboardLayout.vue';
import TaskModal from '../components/TaskModal.vue';
import ConfirmModal from '../components/ConfirmModal.vue';

const route = useRoute();
const router = useRouter();
const task = ref(null);
const showModal = ref(false);
const showConfirm = ref(false);

const fetchTask = async () => {
    const { data } = await axios.get(`/tasks/${route.params.id}`);
    task.value = data.data;
};

const handleSave = async (formData) => {
    await axios.put(`/tasks/${task.value.id}`, formData);
    showModal.value = false;
    fetchTask();
};

const handleDelete = async () => {
    await axios.delete(`/tasks/${task.value.id}`);
    router.push('/dashboard');
};

onMounted(fetchTask);
</script>
