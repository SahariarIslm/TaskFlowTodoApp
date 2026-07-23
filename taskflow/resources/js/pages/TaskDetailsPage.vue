<template>
    <DashboardLayout @new-task="openCreate">
        <template #header>Task Detail</template>
        <template #subtitle>{{ task ? `${taskCode(task)} - ${task.project || 'Platform'}` : 'Loading task' }}</template>

        <div v-if="task" class="mx-auto grid max-w-[950px] gap-6 xl:grid-cols-[minmax(0,620px)_300px]">
            <section class="tf-panel overflow-hidden p-6">
                <div class="flex items-start justify-between gap-4">
                    <div class="min-w-0">
                        <div class="mb-3 flex flex-wrap gap-2">
                            <span class="rounded-md bg-slate-100 px-2.5 py-1 text-xs font-bold tracking-[0.08em] text-slate-400">{{ taskCode(task) }}</span>
                            <span class="tf-chip bg-violet-50 text-[#4f39f6]">{{ task.project || 'Platform' }}</span>
                        </div>
                        <h2 class="text-xl font-bold leading-tight text-[#0f172b]">{{ task.title }}</h2>
                    </div>
                    <button @click="openEdit" class="tf-secondary-btn h-9 px-4">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M12 20h9M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Edit
                    </button>
                </div>

                <div class="mt-4 flex flex-wrap gap-2">
                    <span class="tf-chip gap-1.5" :class="statusClass(task.status)">
                        <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                        {{ formatStatus(task.status) }}
                    </span>
                    <span class="tf-chip gap-1.5 capitalize" :class="priorityClass(task.priority)">
                        <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                        {{ priorityLabel(task.priority) }}
                    </span>
                    <span v-for="tag in tagsFor(task)" :key="tag" class="tf-chip bg-slate-100 text-slate-500">{{ tag }}</span>
                </div>

                <p class="mt-5 max-w-2xl text-sm leading-7 text-slate-600">
                    {{ task.description || 'Complete overhaul of the first-run experience to improve activation rates and reduce time-to-value for new signups.' }}
                </p>

                <div class="mt-8 border-b border-slate-100">
                    <div class="flex gap-8 text-sm font-semibold">
                        <button class="border-b-2 border-[#4f39f6] px-1 pb-3 text-[#4f39f6]">Overview</button>
                        <button class="px-1 pb-3 text-slate-400">Activity</button>
                    </div>
                </div>

                <div class="mt-5">
                    <p class="mb-3 text-xs font-bold uppercase tracking-[0.12em] text-slate-500">Comments ({{ comments.length }})</p>
                    <div class="grid gap-4">
                        <div v-for="comment in comments" :key="comment.id" class="grid grid-cols-[32px_1fr] gap-3">
                            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-100 text-xs font-bold text-blue-600">{{ userInitials(comment.author?.name) }}</span>
                            <div class="rounded-2xl bg-slate-50 p-4">
                                <div class="mb-1 flex items-center justify-between gap-3">
                                    <p class="text-xs font-bold text-slate-800">{{ comment.author?.name || 'Unknown user' }}</p>
                                    <p class="text-xs text-slate-400">{{ formatDateTime(comment.created_at) }}</p>
                                </div>
                                <p class="text-sm leading-6 text-slate-600">{{ comment.body }}</p>
                            </div>
                        </div>

                        <form @submit.prevent="addComment" class="grid grid-cols-[32px_1fr_auto] gap-3">
                            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-violet-100 text-xs font-bold text-[#4f39f6]">{{ userInitials(auth.user?.name) }}</span>
                            <input v-model="newComment" class="h-14 min-w-0 rounded-2xl border border-slate-200 px-4 text-sm outline-none placeholder:text-slate-400 focus:border-[#4f39f6] focus:ring-4 focus:ring-[#4f39f6]/10" placeholder="Add a comment..." />
                            <button class="tf-primary-btn h-14 px-4" type="submit" :disabled="!newComment.trim()">Send</button>
                        </form>
                    </div>
                </div>
            </section>

            <aside class="space-y-4">
                <section class="tf-panel p-5">
                    <h3 class="mb-5 text-xs font-bold uppercase tracking-[0.12em] text-slate-500">Details</h3>
                    <dl class="grid gap-4 text-sm">
                        <div class="flex items-center justify-between gap-4">
                            <dt class="text-xs font-medium text-slate-500">Assignee</dt>
                            <dd class="flex items-center gap-2 font-medium text-slate-700">
                                <span class="flex h-6 w-6 items-center justify-center rounded-full bg-violet-100 text-[10px] font-bold text-[#4f39f6]">{{ assigneeInitials(task) }}</span>
                                {{ task.assignee || 'Unassigned' }}
                            </dd>
                        </div>
                        <div class="flex items-center justify-between gap-4">
                            <dt class="text-xs font-medium text-slate-500">Due Date</dt>
                            <dd class="inline-flex items-center gap-1.5 font-medium text-slate-700">
                                <svg class="h-3.5 w-3.5 text-slate-400" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M8 2v4M16 2v4M3 10h18M5 5h14a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                                </svg>
                                {{ task.due_date || 'N/A' }}
                            </dd>
                        </div>
                        <div class="flex items-center justify-between gap-4">
                            <dt class="text-xs font-medium text-slate-500">Project</dt>
                            <dd><span class="tf-chip bg-violet-50 text-[#4f39f6]">{{ task.project || 'Platform' }}</span></dd>
                        </div>
                        <div class="flex items-center justify-between gap-4">
                            <dt class="text-xs font-medium text-slate-500">Created</dt>
                            <dd class="font-medium text-slate-700">{{ createdDate }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-4">
                            <dt class="text-xs font-medium text-slate-500">Comments</dt>
                            <dd class="inline-flex items-center gap-1.5 font-medium text-slate-700">
                                <svg class="h-3.5 w-3.5 text-slate-400" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M21 15a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4v8Z" stroke="currentColor" stroke-width="2" />
                                </svg>
                                {{ comments.length }}
                            </dd>
                        </div>
                        <div class="flex items-center justify-between gap-4">
                            <dt class="text-xs font-medium text-slate-500">Attachments</dt>
                            <dd class="inline-flex items-center gap-1.5 font-medium text-slate-700">
                                <svg class="h-3.5 w-3.5 text-slate-400" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="m21.4 11.6-8.8 8.8a5 5 0 0 1-7.1-7.1l9.2-9.2a3.5 3.5 0 0 1 5 5l-9.2 9.2a2 2 0 0 1-2.8-2.8l8.5-8.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                                </svg>
                                3
                            </dd>
                        </div>
                    </dl>
                </section>

                <section class="tf-panel p-5">
                    <h3 class="mb-4 text-xs font-bold uppercase tracking-[0.12em] text-slate-500">Attachments</h3>
                    <div class="grid gap-3">
                        <div v-for="file in attachments" :key="file.name" class="flex items-center gap-3">
                            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-violet-100 text-[#4f39f6]">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="m21.4 11.6-8.8 8.8a5 5 0 0 1-7.1-7.1l9.2-9.2a3.5 3.5 0 0 1 5 5l-9.2 9.2a2 2 0 0 1-2.8-2.8l8.5-8.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                                </svg>
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-xs font-bold text-slate-700">{{ file.name }}</p>
                                <p class="text-xs text-slate-400">{{ file.size }}</p>
                            </div>
                            <button class="text-slate-400 hover:text-[#4f39f6]" type="button" aria-label="Open attachment">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M14 3h7v7M10 14 21 3M21 14v5a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    <button class="tf-secondary-btn mt-4 h-9 w-full text-xs" type="button">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M12 16V4M8 8l4-4 4 4M20 16.5V19a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-2.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Upload file
                    </button>
                </section>
            </aside>
        </div>

        <div v-else class="tf-panel mx-auto max-w-xl p-8 text-sm text-slate-500">
            Loading task details...
        </div>

        <TaskModal :show="showModal" :is-edit="isEdit" :task-data="selectedTask" :users="users" @close="showModal = false" @save="handleSave" @delete="showConfirm = true" />
        <ConfirmModal :show="showConfirm" title="Delete Task" message="This will permanently delete the task." @cancel="showConfirm = false" @confirm="handleDelete" />
    </DashboardLayout>
</template>

<script setup>
import { computed, ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from '../plugins/axios';
import { useAuthStore } from '../stores/auth';
import { useDashboardStore } from '../stores/dashboard';
import DashboardLayout from '../layouts/DashboardLayout.vue';
import TaskModal from '../components/TaskModal.vue';
import ConfirmModal from '../components/ConfirmModal.vue';

const route = useRoute();
const router = useRouter();
const auth = useAuthStore();
const dashboard = useDashboardStore();
const task = ref(null);
const users = ref([]);
const newComment = ref('');
const showModal = ref(false);
const showConfirm = ref(false);
const isEdit = ref(true);
const selectedTask = ref(null);

const comments = computed(() => task.value?.comments || []);
const attachments = [
    { name: 'design-mockup.fig', size: '2.4 MB' },
    { name: 'auth-flow-diagram.png', size: '840 KB' },
    { name: 'bug-report.pdf', size: '156 KB' },
];
const createdDate = computed(() => task.value?.created_at?.slice(0, 10) || 'N/A');
const formatStatus = (s) => ({ todo: 'Todo', in_progress: 'In Progress', completed: 'Done' }[s] || s);
const statusClass = (s) =>
    ({
        todo: 'bg-slate-100 text-slate-600',
        in_progress: 'bg-blue-50 text-blue-600 ring-1 ring-blue-200',
        completed: 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200',
    })[s] || 'bg-slate-100 text-slate-700';
const priorityClass = (p) =>
    ({
        low: 'bg-slate-100 text-slate-600',
        medium: 'bg-orange-50 text-orange-600 ring-1 ring-orange-200',
        high: 'bg-orange-50 text-orange-600 ring-1 ring-orange-200',
    })[p] || 'bg-slate-100 text-slate-700';
const priorityLabel = (p) => ({ low: 'Low', medium: 'Medium', high: 'High' }[p] || p);
const taskCode = (item) => `TF-${String(item.id).padStart(3, '0')}`;
const tagsFor = (item) => item.tags || [];
const userInitials = (name = 'User') =>
    name
        .split(' ')
        .map((part) => part[0])
        .join('')
        .slice(0, 2)
        .toUpperCase();
const assigneeInitials = (item) =>
    userInitials(item.assignee || 'Unassigned');
const formatDateTime = (value) => (value ? new Date(value.replace(' ', 'T')).toLocaleString() : '');

const fetchTask = async () => {
    const { data } = await axios.get(`/tasks/${route.params.id}`);
    task.value = data.data;
    selectedTask.value = data.data;
};

const fetchUsers = async () => {
    const { data } = await axios.get('/users');
    users.value = data.data;
};

const openEdit = () => {
    selectedTask.value = task.value;
    isEdit.value = true;
    showModal.value = true;
};

const openCreate = () => {
    selectedTask.value = null;
    isEdit.value = false;
    showModal.value = true;
};

const handleSave = async (formData) => {
    if (isEdit.value) {
        await axios.put(`/tasks/${selectedTask.value.id}`, formData);
        showModal.value = false;
        await Promise.all([fetchTask(), dashboard.refreshSidebarData()]);
        return;
    }

    await axios.post('/tasks', formData);
    showModal.value = false;
    await dashboard.refreshSidebarData();
};

const addComment = async () => {
    if (!newComment.value.trim()) return;

    const { data } = await axios.post(`/tasks/${task.value.id}/comments`, { body: newComment.value.trim() });
    task.value = data.data;
    selectedTask.value = data.data;
    newComment.value = '';
};

const handleDelete = async () => {
    await axios.delete(`/tasks/${task.value.id}`);
    await dashboard.refreshSidebarData();
    router.push('/dashboard');
};

onMounted(() => {
    fetchTask();
    fetchUsers();
});
</script>
