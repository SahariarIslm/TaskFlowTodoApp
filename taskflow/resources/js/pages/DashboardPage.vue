<template>
    <DashboardLayout>
        <template #header>Dashboard</template>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div class="tf-panel p-5">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-semibold text-slate-500">Total Tasks</p>
                    <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-slate-100 text-slate-600">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M8 7h10M8 12h10M8 17h6M4 7h.01M4 12h.01M4 17h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                        </svg>
                    </span>
                </div>
                <p class="mt-4 text-3xl font-bold text-[#0f172b]">{{ stats.total_tasks }}</p>
            </div>
            <div class="tf-panel p-5">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-semibold text-slate-500">To Do</p>
                    <span class="h-3 w-3 rounded-full bg-amber-400"></span>
                </div>
                <p class="mt-4 text-3xl font-bold text-[#0f172b]">{{ stats.todo }}</p>
            </div>
            <div class="tf-panel p-5">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-semibold text-slate-500">In Progress</p>
                    <span class="h-3 w-3 rounded-full bg-[#4f39f6]"></span>
                </div>
                <p class="mt-4 text-3xl font-bold text-[#0f172b]">{{ stats.in_progress }}</p>
            </div>
            <div class="tf-panel p-5">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-semibold text-slate-500">Completed</p>
                    <span class="h-3 w-3 rounded-full bg-emerald-500"></span>
                </div>
                <p class="mt-4 text-3xl font-bold text-[#0f172b]">{{ stats.completed }}</p>
            </div>
        </div>

        <section class="tf-panel mt-6 overflow-hidden">
            <div class="flex flex-col gap-4 border-b border-slate-200 p-5 xl:flex-row xl:items-center xl:justify-between">
                <div>
                    <h2 class="text-lg font-bold text-[#0f172b]">Tasks</h2>
                    <p class="text-sm text-slate-500">Filter, open, edit, or remove workspace tasks.</p>
                </div>
                <div class="flex flex-col gap-3 md:flex-row">
                    <select v-model="filters.status" @change="fetchTasks(1)" class="tf-select md:w-40">
                        <option value="">All Statuses</option>
                        <option value="todo">To Do</option>
                        <option value="in_progress">In Progress</option>
                        <option value="completed">Completed</option>
                    </select>
                    <select v-model="filters.priority" @change="fetchTasks(1)" class="tf-select md:w-40">
                        <option value="">All Priorities</option>
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                    </select>
                    <select v-model="filters.sort" @change="fetchTasks(1)" class="tf-select md:w-40">
                        <option value="">Default Order</option>
                        <option value="due_date">Due Date</option>
                        <option value="priority">Priority</option>
                        <option value="title">Title</option>
                    </select>
                    <button @click="openCreate" class="tf-primary-btn">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                        </svg>
                        Add New Task
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[780px] text-left">
                    <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-[0.06em] text-slate-400">
                        <tr>
                            <th class="px-5 py-4">Title</th>
                            <th class="px-5 py-4">Status</th>
                            <th class="px-5 py-4">Priority</th>
                            <th class="px-5 py-4">Due Date</th>
                            <th class="px-5 py-4">Assignee</th>
                            <th class="px-5 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="task in tasks" :key="task.id" class="bg-white transition hover:bg-slate-50">
                            <td class="px-5 py-4">
                                <button class="text-left font-semibold text-[#4f39f6] hover:text-[#432dd7]" @click="$router.push(`/tasks/${task.id}`)">
                                    {{ task.title }}
                                </button>
                                <p class="mt-1 line-clamp-1 max-w-md text-xs text-slate-500">{{ task.description || 'No description provided.' }}</p>
                            </td>
                            <td class="px-5 py-4">
                                <span class="tf-chip" :class="statusClass(task.status)">{{ formatStatus(task.status) }}</span>
                            </td>
                            <td class="px-5 py-4">
                                <span class="tf-chip capitalize" :class="priorityClass(task.priority)">{{ task.priority }}</span>
                            </td>
                            <td class="px-5 py-4 text-sm text-slate-600">{{ task.due_date || 'N/A' }}</td>
                            <td class="px-5 py-4 text-sm text-slate-600">{{ task.assignee || 'Unassigned' }}</td>
                            <td class="px-5 py-4">
                                <div class="flex justify-end gap-2">
                                    <button @click="openEdit(task)" class="tf-secondary-btn h-9 px-3">Edit</button>
                                    <button @click="confirmDelete(task)" class="h-9 rounded-2xl px-3 text-sm font-semibold text-rose-600 hover:bg-rose-50">Delete</button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!tasks.length">
                            <td colspan="6" class="px-5 py-12 text-center text-sm text-slate-500">No tasks found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="flex flex-col gap-3 border-t border-slate-200 p-5 text-sm text-slate-600 sm:flex-row sm:items-center sm:justify-between" v-if="meta.last_page > 1">
                <span>Page {{ meta.current_page }} of {{ meta.last_page }}</span>
                <div class="flex gap-2">
                    <button :disabled="meta.current_page === 1" @click="fetchTasks(meta.current_page - 1)" class="tf-secondary-btn h-9 px-4">Prev</button>
                    <button :disabled="meta.current_page === meta.last_page" @click="fetchTasks(meta.current_page + 1)" class="tf-secondary-btn h-9 px-4">Next</button>
                </div>
            </div>
        </section>

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
const statusClass = (s) =>
    ({
        todo: 'bg-amber-50 text-amber-700',
        in_progress: 'bg-indigo-50 text-[#4f39f6]',
        completed: 'bg-emerald-50 text-emerald-700',
    })[s] || 'bg-slate-100 text-slate-700';
const priorityClass = (p) =>
    ({
        low: 'bg-slate-100 text-slate-600',
        medium: 'bg-sky-50 text-sky-700',
        high: 'bg-rose-50 text-rose-700',
    })[p] || 'bg-slate-100 text-slate-700';

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
