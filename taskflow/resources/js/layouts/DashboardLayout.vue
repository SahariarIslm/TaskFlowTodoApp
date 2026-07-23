<template>
    <div class="min-h-screen bg-slate-50 text-slate-900 lg:flex">
        <aside class="border-b border-slate-200 bg-white lg:fixed lg:inset-y-0 lg:left-0 lg:w-72 lg:border-b-0 lg:border-r">
            <div class="flex h-full flex-col justify-between p-5 lg:p-6">
                <div>
                    <router-link to="/dashboard" class="flex items-center gap-3">
                        <span class="tf-logo">TF</span>
                        <span class="text-xl font-bold tracking-normal text-[#0f172b]">TaskFlow</span>
                    </router-link>

                    <nav class="mt-8 grid gap-2">
                        <router-link
                            to="/dashboard"
                            class="flex h-11 items-center gap-3 rounded-2xl px-4 text-sm font-semibold text-slate-600 transition hover:bg-slate-100"
                            active-class="bg-[#4f39f6] text-white hover:bg-[#4f39f6]"
                        >
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M4 13h7V4H4v9Zm9 7h7V4h-7v16ZM4 20h7v-5H4v5Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round" />
                            </svg>
                            Dashboard
                        </router-link>
                    </nav>
                </div>

                <div class="mt-6 hidden rounded-[24px] bg-slate-50 p-4 lg:block">
                    <p class="text-xs font-semibold uppercase text-slate-400">Signed in as</p>
                    <p class="mt-1 truncate text-sm font-semibold text-slate-800">{{ auth.user?.name }}</p>
                    <p class="truncate text-xs text-slate-500">{{ auth.user?.email }}</p>
                    <button @click="handleLogout" class="mt-4 inline-flex text-sm font-semibold text-rose-600 hover:text-rose-700">
                        Logout
                    </button>
                </div>

                <div class="mt-5 flex items-center justify-between border-t border-slate-100 pt-4 lg:hidden">
                    <div>
                        <p class="text-sm font-semibold text-slate-800">{{ auth.user?.name }}</p>
                        <p class="text-xs text-slate-500">{{ auth.user?.email }}</p>
                    </div>
                    <button @click="handleLogout" class="text-sm font-semibold text-rose-600">Logout</button>
                </div>
            </div>
        </aside>

        <div class="min-h-screen flex-1 lg:pl-72">
            <header class="sticky top-0 z-20 border-b border-slate-200 bg-white/90 px-5 py-4 backdrop-blur lg:px-8">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.08em] text-slate-400">Workspace</p>
                        <h1 class="mt-1 text-2xl font-bold text-[#0f172b]"><slot name="header">Dashboard</slot></h1>
                    </div>
                    <div class="hidden h-11 items-center gap-3 rounded-2xl border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 sm:flex">
                        <span class="flex h-7 w-7 items-center justify-center rounded-full bg-[#4f39f6] text-xs font-bold text-white">
                            {{ initials }}
                        </span>
                        {{ auth.user?.name || 'User' }}
                    </div>
                </div>
            </header>
            <main class="p-5 lg:p-8">
                <slot />
            </main>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { useAuthStore } from '../stores/auth';
import { useRouter } from 'vue-router';

const auth = useAuthStore();
const router = useRouter();
const initials = computed(() =>
    (auth.user?.name || 'User')
        .split(' ')
        .map((part) => part[0])
        .join('')
        .slice(0, 2)
        .toUpperCase()
);

const handleLogout = async () => {
    await auth.logout();
    router.push('/login');
};
</script>
