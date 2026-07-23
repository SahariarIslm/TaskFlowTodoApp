<template>
    <DashboardLayout>
        <template #header>Task Details</template>

        <router-link to="/dashboard" class="mb-5 inline-flex items-center gap-2 text-sm font-semibold text-[#4f39f6] hover:text-[#432dd7]">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="m15 18-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            Back to Dashboard
        </router-link>

        <div v-if="task" class="grid gap-6 xl:grid-cols-[1fr_360px]">
            <section class="tf-panel p-6 lg:p-8">
                <div class="flex flex-col gap-4 border-b border-slate-100 pb-6 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <div class="mb-3 flex flex-wrap gap-2">
                            <span class="tf-chip" :class="statusClass(task.status)">{{ formatStatus(task.status) }}</span>
                            <span class="tf-chip capitalize" :class="priorityClass(task.priority)">{{ task.priority }}</span>
                        </div>
                        <h2 class="text-3xl font-bold leading-tight text-[#0f172b]">{{ task.title }}</h2>
                    </div>
                    <div class="flex gap-2">
                        <button @click="showModal = true" class="tf-secondary-btn">
                            Edit
                        </button>
                        <button @click="showConfirm = true" class="h-11 rounded-2xl px-5 text-sm font-semibold text-rose-600 hover:bg-rose-50">
                            Delete
                        </button>
                    </div>
                </div>

                <div class="pt-6">
                    <h3 class="text-sm font-semibold uppercase tracking-[0.08em] text-slate-400">Description</h3>
                    <p class="mt-3 whitespace-pre-line text-base leading-8 text-slate-600">{{ task.description || 'No description provided.' }}</p>
                </div>
            </section>

            <aside class="tf-panel p-6">
                <h3 class="text-lg font-bold text-[#0f172b]">Task Summary</h3>
                <dl class="mt-5 grid gap-4 text-sm">
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <dt class="text-xs font-semibold uppercase tracking-[0.08em] text-slate-400">Status</dt>
                        <dd class="mt-2 font-semibold text-slate-800">{{ formatStatus(task.status) }}</dd>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <dt class="text-xs font-semibold uppercase tracking-[0.08em] text-slate-400">Priority</dt>
                        <dd class="mt-2 font-semibold capitalize text-slate-800">{{ task.priority }}</dd>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <dt class="text-xs font-semibold uppercase tracking-[0.08em] text-slate-400">Due Date</dt>
                        <dd class="mt-2 font-semibold text-slate-800">{{ task.due_date || 'N/A' }}</dd>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <dt class="text-xs font-semibold uppercase tracking-[0.08em] text-slate-400">Assignee</dt>
                        <dd class="mt-2 font-semibold text-slate-800">{{ task.assignee || 'Unassigned' }}</dd>
                    </div>
                </dl>
            </aside>
        </div>

        <div v-else class="tf-panel p-8 text-sm text-slate-500">
            Loading task details...
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
