<template>
  <nav class="navbar">
    <router-link to="/" class="brand">Beyou</router-link>
    <div class="links">
      <template v-if="auth.isAuthenticated">
        <router-link to="/creator/dashboard" class="nav-link">Meu Estúdio</router-link> <span class="welcome-message">Bem-vindo, {{ auth.user.name }}</span>
        <button @click="handleLogout" class="nav-button">Logout</button>
      </template>
      <template v-else>
        <router-link to="/login" class="nav-link">Login</router-link>
        <router-link to="/register" class="nav-link">Registre-se</router-link>
      </template>
    </div>
  </nav>
</template>

<script setup>
import { useAuthStore } from '@/stores/auth.js';
import { useRouter } from 'vue-router';

const auth = useAuthStore();
const router = useRouter();

async function handleLogout() {
  await auth.logout();
  router.push('/login'); 
}
</script>

<style scoped>
/* Estilos simples para a barra de navegação */
.navbar { display: flex; justify-content: space-between; align-items: center; background-color: #1f1f1f; padding: 10px 20px; color: white; font-family: sans-serif; }
.brand { color: white; text-decoration: none; font-weight: bold; font-size: 1.5rem; }
.links { display: flex; align-items: center; gap: 15px; }
.nav-link { color: #ccc; text-decoration: none; }
.nav-link:hover { color: white; }
.welcome-message { font-size: 0.9rem; }
.nav-button { background: none; border: 1px solid white; color: white; padding: 5px 10px; border-radius: 5px; cursor: pointer; }
</style>