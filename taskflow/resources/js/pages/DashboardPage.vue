<template>
    <DashboardLayout @new-task="openCreate">
        <template #header>{{ pageTitle }}</template>
        <template #subtitle>{{ greeting }}, {{ firstName }} - here's what's happening</template>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div v-for="card in statCards" :key="card.label" class="tf-panel p-6">
                <div class="flex items-center justify-between">
                    <span class="flex h-11 w-11 items-center justify-center rounded-2xl" :class="card.iconClass" v-html="card.icon"></span>
                    <span class="rounded-full px-2 py-1 text-xs font-bold" :class="card.trendClass">{{ card.trend }}</span>
                </div>
                <p class="mt-5 text-3xl font-bold text-[#0f172b]">{{ card.value }}</p>
                <p class="mt-1 text-sm font-medium text-slate-700">{{ card.label }}</p>
                <p class="text-xs text-slate-400">vs last month</p>
            </div>
        </div>

        <section class="tf-panel mt-6 overflow-hidden">
            <div class="flex flex-col gap-3 border-b border-slate-100 bg-white p-5 md:flex-row md:items-center">
                <div class="flex h-10 min-w-0 flex-1 items-center rounded-2xl bg-slate-100 px-4 text-slate-400 md:max-w-md">
                    <svg class="mr-2 h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="m21 21-4.3-4.3M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                    </svg>
                    <input v-model="filters.search" @input="queueSearch" class="min-w-0 flex-1 border-0 bg-transparent text-sm outline-none placeholder:text-slate-400" placeholder="Search tasks, IDs, tags..." />
                </div>
                <select v-model="filters.status" @change="fetchTasks(1)" class="tf-select md:w-36">
                    <option value="">Status</option>
                    <option value="todo">To Do</option>
                    <option value="in_progress">In Progress</option>
                    <option value="completed">Completed</option>
                </select>
                <select v-model="filters.priority" @change="fetchTasks(1)" class="tf-select md:w-36">
                    <option value="">Priority</option>
                    <option value="low">Low</option>
                    <option value="medium">Medium</option>
                    <option value="high">High</option>
                </select>
                <span class="text-xs font-medium text-slate-400">{{ meta.total }} tasks</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[980px] text-left">
                    <thead class="bg-slate-50 text-xs font-bold uppercase tracking-[0.12em] text-slate-400">
                        <tr>
                            <th class="w-10 px-5 py-4"><input type="checkbox" class="h-4 w-4 rounded border-slate-300" /></th>
                            <th class="w-20 px-2 py-4">ID</th>
                            <th class="px-5 py-4">Task</th>
                            <th class="w-36 px-5 py-4">Status</th>
                            <th class="w-32 px-5 py-4">Priority</th>
                            <th class="w-32 px-5 py-4">Assignee</th>
                            <th class="w-36 px-5 py-4">Due Date</th>
                            <th class="w-24 px-5 py-4"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="task in tasks" :key="task.id" class="bg-white text-sm transition hover:bg-slate-50">
                            <td class="px-5 py-4"><input type="checkbox" class="h-4 w-4 rounded border-slate-300" /></td>
                            <td class="px-2 py-4 text-xs font-bold tracking-[0.08em] text-slate-300">{{ taskCode(task) }}</td>
                            <td class="px-5 py-4">
                                <button class="text-left font-semibold text-[#0f172b] hover:text-[#4f39f6]" @click="$router.push(`/tasks/${task.id}`)">
                                    {{ task.title }}
                                </button>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-xs text-slate-400">
                                    <span class="rounded-md bg-violet-50 px-2 py-0.5 font-medium text-[#4f39f6]">{{ task.project || 'Platform' }}</span>
                                    <span v-for="tag in tagsFor(task)" :key="tag" class="rounded-md bg-slate-100 px-2 py-0.5 text-slate-500">{{ tag }}</span>
                                    <span class="inline-flex items-center gap-1">
                                        <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path d="M21 15a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4v8Z" stroke="currentColor" stroke-width="2" />
                                        </svg>
                                        {{ commentCount(task) }}
                                    </span>
                                    <span class="inline-flex items-center gap-1">
                                        <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path d="m21.4 11.6-8.8 8.8a5 5 0 0 1-7.1-7.1l9.2-9.2a3.5 3.5 0 0 1 5 5l-9.2 9.2a2 2 0 0 1-2.8-2.8l8.5-8.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                                        </svg>
                                        {{ attachmentCount(task) }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <span class="tf-chip gap-1.5" :class="statusClass(task.status)">
                                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                    {{ formatStatus(task.status) }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <span class="tf-chip gap-1.5 capitalize" :class="priorityClass(task.priority)">
                                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                    {{ priorityLabel(task.priority) }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-2">
                                    <span class="flex h-7 w-7 items-center justify-center rounded-full text-xs font-bold" :class="avatarClass(task)">{{ assigneeInitials(task) }}</span>
                                    <span class="text-sm font-medium text-slate-600">{{ shortAssignee(task) }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-xs font-medium text-slate-500">
                                <span class="inline-flex items-center gap-1.5">
                                    <svg class="h-3.5 w-3.5 text-slate-400" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M8 2v4M16 2v4M3 10h18M5 5h14a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                                    </svg>
                                    {{ task.due_date || 'N/A' }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex justify-end gap-3">
                                    <button @click="openEdit(task)" class="text-blue-500 hover:text-blue-700" type="button" aria-label="Edit task">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path d="M12 20h9M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </button>
                                    <button @click="confirmDelete(task)" class="text-rose-500 hover:text-rose-700" type="button" aria-label="Delete task">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6M10 11v5M14 11v5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!tasks.length">
                            <td colspan="8" class="px-5 py-12 text-center text-sm text-slate-500">No tasks found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="flex items-center justify-between border-t border-slate-100 px-5 py-4 text-xs text-slate-500">
                <span>Showing {{ tasks.length }} of {{ meta.total || stats.total_tasks || tasks.length }} tasks</span>
                <div class="flex items-center gap-2" v-if="meta.last_page > 1">
                    <button :disabled="meta.current_page === 1" @click="fetchTasks(meta.current_page - 1)" class="flex h-8 w-8 items-center justify-center rounded-full text-slate-400 disabled:opacity-40" aria-label="Previous page">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="m15 18-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                        </svg>
                    </button>
                    <button v-for="page in meta.last_page" :key="page" @click="fetchTasks(page)" class="flex h-8 w-8 items-center justify-center rounded-full font-semibold" :class="page === meta.current_page ? 'bg-[#4f39f6] text-white' : 'text-slate-500 hover:bg-slate-100'">
                        {{ page }}
                    </button>
                    <button :disabled="meta.current_page === meta.last_page" @click="fetchTasks(meta.current_page + 1)" class="flex h-8 w-8 items-center justify-center rounded-full text-slate-400 disabled:opacity-40" aria-label="Next page">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="m9 18 6-6-6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                        </svg>
                    </button>
                </div>
            </div>
        </section>

        <TaskModal :show="showModal" :is-edit="isEdit" :task-data="selectedTask" :users="users" @close="showModal = false" @save="handleSave" @delete="deleteFromModal" />
        <ConfirmModal :show="showConfirm" title="Delete Task" message="This will permanently delete the task." @cancel="showConfirm = false" @confirm="handleDelete" />
    </DashboardLayout>
</template>

<script setup>
import { computed, ref, reactive, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import axios from '../plugins/axios';
import { useAuthStore } from '../stores/auth';
import { useDashboardStore } from '../stores/dashboard';
import DashboardLayout from '../layouts/DashboardLayout.vue';
import TaskModal from '../components/TaskModal.vue';
import ConfirmModal from '../components/ConfirmModal.vue';

const tasks = ref([]);
const users = ref([]);
const meta = reactive({ current_page: 1, last_page: 1, total: 0 });
const stats = reactive({ total_tasks: 0, todo: 0, in_progress: 0, completed: 0 });
const filters = reactive({ status: '', priority: '', sort: '', search: '' });
const route = useRoute();
const auth = useAuthStore();
const dashboard = useDashboardStore();
let searchTimer = null;

const showModal = ref(false);
const isEdit = ref(false);
const selectedTask = ref(null);

const showConfirm = ref(false);
const taskToDelete = ref(null);

const pageTitle = computed(() => route.meta.title || 'Dashboard');
const firstName = computed(() => (auth.user?.name || 'there').split(' ')[0]);
const greeting = computed(() => {
    const hour = new Date().getHours();

    if (hour < 12) return 'Good morning';
    if (hour < 17) return 'Good afternoon';
    if (hour < 21) return 'Good evening';
    return 'Good night';
});

const overdueCount = computed(() => {
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    return tasks.value.filter((task) => task.due_date && new Date(task.due_date) < today && task.status !== 'completed').length;
});
const statCards = computed(() => [
    {
        label: 'Total Tasks',
        value: stats.total_tasks,
        trend: '+12%',
        trendClass: 'bg-emerald-50 text-emerald-600',
        iconClass: 'bg-indigo-50 text-[#4f39f6]',
        icon: '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"><path d="m9 11 2 2 4-5M5 5h14v14H5V5Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
    },
    {
        label: 'Completed',
        value: stats.completed,
        trend: '+8%',
        trendClass: 'bg-emerald-50 text-emerald-600',
        iconClass: 'bg-emerald-50 text-emerald-600',
        icon: '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"><path d="m9 12 2 2 4-5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
    },
    {
        label: 'In Progress',
        value: stats.in_progress,
        trend: '-3%',
        trendClass: 'bg-rose-50 text-rose-600',
        iconClass: 'bg-orange-50 text-orange-500',
        icon: '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"><path d="M13 2 4 14h7l-1 8 10-13h-7l1-7Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/></svg>',
    },
    {
        label: 'Overdue',
        value: overdueCount.value,
        trend: '-15%',
        trendClass: 'bg-rose-50 text-rose-600',
        iconClass: 'bg-rose-50 text-rose-600',
        icon: '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"><path d="M12 8v5M12 17h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>',
    },
]);
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
const priorityLabel = (p) => ({ low: 'Low', medium: 'Medium', high: 'High' }[p] || p);
const taskCode = (task) => `TF-${String(task.id).padStart(3, '0')}`;
const tagsFor = (task) => task.tags || [];
const commentCount = (task) => task.comments_count || 0;
const attachmentCount = (task) => (task.id % 3) + 1;
const assigneeInitials = (task) =>
    (task.assignee || 'Unassigned')
        .split(' ')
        .map((part) => part[0])
        .join('')
        .slice(0, 2)
        .toUpperCase();
const shortAssignee = (task) => (task.assignee || 'Unassigned').split(' ')[0];
const avatarClass = (task) => {
    const classes = ['bg-violet-100 text-[#4f39f6]', 'bg-blue-100 text-blue-600', 'bg-emerald-100 text-emerald-700', 'bg-rose-100 text-rose-600'];
    return classes[task.id % classes.length];
};

const fetchStats = async () => {
    const { data } = await axios.get('/dashboard/stats');
    Object.assign(stats, data);
};

const fetchTasks = async (page = 1) => {
    const { data } = await axios.get('/tasks', { params: { ...filters, page } });
    tasks.value = data.data;
    Object.assign(meta, { current_page: data.meta.current_page, last_page: data.meta.last_page, total: data.meta.total });
};

const fetchUsers = async () => {
    const { data } = await axios.get('/users');
    users.value = data.data;
};

const queueSearch = () => {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => fetchTasks(1), 250);
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
    await Promise.all([fetchTasks(meta.current_page), fetchStats(), dashboard.refreshSidebarData()]);
};

const confirmDelete = (task) => {
    taskToDelete.value = task;
    showConfirm.value = true;
};

const handleDelete = async () => {
    await axios.delete(`/tasks/${taskToDelete.value.id}`);
    showConfirm.value = false;
    await Promise.all([fetchTasks(meta.current_page), fetchStats(), dashboard.refreshSidebarData()]);
};

const deleteFromModal = async () => {
    if (!selectedTask.value) return;
    await axios.delete(`/tasks/${selectedTask.value.id}`);
    showModal.value = false;
    await Promise.all([fetchTasks(meta.current_page), fetchStats(), dashboard.refreshSidebarData()]);
};

onMounted(() => {
    fetchStats();
    fetchTasks();
    fetchUsers();
    dashboard.refreshSidebarData();
});
</script>
