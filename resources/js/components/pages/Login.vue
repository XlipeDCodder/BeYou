<template>
  <div class="login-container">
    <form @submit.prevent="handleLogin" class="login-form">
      <h2>Login</h2>
      <div v-if="errorMessage" class="error-message">{{ errorMessage }}</div>
      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" v-model="email" required>
      </div>
      <div class="form-group">
        <label for="password">Senha</label>
        <input type="password" id="password" v-model="password" required>
      </div>
      <button type="submit" class="submit-button">Entrar</button>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useAuthStore } from '@/stores/auth';
import { useRouter } from 'vue-router';

const router = useRouter();
const authStore = useAuthStore();

const email = ref('');
const password = ref('');
const errorMessage = ref(null);

const handleLogin = async () => {
  try {
    errorMessage.value = null;
    await authStore.login({ email: email.value, password: password.value });
    // Redireciona para a página inicial após o login bem-sucedido
    router.push('/');
  } catch (error) {
    errorMessage.value = 'Email ou password inválidos. Tente novamente.';
    console.error('Falha no login:', error);
  }
};
</script>

<style scoped>
/* Estilos para a página de login */
.login-container { display: flex; justify-content: center; align-items: center; min-height: 80vh; font-family: sans-serif; }
.login-form { background: #202020; padding: 40px; border-radius: 8px; color: white; width: 100%; max-width: 400px; }
h2 { text-align: center; margin-bottom: 20px; }
.form-group { margin-bottom: 15px; }
label { display: block; margin-bottom: 5px; }
input { width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #444; background: #333; color: white; }
.submit-button { width: 100%; padding: 10px; border: none; background-color: #007bff; color: white; border-radius: 5px; font-size: 1rem; cursor: pointer; }
.error-message { color: #ff4d4d; background: #4d1a1a; padding: 10px; border-radius: 5px; margin-bottom: 15px; text-align: center; }
</style>