import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const routes = [
    { path: '/', name: 'home', component: () => import('../pages/HomePage.vue') },
    { path: '/login', name: 'login', component: () => import('../pages/LoginPage.vue'), meta: { guestOnly: true } },
    { path: '/register', name: 'register', component: () => import('../pages/RegisterPage.vue'), meta: { guestOnly: true } },
    { path: '/verify-otp', name: 'verify-otp', component: () => import('../pages/OtpPage.vue'), meta: { guestOnly: true } },
    { path: '/auth/google/success', name: 'google-success', component: () => import('../pages/GoogleCallbackPage.vue') },
    { path: '/dashboard', name: 'dashboard', component: () => import('../pages/DashboardPage.vue'), meta: { requiresAuth: true, title: 'Dashboard' } },
    { path: '/my-tasks', name: 'my-tasks', component: () => import('../pages/DashboardPage.vue'), meta: { requiresAuth: true, title: 'My Tasks' } },
    { path: '/profile', name: 'profile', component: () => import('../pages/ProfilePage.vue'), meta: { requiresAuth: true } },
    { path: '/settings', name: 'settings', component: () => import('../pages/ProfilePage.vue'), meta: { requiresAuth: true } },
    { path: '/tasks/:id', name: 'task-details', component: () => import('../pages/TaskDetailsPage.vue'), meta: { requiresAuth: true } },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach((to) => {
    const auth = useAuthStore();

    if (to.meta.requiresAuth && !auth.isAuthenticated) {
        return { name: 'login' };
    }
    if (to.meta.guestOnly && auth.isAuthenticated) {
        return { name: 'dashboard' };
    }
});

export default router;
