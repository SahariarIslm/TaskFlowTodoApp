<template>
    <GuestLayout>
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Welcome Back</h2>
        <p v-if="errorMsg" class="text-red-600 text-sm mb-4">{{ errorMsg }}</p>
        <form @submit.prevent="submit" class="space-y-4">
            <div>
                <label class="block text-sm font-medium">Email</label>
                <input v-model="form.email" type="email" required class="w-full border rounded px-3 py-2 mt-1" />
            </div>
            <div>
                <label class="block text-sm font-medium">Password</label>
                <input v-model="form.password" type="password" required class="w-full border rounded px-3 py-2 mt-1" />
            </div>
            <button type="submit" :disabled="loading" class="w-full bg-indigo-600 text-white py-2 rounded hover:bg-indigo-700 disabled:opacity-50">
                {{ loading ? 'Signing in...' : 'Login' }}
            </button>
        </form>
        <p class="mt-4 text-sm text-center">
            Don't have an account? <router-link to="/register" class="text-indigo-600">Register</router-link>
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
