<template>
    <div v-if="show" class="fixed inset-0 bg-black/50 flex items-center justify-center p-4 z-50">
        <div class="bg-white rounded-lg max-w-sm w-full p-6 shadow-xl">
            <h3 class="text-lg font-bold mb-2">{{ title }}</h3>
            <p class="text-sm text-gray-600 mb-6">{{ message }}</p>
            <div class="flex justify-end gap-2">
                <button @click="$emit('cancel')" class="px-4 py-2 border rounded">Cancel</button>
                <button @click="$emit('confirm')" class="px-4 py-2 bg-red-600 text-white rounded">Delete</button>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({ show: Boolean, title: { type: String, default: 'Are you sure?' }, message: { type: String, default: 'This action cannot be undone.' } });
defineEmits(['confirm', 'cancel']);
</script>
