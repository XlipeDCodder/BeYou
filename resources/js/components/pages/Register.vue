<template>
  <div class="register-container">
    <form @submit.prevent="handleRegister" class="register-form">
      <h2>Criar Conta</h2>
      <div v-if="errorMessage" class="error-message">{{ errorMessage }}</div>
      <div class="form-group">
        <label for="name">Nome</label>
        <input type="text" id="name" v-model="form.name" required>
      </div>
      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" v-model="form.email" required>
      </div>
      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" v-model="form.password" required>
      </div>
      <div class="form-group">
        <label for="password_confirmation">Confirmar Password</label>
        <input type="password" id="password_confirmation" v-model="form.password_confirmation" required>
      </div>
      <button type="submit" class="submit-button">Registar</button>
    </form>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue';
import { useAuthStore } from '@/stores/auth';
import { useRouter } from 'vue-router';

const router = useRouter();
const authStore = useAuthStore();

const form = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
});
const errorMessage = ref(null);

const handleRegister = async () => {
  try {
    errorMessage.value = null;
    await authStore.register(form);
    router.push('/'); // Redireciona para a home após o registo
  } catch (error) {
    // Lógica para mostrar erros de validação da API
    if (error.response && error.response.status === 422) {
      const errors = error.response.data.errors;
      errorMessage.value = Object.values(errors).flat().join(' ');
    } else {
      errorMessage.value = 'Ocorreu um erro. Tente novamente.';
    }
    console.error('Falha no registo:', error);
  }
};
</script>

<style scoped>
/* Usando os mesmos estilos da página de login para consistência */
.register-container { display: flex; justify-content: center; align-items: center; min-height: 80vh; font-family: sans-serif; }
.register-form { background: #202020; padding: 40px; border-radius: 8px; color: white; width: 100%; max-width: 400px; }
h2 { text-align: center; margin-bottom: 20px; }
.form-group { margin-bottom: 15px; }
label { display: block; margin-bottom: 5px; }
input { width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #444; background: #333; color: white; }
.submit-button { width: 100%; padding: 10px; border: none; background-color: #007bff; color: white; border-radius: 5px; font-size: 1rem; cursor: pointer; }
.error-message { color: #ff4d4d; background: #4d1a1a; padding: 10px; border-radius: 5px; margin-bottom: 15px; text-align: center; }
</style>