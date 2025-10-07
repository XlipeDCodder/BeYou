<template>
  <div class="video-page-container">
    <div v-if="loading">Carregando vídeo...</div>
    <div v-if="error" class="error-message">Vídeo não encontrado.</div>

    <div v-if="video" class="video-layout">
      <div class="video-player-wrapper">
        <video :src="video.stream_url" controls autoplay class="video-player"></video>
        <h1 class="video-title">{{ video.title }}</h1>
        <div class="channel-info">
          <p>Canal: {{ video.channel.name }}</p>
        </div>
        <p class="video-description">{{ video.description }}</p>
      </div>
      </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const video = ref(null);
const loading = ref(true);
const error = ref(false);

onMounted(async () => {
  const videoId = route.params.id; // Pega o UUID da URL
  try {
    const response = await axios.get(`/api/videos/${videoId}`);
    video.value = response.data.data;
  } catch (e) {
    console.error('Erro ao buscar o vídeo:', e);
    error.value = true;
  } finally {
    loading.value = false;
  }
});
</script>

<style scoped>
/* Estilos simples para a página de visualização */
.video-page-container {
  max-width: 900px;
  margin: 20px auto;
  padding: 0 20px;
  font-family: sans-serif;
  color: #fff;
}
.video-player {
  width: 100%;
  background-color: #000;
}
.video-title {
  margin-top: 15px;
}
.channel-info, .video-description {
  color: #ccc;
}
</style>