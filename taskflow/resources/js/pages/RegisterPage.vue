<template>
    <GuestLayout>
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Create Account</h2>
        <p v-if="errorMsg" class="text-red-600 text-sm mb-4">{{ errorMsg }}</p>
        <form @submit.prevent="submit" class="space-y-4">
            <div>
                <label class="block text-sm font-medium">Name</label>
                <input v-model="form.name" type="text" required class="w-full border rounded px-3 py-2 mt-1" />
            </div>
            <div>
                <label class="block text-sm font-medium">Email</label>
                <input v-model="form.email" type="email" required class="w-full border rounded px-3 py-2 mt-1" />
            </div>
            <div>
                <label class="block text-sm font-medium">Password</label>
                <input v-model="form.password" type="password" required class="w-full border rounded px-3 py-2 mt-1" />
            </div>
            <div>
                <label class="block text-sm font-medium">Confirm Password</label>
                <input v-model="form.password_confirmation" type="password" required class="w-full border rounded px-3 py-2 mt-1" />
            </div>
            <button type="submit" :disabled="loading" class="w-full bg-indigo-600 text-white py-2 rounded hover:bg-indigo-700 disabled:opacity-50">
                {{ loading ? 'Please wait...' : 'Register' }}
            </button>
        </form>
        <p class="mt-4 text-sm text-center">
            Already have an account? <router-link to="/login" class="text-indigo-600">Login</router-link>
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
