<template>
    <main class="flex min-h-screen items-center justify-center bg-white px-6 py-12">
        <section class="w-full max-w-lg text-center">
            <div class="mx-auto mb-6 flex justify-center">
                <span class="tf-logo h-16 w-16 rounded-[24px] text-xl">TF</span>
            </div>
            <h1 class="text-4xl font-bold leading-10 text-[#0f172b] sm:text-5xl sm:leading-[1.05]">Welcome to TaskFlow</h1>
            <p class="mt-4 text-base leading-7 text-slate-500">
                The modern task management platform designed for high-performance teams.
                Ship faster, collaborate smarter, stay organized.
            </p>

            <div class="mt-10 grid gap-4">
                <router-link to="/register" class="tf-primary-btn w-full">Get started for free</router-link>
                <router-link to="/login" class="tf-secondary-btn w-full">Sign in to your account</router-link>
            </div>

            <div class="mt-8 grid grid-cols-1 gap-5 text-center sm:grid-cols-3">
                <div class="flex flex-col items-center gap-2">
                    <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-indigo-50 text-[#4f39f6]">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M8 7h10M8 12h10M8 17h6M4 7h.01M4 12h.01M4 17h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                        </svg>
                    </div>
                    <p class="text-xs font-medium text-slate-500">Smart task tracking</p>
                </div>
                <div class="flex flex-col items-center gap-2">
                    <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M16 11a4 4 0 1 0-8 0M5 20a7 7 0 0 1 14 0M19 8v4M21 10h-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                        </svg>
                    </div>
                    <p class="text-xs font-medium text-slate-500">Team collaboration</p>
                </div>
                <div class="flex flex-col items-center gap-2">
                    <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-amber-50 text-amber-600">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M4 19V5M4 19h16M8 16v-5M12 16V8M16 16v-3" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                        </svg>
                    </div>
                    <p class="text-xs font-medium text-slate-500">Progress analytics</p>
                </div>
            </div>
        </section>
    </main>
</template>
