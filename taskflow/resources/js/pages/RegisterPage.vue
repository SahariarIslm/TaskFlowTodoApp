<template>
    <GuestLayout>
        <div class="mb-8">
            <router-link to="/" class="mb-8 inline-flex items-center gap-3 lg:hidden">
                <span class="tf-logo">TF</span>
                <span class="text-xl font-bold text-slate-950">TaskFlow</span>
            </router-link>
            <h2 class="text-2xl font-bold text-[#0f172b]">Create your account</h2>
            <p class="mt-1 text-sm text-slate-500">Start your workspace and verify it with a one-time code</p>
        </div>

        <p v-if="errorMsg" class="mb-4 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">{{ errorMsg }}</p>
        <form @submit.prevent="submit" class="space-y-5">
            <div>
                <label class="tf-label">Full Name</label>
                <input v-model="form.name" type="text" required class="tf-input pl-10" placeholder="Alex Morgan" />
            </div>
            <div>
                <label class="tf-label">Email Address</label>
                <input v-model="form.email" type="email" required class="tf-input pl-10" placeholder="you@company.com" />
            </div>
            <div>
                <label class="tf-label">Password</label>
                <input v-model="form.password" type="password" required class="tf-input pl-10" placeholder="Min. 8 characters" />
            </div>
            <div>
                <label class="tf-label">Confirm Password</label>
                <input v-model="form.password_confirmation" type="password" required class="tf-input pl-10" placeholder="Repeat password" />
            </div>

            <div class="grid grid-cols-4 gap-2" aria-hidden="true">
                <span class="h-1.5 rounded-full bg-[#4f39f6]"></span>
                <span class="h-1.5 rounded-full bg-slate-200"></span>
                <span class="h-1.5 rounded-full bg-slate-200"></span>
                <span class="h-1.5 rounded-full bg-slate-200"></span>
            </div>

            <button type="submit" :disabled="loading" class="tf-primary-btn w-full">
                {{ loading ? 'Please wait...' : 'Create account' }}
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-slate-500">
            Already have an account?
            <router-link to="/login" class="font-semibold text-[#4f39f6]">Sign in</router-link>
        </p>
    </GuestLayout>
</template>

<script setup>
import { reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import axios from '../plugins/axios';
import GuestLayout from '../layouts/GuestLayout.vue';

const router = useRouter();
const loading = ref(false);
const errorMsg = ref('');
const form = reactive({ name: '', email: '', password: '', password_confirmation: '' });

const submit = async () => {
    loading.value = true;
    errorMsg.value = '';
    try {
        await axios.post('/auth/register', form);
        sessionStorage.setItem('pendingEmail', form.email);
        router.push('/verify-otp');
    } catch (error) {
        errorMsg.value = error.response?.data?.message || 'Registration failed.';
    } finally {
        loading.value = false;
    }
};
</script>
