<template>
    <div class="min-h-screen flex">
        <div class="hidden lg:flex w-1/2 bg-indigo-600 text-white p-12 flex-col justify-between">
            <h1 class="text-4xl font-bold">TaskFlow</h1>
            <p class="text-indigo-200">Manage your task workflows with total ease and control.</p>
        </div>
        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 bg-gray-50">
            <div class="w-full max-w-md bg-white p-8 rounded-lg shadow-md">
                <slot />
            </div>
        </div>
    </div>
</template>
