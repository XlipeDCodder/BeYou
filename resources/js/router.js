import { createRouter, createWebHistory } from 'vue-router';
import VideoList from './components/VideoList.vue';
import VideoPlayer from './components/VideoPlayer.vue'; 
import Login from '@/components/pages/Login.vue';

const routes = [
  { path: '/', name: 'home', component: VideoList },
  { path: '/videos/:id', name: 'videos.show', component: VideoPlayer },
  { path: '/login', name: 'login', component: Login }, 
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;