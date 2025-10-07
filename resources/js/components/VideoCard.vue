<template>
  <div class="video-card">
    <img :src="video.thumbnail_url" alt="Thumbnail do vídeo" class="thumbnail">
    <div class="details">
      <h3 class="title">{{ video.title }}</h3>
      <p class="channel-name">{{ video.channel.name }}</p>
      </div>
  </div>
</template>

<script setup>
// Este componente recebe um 'prop' chamado 'video' do seu componente pai.
defineProps({
  video: {
    type: Object,
    required: true,
  },
});
</script>

<style scoped>
.video-card {
  cursor: pointer;
  border-radius: 8px;
  overflow: hidden;
  background-color: #202020;
  color: #fff;
  font-family: sans-serif;
}
.thumbnail {
  width: 100%;
  aspect-ratio: 16 / 9;
  object-fit: cover;
  background-color: #333;
}
.details {
  padding: 12px;
}
.title {
  font-size: 1rem;
  margin: 0 0 8px 0;
  line-height: 1.3;
}
.channel-name {
  font-size: 0.85rem;
  color: #aaa;
  margin: 0;
}
</style>