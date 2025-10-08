<template>
  <div class="page-container">
    <div v-if="loading" class="loading-message">A carregar o canal...</div>
    <div v-if="error" class="error-message">Canal não encontrado.</div>

    <div v-if="channel" class="channel-content">
      <div class="channel-banner">
        <h1 class="channel-name">{{ channel.name }}</h1>
        <p class="channel-description">{{ channel.description }}</p>
        </div>

      <h2 class="section-title">Vídeos</h2>
      <div class="video-grid">
        <router-link
          v-for="video in channel.videos"
          :key="video.id"
          :to="{ name: 'videos.show', params: { id: video.id } }"
          class="video-link"
        >
          <VideoCard :video="video" />
        </router-link>
      </div>
      </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';
import VideoCard from '@/components/VideoCard.vue'; 

const route = useRoute();
const channel = ref(null);
const loading = ref(true);
const error = ref(false);

async function fetchChannelData() {
  loading.value = true;
  error.value = false;
  channel.value = null;

  const channelSlug = route.params.slug;
  try {
    const response = await axios.get(`/api/channels/${channelSlug}`);
    channel.value = response.data.data;
  } catch (e) {
    console.error('Erro ao buscar o canal:', e);
    error.value = true;
  } finally {
    loading.value = false;
  }
}


onMounted(fetchChannelData);


watch(() => route.params.slug, fetchChannelData);
</script>

<style scoped>
.page-container { max-width: 1200px; margin: 20px auto; padding: 0 20px; font-family: sans-serif; color: #fff; }
.loading-message, .error-message { text-align: center; font-size: 1.2rem; margin-top: 50px; }
.channel-banner { background-color: #272727; padding: 30px; border-radius: 12px; margin-bottom: 30px; }
.channel-name { margin: 0 0 10px 0; font-size: 2rem; }
.channel-description { color: #aaa; margin: 0; }
.section-title { font-size: 1.5rem; margin-bottom: 20px; }
.video-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; }
.video-link { text-decoration: none; }
</style>