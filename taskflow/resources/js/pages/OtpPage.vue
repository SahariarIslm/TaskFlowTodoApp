<template>
    <GuestLayout>
        <h2 class="text-2xl font-bold text-gray-800 mb-2">OTP Verification</h2>
        <p class="text-sm text-gray-600 mb-6">Enter the 6-digit code sent to {{ email }}.</p>
        <p v-if="errorMsg" class="text-red-600 text-sm mb-4">{{ errorMsg }}</p>
        <form @submit.prevent="verify" class="space-y-4">
            <input
                v-model="otp"
                type="text"
                maxlength="6"
                inputmode="numeric"
                required
                placeholder="000000"
                class="w-full text-center tracking-widest text-2xl border rounded py-3"
            />
            <button type="submit" :disabled="loading" class="w-full bg-indigo-600 text-white py-2 rounded hover:bg-indigo-700 disabled:opacity-50">
                {{ loading ? 'Verifying...' : 'Verify Code' }}
            </button>
        </form>
        <button @click="resend" class="mt-4 text-sm text-indigo-600 hover:underline">Resend code</button>
    </GuestLayout>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import axios from '../plugins/axios';
import GuestLayout from '../layouts/GuestLayout.vue';

const otp = ref('');
const loading = ref(false);
const errorMsg = ref('');
const router = useRouter();
const auth = useAuthStore();
const email = sessionStorage.getItem('pendingEmail');

const verify = async () => {
    loading.value = true;
    errorMsg.value = '';
    try {
        const { data } = await axios.post('/auth/verify-otp', { email, otp: otp.value });
        auth.setSession(data.token, data.user);
        sessionStorage.removeItem('pendingEmail');
        router.push('/dashboard');
    } catch (error) {
        errorMsg.value = error.response?.data?.message || 'Verification failed.';
    } finally {
        loading.value = false;
    }
};

const resend = async () => {
    try {
        await axios.post('/auth/resend-otp', { email });
        errorMsg.value = 'A new code has been sent.';
    } catch (error) {
        errorMsg.value = 'Could not resend code.';
    }
};
</script>
