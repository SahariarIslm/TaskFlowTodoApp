<template>
    <div class="tf-auth-shell grid lg:grid-cols-2">
        <div class="tf-auth-art relative hidden min-h-screen overflow-hidden p-12 text-white lg:flex lg:flex-col lg:justify-between">
            <router-link to="/" class="relative z-10 flex items-center gap-3">
                <span class="tf-logo tf-logo-light">TF</span>
                <span class="text-xl font-bold tracking-normal">TaskFlow</span>
            </router-link>

            <div class="relative z-10 max-w-3xl">
                <p class="text-xl font-medium leading-8 text-white/90">
                    "TaskFlow completely transformed how our team collaborates. We ship 40% faster now."
                </p>
                <div class="mt-4 flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-white/20 text-sm font-bold">MR</div>
                    <div>
                        <p class="text-sm text-white">Maya Rodriguez</p>
                        <p class="text-xs text-white/60">CTO, Meridian Labs</p>
                    </div>
                </div>
            </div>

            <div class="relative z-10 grid grid-cols-3 gap-4 text-center">
                <div>
                    <p class="text-xl font-bold">10K+</p>
                    <p class="text-xs text-white/60">Teams</p>
                </div>
                <div>
                    <p class="text-xl font-bold">99.9%</p>
                    <p class="text-xs text-white/60">Uptime</p>
                </div>
                <div>
                    <p class="text-xl font-bold">4.9/5</p>
                    <p class="text-xs text-white/60">Rating</p>
                </div>
            </div>
        </div>
        <div class="flex min-h-screen items-center justify-center p-6 sm:p-8">
            <div class="w-full max-w-md">
                <slot />
            </div>
        </div>
    </div>
</template>
