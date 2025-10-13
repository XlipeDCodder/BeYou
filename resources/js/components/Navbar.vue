<template>
  <nav class="navbar">
    <div class="navbar-left">
      <router-link to="/" class="brand">BeYou</router-link>
    </div>

    <div class="navbar-center">
      <form @submit.prevent="performSearch" class="search-form">
        <input type="text" v-model="searchQuery" placeholder="Procurar">
        <button type="submit">
          <svg viewBox="0 0 24 24"><path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"></path></svg>
        </button>
      </form>
    </div>

    <div class="navbar-right">
      <template v-if="auth.isAuthenticated">
        <router-link to="/creator/dashboard" class="nav-link">Meu Estúdio</router-link>
        <span class="welcome-message">Bem-vindo, {{ auth.user.name }}</span>
        <button @click="handleLogout" class="nav-button">Logout</button>
      </template>
      <template v-else>
        <router-link to="/login" class="nav-link">Login</router-link>
        <router-link to="/register" class="nav-link">Cadastre-se</router-link>
      </template>
    </div>
  </nav>
</template>

<script setup>
import { useAuthStore } from '@/stores/auth.js';
import { useRouter } from 'vue-router';
import { ref } from 'vue'; 

const auth = useAuthStore();
const router = useRouter();
const searchQuery = ref('');


const performSearch = () => {
  if (searchQuery.value.trim()) {
    router.push({ name: 'search', query: { q: searchQuery.value } });
  }
};

async function handleLogout() {
  await auth.logout();
  router.push('/login'); 
}
</script>

<style scoped>
/* Estilos simples para a barra de navegação */
.navbar {
    display: grid; /* Mude para grid para 3 colunas */
    grid-template-columns: 1fr 2fr 1fr; /* Esquerda, Centro, Direita */
    align-items: center;
    background-color: #1f1f1f;
    padding: 10px 20px;
    color: white;
    font-family: sans-serif;
}
.navbar-left { justify-self: start; }
.navbar-center { justify-self: center; width: 100%; max-width: 600px; }
.navbar-right { justify-self: end; display: flex; align-items: center; gap: 15px; }

.search-form {
    display: flex;
    width: 100%;
}
.search-form input {
    width: 100%;
    padding: 8px 12px;
    font-size: 1rem;
    border: 1px solid #3f3f3f;
    background-color: #121212;
    color: #fff;
    border-radius: 20px 0 0 20px;
}
.search-form button {
    padding: 0 15px;
    border: 1px solid #3f3f3f;
    border-left: none;
    background-color: #333;
    cursor: pointer;
    border-radius: 0 20px 20px 0;
}
.search-form svg {
    fill: #aaa;
    width: 20px;
    height: 20px;
}
.brand { color: white; text-decoration: none; font-weight: bold; font-size: 1.5rem; }
.links { display: flex; align-items: center; gap: 15px; }
.nav-link { color: #ccc; text-decoration: none; }
.nav-link:hover { color: white; }
.welcome-message { font-size: 0.9rem; }
.nav-button { background: none; border: 1px solid white; color: white; padding: 5px 10px; border-radius: 5px; cursor: pointer; }
</style>