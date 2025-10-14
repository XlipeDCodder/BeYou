<template>
  <div class="video-page-container">
    <div v-if="loading">Carregando vídeo...</div>
    <div v-if="error" class="error-message">Vídeo não encontrado.</div>

    <div v-if="video" class="video-layout">
      <div class="video-player-wrapper">
        <video
          ref="videoPlayer"
          :src="video.stream_url"
          controls
          autoplay
          class="video-player"
          @volumechange="saveVolume"
          @loadedmetadata="applySavedVolume"
        ></video>

        <h1 class="video-title">{{ video.title }}</h1>

        <div class="actions-bar">
          <div class="channel-info">
            <router-link
              :to="{ name: 'channels.show', params: { slug: video.channel.slug } }"
              class="channel-link"
            >
              <p>Canal: {{ video.channel.name }}</p>
            </router-link>
            <p class="video-description">{{ video.description }}</p>
          </div>
          <div class="reactions">
            <button
              @click="handleReaction('like')"
              :class="{ active: userReaction === 'like' }"
            >
              Gostei ({{ stats.likes_count }})
            </button>
            <button
              @click="handleReaction('dislike')"
              :class="{ active: userReaction === 'dislike' }"
            >
              Não Gostei ({{ stats.dislikes_count }})
            </button>
          </div>
        </div>
        <CommentSection v-if="video" :video-id="video.id" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth.js';
import axios from 'axios';
import CommentSection from './CommentSection.vue';

// --- Estado do Componente ---
const route = useRoute();
const router = useRouter();
const auth = useAuthStore();

const video = ref(null);
const loading = ref(true);
const error = ref(false);
const stats = ref({ likes_count: 0, dislikes_count: 0 });
const userReaction = ref(null);

// Referência para o elemento <video> no template
const videoPlayer = ref(null);

// --- Lógica de Volume Persistente ---

/**
 * Guarda o volume e o estado 'muted' atuais no localStorage.
 * Chamado pelo evento @volumechange do player.
 */
function saveVolume() {
  if (!videoPlayer.value) return;
  localStorage.setItem('video_player_volume', videoPlayer.value.volume);
  localStorage.setItem('video_player_muted', videoPlayer.value.muted);
}

/**
 * Aplica o volume e o estado 'muted' guardados ao player de vídeo.
 * Chamado pelo evento @loadedmetadata, que garante que o vídeo está pronto.
 */
function applySavedVolume() {
  if (!videoPlayer.value) return;
  const savedVolume = localStorage.getItem('video_player_volume');
  const savedMuted = localStorage.getItem('video_player_muted');

  if (savedVolume !== null) {
    videoPlayer.value.volume = parseFloat(savedVolume);
  }
  if (savedMuted !== null) {
    videoPlayer.value.muted = savedMuted === 'true';
  }
}

// --- Lógica de Reações (Likes/Dislikes) ---
async function handleReaction(newReactionType) {
  if (!auth.isAuthenticated) return router.push('/login');

  const currentReaction = userReaction.value;
  const videoId = video.value.id;

  // Lógica de atualização otimista da UI
  if (currentReaction === newReactionType) {
    // Clicou no mesmo botão: remover reação
    userReaction.value = null;
    stats.value[`${newReactionType}s_count`]--;
    await axios.delete(`/api/videos/${videoId}/reaction`);
  } else {
    // Clicou num botão diferente ou não tinha reagido
    if (currentReaction) {
      stats.value[`${currentReaction}s_count`]--;
    }
    userReaction.value = newReactionType;
    stats.value[`${newReactionType}s_count`]++;
    await axios.post(`/api/videos/${videoId}/${newReactionType}`);
  }
}

// --- Lógica Principal (Lifecycle Hook) ---
onMounted(async () => {
  const videoId = route.params.id;
  loading.value = true;
  error.value = false;

  try {
    const videoResponse = await axios.get(`/api/videos/${videoId}`);
    video.value = videoResponse.data.data;
    stats.value = videoResponse.data.data.stats;

    if (auth.isAuthenticated) {
      const reactionResponse = await axios.get(`/api/videos/${videoId}/reaction`);
      userReaction.value = reactionResponse.data.reaction;
    }
  } catch (e) {
    console.error('Erro ao buscar dados do vídeo:', e);
    error.value = true;
  } finally {
    loading.value = false;
  }
});
</script>

<style scoped>
.video-page-container {
  max-width: 1280px; /* Aumentado para layouts maiores */
  margin: 20px auto;
  padding: 0 20px;
  font-family: sans-serif;
  color: #fff;
}

.video-layout {
  display: flex; /* Usamos flexbox para o layout principal */
  gap: 24px;
}

.video-player-wrapper {
  flex: 1; /* Faz a coluna do vídeo ocupar o espaço principal */
}

.video-player {
  width: 100%;
  aspect-ratio: 16 / 9; /* Garante a proporção correta */
  background-color: #000;
  border-radius: 12px;
}

.video-title {
  color: #f1f1f1; /* Cor mais clara para o título */
  margin-top: 15px;
  font-size: 1.5rem;
}

.actions-bar {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-top: 10px;
  border-bottom: 1px solid #3f3f3f;
  padding-bottom: 15px;
  gap: 20px; /* Adiciona um espaço entre a info e os botões */
}

.channel-info p {
  margin: 0;
  color: #aaa; /* Cor mais clara para o nome do canal */
}

.reactions {
  flex-shrink: 0; /* Impede que o container dos botões encolha */
  display: flex; /* Garante que os botões dentro dele ficam em linha */
}

.reactions button {
  background: #272727;
  color: white;
  border: none;
  padding: 10px 18px;
  border-radius: 20px;
  cursor: pointer;
  margin-left: 10px;
  font-size: 0.9rem;
  font-weight: 500;
  transition: background-color 0.2s;
}

.reactions button:hover {
  background-color: #3f3f3f;
}

.reactions button.active {
  background-color: #3ea6ff;
  color: #0f0f0f;
}

.video-description {
  margin-top: 8px; /* Adiciona um pequeno espaço acima */
  line-height: 1.5;
  font-size: 0.9rem;
  color: #aaa; /* Cor mais suave */
}

/* Futuramente, a lista de vídeos sugeridos pode ir aqui */
.sidebar {
    width: 400px;
}

.channel-link {
  text-decoration: none; color: #ccc;
}
.channel-link:hover {
  text-decoration: underline;
}
</style>