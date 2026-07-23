<template>
    <DashboardLayout>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white p-6 rounded-lg shadow-sm border">
                <p class="text-sm font-semibold text-gray-500">Total Tasks</p>
                <p class="text-3xl font-bold mt-2">{{ stats.total_tasks }}</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-sm border border-l-4 border-l-yellow-400">
                <p class="text-sm font-semibold text-gray-500">To Do</p>
                <p class="text-3xl font-bold mt-2">{{ stats.todo }}</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-sm border border-l-4 border-l-blue-500">
                <p class="text-sm font-semibold text-gray-500">In Progress</p>
                <p class="text-3xl font-bold mt-2">{{ stats.in_progress }}</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-sm border border-l-4 border-l-green-500">
                <p class="text-sm font-semibold text-gray-500">Completed</p>
                <p class="text-3xl font-bold mt-2">{{ stats.completed }}</p>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border p-6">
            <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                <div class="flex gap-4">
                    <select v-model="filters.status" @change="fetchTasks(1)" class="border rounded px-3 py-2">
                        <option value="">All Statuses</option>
                        <option value="todo">To Do</option>
                        <option value="in_progress">In Progress</option>
                        <option value="completed">Completed</option>
                    </select>
                    <select v-model="filters.priority" @change="fetchTasks(1)" class="border rounded px-3 py-2">
                        <option value="">All Priorities</option>
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                    </select>
                    <select v-model="filters.sort" @change="fetchTasks(1)" class="border rounded px-3 py-2">
                        <option value="">Default Order</option>
                        <option value="due_date">Due Date</option>
                        <option value="priority">Priority</option>
                        <option value="title">Title</option>
                    </select>
                </div>
                <button @click="openCreate" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                    + Add New Task
                </button>
            </div>

            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b text-sm font-semibold text-gray-600 bg-gray-50">
                        <th class="p-3">Title</th>
                        <th class="p-3">Status</th>
                        <th class="p-3">Priority</th>
                        <th class="p-3">Due Date</th>
                        <th class="p-3">Assignee</th>
                        <th class="p-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="task in tasks" :key="task.id" class="border-b hover:bg-gray-50">
                        <td class="p-3 font-medium text-indigo-600 cursor-pointer" @click="$router.push(`/tasks/${task.id}`)">
                            {{ task.title }}
                        </td>
                        <td class="p-3"><span class="px-2 py-1 text-xs rounded bg-gray-200">{{ formatStatus(task.status) }}</span></td>
                        <td class="p-3"><span class="px-2 py-1 text-xs rounded bg-indigo-100 text-indigo-700 capitalize">{{ task.priority }}</span></td>
                        <td class="p-3 text-sm">{{ task.due_date || 'N/A' }}</td>
                        <td class="p-3 text-sm">{{ task.assignee || 'Unassigned' }}</td>
                        <td class="p-3 space-x-3">
                            <button @click="openEdit(task)" class="text-blue-600 text-sm">Edit</button>
                            <button @click="confirmDelete(task)" class="text-red-600 text-sm">Delete</button>
                        </td>
                    </tr>
                    <tr v-if="!tasks.length">
                        <td colspan="6" class="p-6 text-center text-gray-500">No tasks found.</td>
                    </tr>
                </tbody>
            </table>

            <div class="flex justify-between items-center mt-4 text-sm text-gray-600" v-if="meta.last_page > 1">
                <span>Page {{ meta.current_page }} of {{ meta.last_page }}</span>
                <div class="space-x-2">
                    <button :disabled="meta.current_page === 1" @click="fetchTasks(meta.current_page - 1)" class="px-3 py-1 border rounded disabled:opacity-40">Prev</button>
                    <button :disabled="meta.current_page === meta.last_page" @click="fetchTasks(meta.current_page + 1)" class="px-3 py-1 border rounded disabled:opacity-40">Next</button>
                </div>
            </div>
        </div>

        <TaskModal :show="showModal" :is-edit="isEdit" :task-data="selectedTask" @close="showModal = false" @save="handleSave" />
        <ConfirmModal :show="showConfirm" title="Delete Task" message="This will permanently delete the task." @cancel="showConfirm = false" @confirm="handleDelete" />
    </DashboardLayout>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import axios from '../plugins/axios';
import DashboardLayout from '../layouts/DashboardLayout.vue';
import TaskModal from '../components/TaskModal.vue';
import ConfirmModal from '../components/ConfirmModal.vue';

const tasks = ref([]);
const meta = reactive({ current_page: 1, last_page: 1 });
const stats = reactive({ total_tasks: 0, todo: 0, in_progress: 0, completed: 0 });
const filters = reactive({ status: '', priority: '', sort: '' });

const showModal = ref(false);
const isEdit = ref(false);
const selectedTask = ref(null);

const showConfirm = ref(false);
const taskToDelete = ref(null);

const formatStatus = (s) => ({ todo: 'To Do', in_progress: 'In Progress', completed: 'Completed' }[s] || s);

const fetchStats = async () => {
    const { data } = await axios.get('/dashboard/stats');
    Object.assign(stats, data);
};

const fetchTasks = async (page = 1) => {
    const { data } = await axios.get('/tasks', { params: { ...filters, page } });
    tasks.value = data.data;
    Object.assign(meta, { current_page: data.meta.current_page, last_page: data.meta.last_page });
};

const openCreate = () => {
    selectedTask.value = null;
    isEdit.value = false;
    showModal.value = true;
};

const openEdit = (task) => {
    selectedTask.value = task;
    isEdit.value = true;
    showModal.value = true;
};

const handleSave = async (formData) => {
    if (isEdit.value) {
        await axios.put(`/tasks/${selectedTask.value.id}`, formData);
    } else {
        await axios.post('/tasks', formData);
    }
    showModal.value = false;
    fetchTasks(meta.current_page);
    fetchStats();
};

const confirmDelete = (task) => {
    taskToDelete.value = task;
    showConfirm.value = true;
};

const handleDelete = async () => {
    await axios.delete(`/tasks/${taskToDelete.value.id}`);
    showConfirm.value = false;
    fetchTasks(meta.current_page);
    fetchStats();
};

onMounted(() => {
    fetchStats();
    fetchTasks();
});
</script>
