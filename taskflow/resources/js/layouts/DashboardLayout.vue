<template>
    <div class="min-h-screen bg-[#f8fafc] text-slate-900 lg:flex">
        <aside class="border-b border-slate-200 bg-white lg:fixed lg:inset-y-0 lg:left-0 lg:w-64 lg:border-b-0 lg:border-r">
            <div class="flex h-full flex-col justify-between">
                <div>
                    <div class="flex h-[70px] items-center justify-between border-b border-slate-200 px-5">
                        <router-link to="/dashboard" class="flex items-center gap-3">
                            <span class="tf-logo">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="m13 2-9 11h7l-1 9 9-12h-7l1-8Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round" />
                                </svg>
                            </span>
                            <span class="text-base font-bold tracking-normal text-[#0f172b]">TaskFlow</span>
                        </router-link>
                        <button class="flex h-7 w-7 items-center justify-center rounded-full bg-slate-100 text-slate-400" type="button" aria-label="Collapse sidebar">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="m15 18-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>
                    </div>

                    <nav class="grid gap-1 px-3 py-4 text-sm">
                        <router-link v-for="item in menuItems" :key="item.label" :to="item.to" class="flex h-11 items-center gap-3 rounded-xl px-3 font-medium text-slate-600 transition hover:bg-slate-100" active-class="bg-[#4f39f6] text-white hover:bg-[#4f39f6]">
                            <span v-html="item.icon" class="tf-sidebar-icon flex h-4 w-4 items-center justify-center"></span>
                            <span class="flex-1">{{ item.label }}</span>
                            <span v-if="item.badge !== undefined" class="rounded-full bg-slate-200 px-2 py-0.5 text-xs font-bold text-slate-500">{{ item.badge }}</span>
                        </router-link>
                    </nav>

                    <div class="px-5 pt-2">
                        <p class="mb-3 text-xs font-semibold uppercase tracking-[0.14em] text-slate-400">Projects</p>
                        <div class="grid gap-3 text-sm">
                            <div v-for="project in projects" :key="project.name" class="flex items-center gap-3 text-slate-600">
                                <span class="h-2.5 w-2.5 rounded-full" :class="project.color"></span>
                                <span class="flex-1 font-medium">{{ project.name }}</span>
                                <span class="text-xs text-slate-400">{{ project.count }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="relative border-t border-slate-200 p-5">
                    <div v-if="showAccountMenu" class="absolute bottom-[72px] right-4 z-30 w-44 rounded-xl border border-slate-200 bg-white p-2 shadow-[0_18px_45px_rgba(15,23,42,0.16)]">
                        <router-link to="/profile" @click="showAccountMenu = false" class="flex h-9 items-center rounded-lg px-3 text-sm font-medium text-slate-600 hover:bg-slate-100">View profile</router-link>
                        <router-link to="/settings" @click="showAccountMenu = false" class="flex h-9 items-center rounded-lg px-3 text-sm font-medium text-slate-600 hover:bg-slate-100">Edit profile</router-link>
                        <button @click="handleLogout" class="flex h-9 w-full items-center rounded-lg px-3 text-left text-sm font-semibold text-rose-600 hover:bg-rose-50" type="button">Logout</button>
                    </div>
                    <div class="flex items-center gap-3">
                        <img v-if="auth.user?.avatar" :src="auth.user.avatar" alt="" class="h-9 w-9 rounded-full object-cover" />
                        <span v-else class="flex h-9 w-9 items-center justify-center rounded-full bg-violet-100 text-xs font-bold text-[#4f39f6]">{{ initials }}</span>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-bold text-slate-800">{{ auth.user?.name || 'Alex Morgan' }}</p>
                            <p class="truncate text-xs text-slate-500">{{ auth.user?.email || 'alex@taskflow.io' }}</p>
                        </div>
                        <button @click="showAccountMenu = !showAccountMenu" class="text-slate-400 hover:text-[#4f39f6]" type="button" aria-label="Account menu">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M12 12h.01M18 12h.01M6 12h.01" stroke="currentColor" stroke-width="3" stroke-linecap="round" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </aside>

        <div class="min-h-screen flex-1 lg:pl-64">
            <header class="sticky top-0 z-20 border-b border-slate-200 bg-white px-5 lg:px-7">
                <div class="flex h-[70px] items-center justify-between gap-4">
                    <div class="min-w-0">
                        <h1 class="truncate text-lg font-bold leading-5 text-[#0f172b]"><slot name="header">Dashboard</slot></h1>
                        <p class="truncate text-xs text-slate-500"><slot name="subtitle">Good morning, Alex - here's what's happening</slot></p>
                    </div>
                    <div class="flex min-w-0 flex-1 items-center justify-end gap-3">
                        <div class="hidden h-10 w-full max-w-[280px] items-center rounded-2xl bg-slate-100 px-4 text-sm text-slate-400 md:flex">
                            <svg class="mr-2 h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="m21 21-4.3-4.3M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                            </svg>
                            <input class="min-w-0 flex-1 border-0 bg-transparent text-sm outline-none placeholder:text-slate-400" placeholder="Search tasks..." />
                        </div>
                        <button @click="emit('new-task')" type="button" class="tf-primary-btn shrink-0">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                            </svg>
                            New Task
                        </button>
                        <button type="button" class="relative flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-slate-100 text-slate-600" aria-label="Notifications">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M18 8a6 6 0 1 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9ZM13.7 21a2 2 0 0 1-3.4 0" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                            </svg>
                            <span class="absolute right-2 top-2 h-2 w-2 rounded-full bg-[#4f39f6]"></span>
                        </button>
                        <img v-if="auth.user?.avatar" :src="auth.user.avatar" alt="" class="h-10 w-10 shrink-0 rounded-full object-cover" />
                        <span v-else class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-violet-100 text-xs font-bold text-[#4f39f6]">{{ initials }}</span>
                    </div>
                </div>
            </header>
            <main class="p-5 lg:p-7">
                <slot />
            </main>
        </div>
    </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import { storeToRefs } from 'pinia';
import { useAuthStore } from '../stores/auth';
import { useDashboardStore } from '../stores/dashboard';
import { useRouter } from 'vue-router';

const auth = useAuthStore();
const dashboard = useDashboardStore();
const router = useRouter();
const emit = defineEmits(['new-task']);
const { projects, totalTasks } = storeToRefs(dashboard);
const showAccountMenu = ref(false);
const initials = computed(() =>
    (auth.user?.name || 'Alex Morgan')
        .split(' ')
        .map((part) => part[0])
        .join('')
        .slice(0, 2)
        .toUpperCase()
);
const menuItems = computed(() => [
    {
        label: 'Dashboard',
        to: '/dashboard',
        icon: '<svg viewBox="0 0 24 24" fill="none"><path d="M4 13h7V4H4v9Zm9 7h7V4h-7v16ZM4 20h7v-5H4v5Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/></svg>',
    },
    {
        label: 'My Tasks',
        to: '/my-tasks',
        badge: totalTasks.value,
        icon: '<svg viewBox="0 0 24 24" fill="none"><path d="m9 11 2 2 4-5M5 5h14v14H5V5Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
    },
    {
        label: 'Profile',
        to: '/profile',
        icon: '<svg viewBox="0 0 24 24" fill="none"><path d="M16 18a4 4 0 0 0-8 0M12 14a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>',
    },
    {
        label: 'Settings',
        to: '/settings',
        icon: '<svg viewBox="0 0 24 24" fill="none"><path d="M12 15.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Z" stroke="currentColor" stroke-width="2"/><path d="M19.4 15a1.7 1.7 0 0 0 .3 1.9l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-1.9-.3 1.7 1.7 0 0 0-1 1.6V21a2 2 0 1 1-4 0v-.1a1.7 1.7 0 0 0-1-1.6 1.7 1.7 0 0 0-1.9.3l-.1.1A2 2 0 1 1 4.2 17l.1-.1a1.7 1.7 0 0 0 .3-1.9 1.7 1.7 0 0 0-1.6-1H3a2 2 0 1 1 0-4h.1a1.7 1.7 0 0 0 1.6-1 1.7 1.7 0 0 0-.3-1.9l-.1-.1A2 2 0 1 1 7 4.2l.1.1a1.7 1.7 0 0 0 1.9.3h.1a1.7 1.7 0 0 0 1-1.6V3a2 2 0 1 1 4 0v.1a1.7 1.7 0 0 0 1 1.6 1.7 1.7 0 0 0 1.9-.3l.1-.1A2 2 0 1 1 19.8 7l-.1.1a1.7 1.7 0 0 0-.3 1.9v.1a1.7 1.7 0 0 0 1.6 1h.1a2 2 0 1 1 0 4H21a1.7 1.7 0 0 0-1.6 1Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>',
    },
]);
const handleLogout = async () => {
    showAccountMenu.value = false;
    await auth.logout();
    router.push('/login');
};

onMounted(() => {
    dashboard.refreshSidebarData();
});
</script>
