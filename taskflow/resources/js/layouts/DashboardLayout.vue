<template>
    <div class="flex h-screen bg-gray-100">
        <aside class="w-64 bg-slate-900 text-white flex flex-col justify-between">
            <div class="p-6">
                <h2 class="text-2xl font-bold">TaskFlow</h2>
                <nav class="mt-8 space-y-2">
                    <router-link to="/dashboard" class="block px-4 py-2 rounded hover:bg-slate-800">Dashboard</router-link>
                </nav>
            </div>
            <div class="p-4 border-t border-slate-800">
                <button @click="handleLogout" class="w-full text-left px-4 py-2 rounded text-red-400 hover:bg-slate-800">
                    Logout
                </button>
            </div>
        </aside>
        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="bg-white border-b h-16 flex items-center justify-between px-8">
                <h1 class="text-xl font-semibold text-gray-800"><slot name="header">Dashboard</slot></h1>
                <div class="text-sm font-medium text-gray-600">{{ auth.user?.name }}</div>
            </header>
            <main class="flex-1 overflow-y-auto p-8">
                <slot />
            </main>
        </div>
    </div>
</template>

<script setup>
import { useAuthStore } from '../stores/auth';
import { useRouter } from 'vue-router';

const auth = useAuthStore();
const router = useRouter();

const handleLogout = async () => {
    await auth.logout();
    router.push('/login');
};
</script>
