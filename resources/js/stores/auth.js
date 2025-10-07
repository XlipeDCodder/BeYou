import { defineStore } from 'pinia';
import { ref, computed } from 'vue';

export const useAuthStore = defineStore('auth', () => {
  // State
  const user = ref(null);

  // Getters
  const isAuthenticated = computed(() => !!user.value);

  // Actions
  function setUser(newUser) {
    user.value = newUser;
  }

  // Vamos adicionar as ações de login, logout e fetchUser a seguir
  // async function login(credentials) { ... }
  // async function logout() { ... }

  return { user, isAuthenticated, setUser };
});