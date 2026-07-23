<template>
    <GuestLayout>
        <div class="text-center">
            <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-violet-100 text-lg font-bold text-[#4f39f6]">G</span>
            <h2 class="mt-5 text-2xl font-bold text-[#0f172b]">Signing you in</h2>
            <p class="mt-2 text-sm text-slate-500">{{ message }}</p>
        </div>
    </GuestLayout>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from '../plugins/axios';
import { useAuthStore } from '../stores/auth';
import GuestLayout from '../layouts/GuestLayout.vue';

const route = useRoute();
const router = useRouter();
const auth = useAuthStore();
const message = ref('Finishing Google authentication...');

onMounted(async () => {
    const token = route.query.token;

    if (!token) {
        message.value = 'Google login did not return a token.';
        router.push('/login');
        return;
    }

    localStorage.setItem('token', token);
    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;

    try {
        const { data } = await axios.get('/auth/me');
        auth.setSession(token, data.data);
        router.push('/dashboard');
    } catch (error) {
        message.value = 'Could not finish Google login.';
        router.push('/login');
    }
});
</script>
