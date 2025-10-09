import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '@/stores/auth';

// Importação dos Componentes/Páginas
import VideoList from '@/components/VideoList.vue';
import VideoPlayer from '@/components/VideoPlayer.vue';
import Login from '@/components/pages/Login.vue';
import Register from '@/components/pages/Register.vue';
import ChannelPage from '@/components/pages/ChannelPage.vue';
import CreatorDashboard from '@/components/pages/CreatorDashboard.vue';
import SearchPage from '@/components/pages/SearchPage.vue';

const routes = [
  // --- Rotas Públicas para acessar o sistema ---
  {
    path: '/',
    name: 'home',
    component: VideoList,
  },
  {
    path: '/videos/:id',
    name: 'videos.show',
    component: VideoPlayer,
  },
  {
    path: '/login',
    name: 'login',
    component: Login,
  },
  {
    path: '/register',
    name: 'register',
    component: Register,
  },
  {
    path: '/channels/:slug',
    name: 'channels.show',
    component: ChannelPage,
  },

  // --- Rotas Protegidas ---
  {
    path: '/creator/dashboard',
    name: 'creator.dashboard',
    component: CreatorDashboard,
    beforeEnter: (to, from, next) => {
      const auth = useAuthStore();
      if (auth.isAuthenticated) {
        next(); 
      } else {
        next({ name: 'login' }); 
      }
    },
  },

  { path: '/search', name: 'search', component: SearchPage },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;