<template>
    <GuestLayout>
        <div class="mb-8">
            <router-link to="/" class="mb-8 inline-flex items-center gap-3 lg:hidden">
                <span class="tf-logo">TF</span>
                <span class="text-xl font-bold text-slate-950">TaskFlow</span>
            </router-link>
            <h2 class="text-2xl font-bold text-[#0f172b]">Sign in to TaskFlow</h2>
            <p class="mt-1 text-sm text-slate-500">Enter your credentials to access your workspace</p>
        </div>

        <p v-if="errorMsg" class="mb-4 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">{{ errorMsg }}</p>
        <form @submit.prevent="submit" class="space-y-5">
            <div>
                <label class="tf-label">Email Address</label>
                <input v-model="form.email" type="email" required class="tf-input pl-10" placeholder="you@company.com" />
            </div>
            <div>
                <div class="mb-1.5 flex items-center justify-between">
                    <label class="block text-xs font-medium text-slate-700">Password</label>
                    <span class="text-xs font-semibold text-[#4f39f6]">Forgot Password?</span>
                </div>
                <input v-model="form.password" type="password" required class="tf-input pl-10" placeholder="********" />
            </div>
            <button type="submit" :disabled="loading" class="tf-primary-btn w-full">
                {{ loading ? 'Signing in...' : 'Sign in' }}
            </button>
        </form>

        <div class="my-6 flex items-center gap-3">
            <div class="h-px flex-1 bg-slate-200"></div>
            <span class="text-xs text-slate-400">or continue with</span>
            <div class="h-px flex-1 bg-slate-200"></div>
        </div>

        <button type="button" class="tf-secondary-btn w-full">
            <span class="font-bold text-[#4285f4]">G</span>
            Continue with Google
        </button>

        <p class="mt-6 text-center text-sm text-slate-500">
            Don't have an account?
            <router-link to="/register" class="font-semibold text-[#4f39f6]">Create account</router-link>
        </p>
    </GuestLayout>
</template>

<script setup>
import { reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import axios from '../plugins/axios';
import GuestLayout from '../layouts/GuestLayout.vue';

const router = useRouter();
const auth = useAuthStore();
const loading = ref(false);
const errorMsg = ref('');
const form = reactive({ email: '', password: '' });

const submit = async () => {
    loading.value = true;
    errorMsg.value = '';
    try {
        const { data } = await axios.post('/auth/login', form);
        auth.setSession(data.token, data.user);
        router.push('/dashboard');
    } catch (error) {
        if (error.response?.status === 403 && error.response.data.requires_otp) {
            sessionStorage.setItem('pendingEmail', form.email);
            router.push('/verify-otp');
            return;
        }
        errorMsg.value = error.response?.data?.message || 'Login failed.';
    } finally {
        loading.value = false;
    }
};
</script>
