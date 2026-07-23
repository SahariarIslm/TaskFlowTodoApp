<template>
    <GuestLayout>
        <div class="mb-8">
            <router-link to="/" class="mb-8 inline-flex items-center gap-3 lg:hidden">
                <span class="tf-logo">TF</span>
                <span class="text-xl font-bold text-slate-950">TaskFlow</span>
            </router-link>
            <h2 class="text-2xl font-bold text-[#0f172b]">Check your email</h2>
            <p class="mt-1 text-sm leading-5 text-slate-500">We sent a 6-digit verification code to {{ email || 'your email' }}</p>
        </div>

        <p
            v-if="message"
            class="mb-4 rounded-2xl border px-4 py-3 text-sm"
            :class="messageType === 'success' ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-rose-200 bg-rose-50 text-rose-700'"
        >
            {{ message }}
        </p>

        <form @submit.prevent="verify">
            <div class="flex justify-center gap-3 sm:gap-4">
                <input
                    v-for="(_, index) in otpParts"
                    :key="index"
                    ref="otpInputs"
                    v-model="otpParts[index]"
                    type="text"
                    maxlength="1"
                    inputmode="numeric"
                    required
                    class="h-14 w-12 rounded-xl border-2 border-slate-200 bg-white text-center text-xl font-semibold text-slate-900 transition focus:border-[#4f39f6] focus:ring-4 focus:ring-[#4f39f6]/10"
                    @input="handleDigit(index)"
                    @keydown.backspace="handleBackspace(index, $event)"
                    @paste.prevent="handlePaste"
                />
            </div>

            <button type="submit" :disabled="loading || otp.length < 6" class="tf-primary-btn mt-8 w-full">
                {{ loading ? 'Verifying...' : 'Verify & continue' }}
            </button>
        </form>

        <div class="mt-6 text-center">
            <p class="text-xs text-slate-500">Didn't receive the code?</p>
            <button @click="resend" type="button" class="mt-2 inline-flex items-center gap-2 border-0 bg-transparent text-sm font-semibold text-[#4f39f6] hover:text-[#432dd7]">
                Resend code
            </button>
        </div>

        <div class="mt-5 border-t border-slate-200 pt-5 text-center">
            <router-link to="/register" class="inline-flex items-center gap-2 text-sm font-semibold text-[#4f39f6]">
                Back to register
            </router-link>
        </div>
    </GuestLayout>
</template>

<script setup>
import { computed, ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import axios from '../plugins/axios';
import GuestLayout from '../layouts/GuestLayout.vue';

const otpParts = ref(['', '', '', '', '', '']);
const otpInputs = ref([]);
const otp = computed(() => otpParts.value.join(''));
const loading = ref(false);
const message = ref('');
const messageType = ref('error');
const router = useRouter();
const auth = useAuthStore();
const email = sessionStorage.getItem('pendingEmail');

const focusInput = (index) => {
    otpInputs.value[index]?.focus();
};

const handleDigit = (index) => {
    otpParts.value[index] = otpParts.value[index].replace(/\D/g, '').slice(-1);
    if (otpParts.value[index] && index < otpParts.value.length - 1) {
        focusInput(index + 1);
    }
};

const handleBackspace = (index, event) => {
    if (!otpParts.value[index] && index > 0) {
        event.preventDefault();
        focusInput(index - 1);
    }
};

const handlePaste = (event) => {
    const pasted = event.clipboardData.getData('text').replace(/\D/g, '').slice(0, 6).split('');
    pasted.forEach((digit, index) => {
        otpParts.value[index] = digit;
    });
    focusInput(Math.min(pasted.length, 5));
};

const verify = async () => {
    loading.value = true;
    message.value = '';
    try {
        const { data } = await axios.post('/auth/verify-otp', { email, otp: otp.value });
        auth.setSession(data.token, data.user);
        sessionStorage.removeItem('pendingEmail');
        router.push('/dashboard');
    } catch (error) {
        messageType.value = 'error';
        message.value = error.response?.data?.message || 'Verification failed.';
    } finally {
        loading.value = false;
    }
};

const resend = async () => {
    try {
        await axios.post('/auth/resend-otp', { email });
        messageType.value = 'success';
        message.value = 'A new code has been sent.';
    } catch (error) {
        messageType.value = 'error';
        message.value = 'Could not resend code.';
    }
};
</script>
