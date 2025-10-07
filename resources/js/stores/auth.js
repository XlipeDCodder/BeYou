import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import axios from 'axios';

export const useAuthStore = defineStore('auth', () => {
    const user = ref(JSON.parse(localStorage.getItem('user')));
    const token = ref(localStorage.getItem('token'));

    axios.defaults.headers.common['Authorization'] = token.value ? `Bearer ${token.value}` : '';

    const isAuthenticated = computed(() => !!user.value);

    async function login(credentials) {
        const response = await axios.post('/api/auth/login', credentials);
        const accessToken = response.data.access_token;

        token.value = accessToken;
        localStorage.setItem('token', accessToken);
        axios.defaults.headers.common['Authorization'] = `Bearer ${accessToken}`;

        await fetchUser();
    }

    async function fetchUser() {
        try {
            const response = await axios.get('/api/auth/me');
            user.value = response.data;
            localStorage.setItem('user', JSON.stringify(response.data));
        } catch (error) {
            // Limpa o estado se o token for inválido
            user.value = null;
            localStorage.removeItem('user');
            token.value = null;
            localStorage.removeItem('token');
            axios.defaults.headers.common['Authorization'] = '';
            throw error; // Propaga o erro para o componente de login
        }
    }

    async function logout() {
        await axios.post('/api/auth/logout');
        user.value = null;
        localStorage.removeItem('user');
        token.value = null;
        localStorage.removeItem('token');
        axios.defaults.headers.common['Authorization'] = '';
    }

    return { token, user, isAuthenticated, login, logout, fetchUser };
});