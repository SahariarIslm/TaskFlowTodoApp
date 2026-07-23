<template>
    <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/55 p-4 backdrop-blur-sm">
        <div class="tf-panel w-full max-w-sm p-6 shadow-[0_30px_90px_rgba(15,23,42,0.24)]">
            <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-50 text-rose-600">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M12 9v4M12 17h.01M10.3 4.8 2.6 18a2 2 0 0 0 1.7 3h15.4a2 2 0 0 0 1.7-3L13.7 4.8a2 2 0 0 0-3.4 0Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>
            <h3 class="text-xl font-bold text-[#0f172b]">{{ title }}</h3>
            <p class="mt-2 text-sm leading-6 text-slate-500">{{ message }}</p>
            <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                <button @click="$emit('cancel')" class="tf-secondary-btn">Cancel</button>
                <button @click="$emit('confirm')" class="tf-danger-btn">Delete</button>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({ show: Boolean, title: { type: String, default: 'Are you sure?' }, message: { type: String, default: 'This action cannot be undone.' } });
defineEmits(['confirm', 'cancel']);
</script>
