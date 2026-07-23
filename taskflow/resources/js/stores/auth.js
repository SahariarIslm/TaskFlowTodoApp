import { defineStore } from 'pinia';
import axios from '../plugins/axios';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        token: localStorage.getItem('token') || null,
        user: JSON.parse(localStorage.getItem('user') || 'null'),
    }),
    getters: {
        isAuthenticated: (state) => !!state.token,
    },
    actions: {
        setSession(token, user) {
            this.token = token;
            this.user = user;
            localStorage.setItem('token', token);
            localStorage.setItem('user', JSON.stringify(user));
            axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
        },
        async logout() {
            try {
                await axios.post('/auth/logout');
            } catch (e) {
                // ignore — clear local state regardless
            }
            this.token = null;
            this.user = null;
            localStorage.removeItem('token');
            localStorage.removeItem('user');
            delete axios.defaults.headers.common['Authorization'];
        },
    },
});
