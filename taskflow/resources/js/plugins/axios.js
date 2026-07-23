import axios from 'axios';

axios.defaults.baseURL = '/api';
axios.defaults.headers.common['Accept'] = 'application/json';

const token = localStorage.getItem('token');
if (token) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
}

// Auto-logout on 401 (expired/invalid token)
axios.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response?.status === 401) {
            localStorage.removeItem('token');
            localStorage.removeItem('user');
            window.location.href = '/login';
        }
        return Promise.reject(error);
    }
);

export default axios;
