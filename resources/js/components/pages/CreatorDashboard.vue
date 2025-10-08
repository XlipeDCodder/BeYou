<template>
  <div class="page-container">
    <h1>Painel do Criador</h1>

    <div v-if="!channel" class="create-channel-wrapper">
      <h2>Crie o seu Canal para Começar</h2>
      <p>Dê um nome ao seu canal para poder começar a partilhar os seus vídeos com o mundo.</p>
      <form @submit.prevent="createChannel" class="create-channel-form">
        <input type="text" v-model="newChannelName" placeholder="Nome do seu canal" required>
        <button type="submit">Criar Canal</button>
      </form>
    </div>

    <div v-if="channel">
      <h2>Bem-vindo ao seu estúdio, {{ auth.user.channel.name }}!</h2>
      <p>Aqui você poderá fazer o upload e gerir os seus vídeos.</p>
      </div>

  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useAuthStore } from '@/stores/auth';
import axios from 'axios';

const auth = useAuthStore();
const newChannelName = ref('');
const channel = computed(() => auth.user?.channel);

async function createChannel() {
  if (!newChannelName.value.trim()) return;

  try {
    await axios.post('/api/channels', { name: newChannelName.value });
    // Após criar o canal, atualiza os dados do utilizador para refletir a mudança
    await auth.fetchUser();
    alert('Canal criado com sucesso!');
  } catch (error) {
    console.error('Erro ao criar o canal:', error);
    alert('Não foi possível criar o canal. Tente outro nome.');
  }
}
</script>

<style scoped>
.page-container { max-width: 900px; margin: 20px auto; padding: 0 20px; font-family: sans-serif; color: #fff; }
.create-channel-wrapper { background-color: #272727; padding: 30px; border-radius: 12px; margin-top: 20px; text-align: center; }
.create-channel-form { display: flex; gap: 10px; margin-top: 20px; }
.create-channel-form input { flex-grow: 1; padding: 10px; border-radius: 5px; border: 1px solid #444; background: #333; color: white; }
.create-channel-form button { padding: 10px 20px; border: none; background-color: #007bff; color: white; border-radius: 5px; cursor: pointer; }
</style>