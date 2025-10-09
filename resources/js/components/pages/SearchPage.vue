<template>
  <div class="page-container">
    <h1>Resultados da Busca para: "{{ searchTerm }}"</h1>
    <div v-if="loading">A procurar...</div>
    <div v-else-if="!videos.length">Nenhum vídeo encontrado.</div>
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
import { ref, onMounted, watch } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';
import VideoCard from '@/components/VideoCard.vue';

const route = useRoute();
const videos = ref([]);
const loading = ref(true);
const searchTerm = ref('');

async function performSearch(query) {
  if (!query) return;
  loading.value = true;
  searchTerm.value = query;
  try {
    const response = await axios.get(`/api/search?q=${query}`);
    videos.value = response.data.data;
  } catch (error) {
    console.error("Erro na busca:", error);
    videos.value = [];
  } finally {
    loading.value = false;
  }
}

onMounted(() => {
  performSearch(route.query.q);
});

// Observa mudanças no parâmetro 'q' da URL para fazer novas buscas
watch(() => route.query.q, (newQuery) => {
  performSearch(newQuery);
});
</script>

<style scoped>
/* Pode reutilizar os estilos do VideoList.vue e ChannelPage.vue */
.page-container { max-width: 1200px; margin: 20px auto; padding: 0 20px; font-family: sans-serif; color: #fff; }
h1 { margin-bottom: 30px; }
.video-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; }
.video-link { text-decoration: none; }
</style>