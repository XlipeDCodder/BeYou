import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import axios from 'axios';

export const useAuthStore = defineStore('auth', () => {
  // --- STATE ---
  
  const user = ref(JSON.parse(localStorage.getItem('user')));
  const token = ref(localStorage.getItem('token'));


  if (token.value) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${token.value}`;
  }

  
  const isAuthenticated = computed(() => !!token.value && !!user.value);

 

  /**
   * Limpa o estado de autenticação do frontend.
   * Não faz chamadas à API. Usado para forçar o logout localmente.
   */
  function forceLogout() {
    token.value = null;
    user.value = null;
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    axios.defaults.headers.common['Authorization'] = '';
  }

  /**
   * Tenta fazer o login no backend e, se bem-sucedido, busca os dados do utilizador.
   */
  async function login(credentials) {
    const response = await axios.post('/api/auth/login', credentials);
    const accessToken = response.data.access_token;

    token.value = accessToken;
    localStorage.setItem('token', accessToken);
    axios.defaults.headers.common['Authorization'] = `Bearer ${accessToken}`;

    await fetchUser();
  }

  /**
   * Busca os dados do utilizador autenticado na API.
   */
  async function fetchUser() {
    try {
      const response = await axios.get('/api/auth/me');
      user.value = response.data;
      localStorage.setItem('user', JSON.stringify(response.data));
    } catch (error) {
      
      forceLogout();
      throw error;
    }
  }

  /**
   * Tenta fazer o logout no backend e sempre limpa o estado do frontend.
   */
  async function logout() {
    try {
      
      await axios.post('/api/auth/logout');
    } catch (error) {
      console.warn('Logout no servidor falhou, mas o cliente será deslogado.', error);
    } finally {
      
      forceLogout();
    }
  }

  return { token, user, isAuthenticated, login, logout, fetchUser, forceLogout };
});