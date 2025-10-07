<template>
  <div class="page-container">
    <h1>Explorar Vídeos</h1>
    <div v-if="loading" class="loading-message">Carregando vídeos...</div>
      <div v-else class="video-grid">
        <router-link
          v-for="video in videos"
          :key="video.id"
          :to="{ name: 'videos.show', params: { id: video.id } }"
          class="video-link"
        >
          <VideoCard :video="video" />
        </router-link>
      </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import VideoCard from './VideoCard.vue';

// Variáveis de estado reativas
const videos = ref([]);
const loading = ref(true);

// Função que é executada quando o componente é "montado" (carregado na página)
onMounted(async () => {
  try {
    // Faz a chamada GET para a nossa API
    const response = await axios.get('/api/videos');
    // Atualiza a nossa variável de estado com os dados recebidos
    videos.value = response.data.data;
  } catch (error) {
    console.error('Erro ao buscar vídeos:', error);
  } finally {
    // Garante que a mensagem de "Carregando" desapareça, mesmo se der erro
    loading.value = false;
  }
});
</script>

<style scoped>
.page-container {
  max-width: 1200px;
  margin: 20px auto;
  padding: 0 20px;
  font-family: sans-serif;
  color: #fff;
}
.loading-message {
  text-align: center;
  font-size: 1.2rem;
}
.video-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 20px;
}

.video-link {
  text-decoration: none;
}
</style>