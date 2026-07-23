import { defineStore } from 'pinia';
import axios from '../plugins/axios';

const projectColors = ['bg-blue-500', 'bg-[#4f39f6]', 'bg-emerald-500', 'bg-rose-500', 'bg-amber-500'];

export const useDashboardStore = defineStore('dashboard', {
    state: () => ({
        totalTasks: 0,
        projects: [],
        loaded: false,
    }),
    actions: {
        async fetchStats() {
            const { data } = await axios.get('/dashboard/stats');
            this.totalTasks = data.total_tasks || 0;
        },
        async fetchProjects() {
            const { data } = await axios.get('/dashboard/projects');
            this.projects = data.map((project, index) => ({
                ...project,
                color: projectColors[index % projectColors.length],
            }));
        },
        async refreshSidebarData() {
            await Promise.all([this.fetchStats(), this.fetchProjects()]);
            this.loaded = true;
        },
    },
});
