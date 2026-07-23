<template>
    <DashboardLayout>
        <template #header>{{ isSettings ? 'Settings' : 'Profile' }}</template>
        <template #subtitle>{{ isSettings ? 'Update your profile details' : 'Your account and task summary' }}</template>

        <div class="mx-auto grid max-w-[980px] gap-6 xl:grid-cols-[320px_1fr]">
            <aside class="tf-panel p-6">
                <div class="flex flex-col items-center text-center">
                    <img v-if="auth.user?.avatar" :src="auth.user.avatar" alt="" class="h-20 w-20 rounded-full object-cover" />
                    <span v-else class="flex h-20 w-20 items-center justify-center rounded-full bg-violet-100 text-xl font-bold text-[#4f39f6]">{{ initials }}</span>
                    <h2 class="mt-4 text-xl font-bold text-[#0f172b]">{{ auth.user?.name }}</h2>
                    <p class="mt-1 text-sm text-slate-500">{{ auth.user?.email }}</p>
                </div>

                <dl class="mt-6 grid gap-3 text-sm">
                    <div class="flex items-center justify-between rounded-xl bg-slate-50 px-4 py-3">
                        <dt class="font-medium text-slate-500">Total Tasks</dt>
                        <dd class="font-bold text-slate-800">{{ stats.total_tasks }}</dd>
                    </div>
                    <div class="flex items-center justify-between rounded-xl bg-slate-50 px-4 py-3">
                        <dt class="font-medium text-slate-500">In Progress</dt>
                        <dd class="font-bold text-slate-800">{{ stats.in_progress }}</dd>
                    </div>
                    <div class="flex items-center justify-between rounded-xl bg-slate-50 px-4 py-3">
                        <dt class="font-medium text-slate-500">Completed</dt>
                        <dd class="font-bold text-slate-800">{{ stats.completed }}</dd>
                    </div>
                </dl>
            </aside>

            <section class="tf-panel p-6">
                <div class="mb-5">
                    <h3 class="text-lg font-bold text-[#0f172b]">Profile Details</h3>
                    <p class="mt-1 text-sm text-slate-500">Keep your account information current.</p>
                </div>

                <p v-if="successMsg" class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ successMsg }}</p>
                <p v-if="errorMsg" class="mb-4 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">{{ errorMsg }}</p>

                <form @submit.prevent="submit" class="space-y-5">
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="tf-label">Full Name</label>
                            <input v-model="form.name" required class="tf-input" placeholder="Your name" />
                        </div>
                        <div>
                            <label class="tf-label">Email Address</label>
                            <input v-model="form.email" type="email" required class="tf-input" placeholder="you@company.com" />
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="tf-label">New Password</label>
                            <input v-model="form.password" type="password" class="tf-input" placeholder="Leave blank to keep current" />
                        </div>
                        <div>
                            <label class="tf-label">Confirm Password</label>
                            <input v-model="form.password_confirmation" type="password" class="tf-input" placeholder="Repeat new password" />
                        </div>
                    </div>

                    <div class="flex justify-end border-t border-slate-100 pt-5">
                        <button type="submit" :disabled="loading" class="tf-primary-btn">
                            {{ loading ? 'Saving...' : 'Save Changes' }}
                        </button>
                    </div>
                </form>
            </section>
        </div>
    </DashboardLayout>
</template>

<script setup>
import { computed, onMounted, reactive, ref, watch } from 'vue';
import { useRoute } from 'vue-router';
import axios from '../plugins/axios';
import { useAuthStore } from '../stores/auth';
import DashboardLayout from '../layouts/DashboardLayout.vue';

const route = useRoute();
const auth = useAuthStore();
const loading = ref(false);
const successMsg = ref('');
const errorMsg = ref('');
const stats = reactive({ total_tasks: 0, in_progress: 0, completed: 0 });
const form = reactive({ name: '', email: '', password: '', password_confirmation: '' });

const isSettings = computed(() => route.name === 'settings');
const initials = computed(() =>
    (auth.user?.name || 'User')
        .split(' ')
        .map((part) => part[0])
        .join('')
        .slice(0, 2)
        .toUpperCase()
);

const syncForm = () => {
    form.name = auth.user?.name || '';
    form.email = auth.user?.email || '';
    form.password = '';
    form.password_confirmation = '';
};

const fetchStats = async () => {
    const { data } = await axios.get('/dashboard/stats');
    Object.assign(stats, data);
};

const submit = async () => {
    loading.value = true;
    successMsg.value = '';
    errorMsg.value = '';

    try {
        const { data } = await axios.put('/auth/profile', form);
        auth.updateUser(data.data);
        syncForm();
        successMsg.value = 'Profile updated successfully.';
    } catch (error) {
        errorMsg.value = error.response?.data?.message || 'Could not update profile.';
    } finally {
        loading.value = false;
    }
};

watch(() => auth.user, syncForm, { immediate: true });

onMounted(fetchStats);
</script>
