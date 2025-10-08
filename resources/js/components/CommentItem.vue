<template>
  <div class="comment-item">
    <div class="comment-avatar">
      <span class="avatar-letter">{{ comment.author.name.charAt(0) }}</span>
    </div>
    <div class="comment-content">
      <div class="comment-meta">
        <span class="comment-author">{{ comment.author.name }}</span>
        <span class="comment-date">{{ comment.created_at }}</span>
      </div>
      <p class="comment-body">{{ comment.body }}</p>
    </div>
    <div v-if="comment.can_delete" class="comment-actions">
      <div class="more-options-menu" @click="toggleMenu">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
          <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"></path>
        </svg>
        <div v-if="showMenu" class="dropdown-menu">
          <div @click.stop="deleteComment" class="menu-item">Apagar</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';

const props = defineProps({
  comment: {
    type: Object,
    required: true,
  }
});

const emit = defineEmits(['comment-deleted']);
const showMenu = ref(false); // Estado para controlar a visibilidade do menu

function toggleMenu() {
  showMenu.value = !showMenu.value;
}

async function deleteComment() {
  if (confirm('Tem a certeza de que quer apagar este comentário?')) {
    try {
      await axios.delete(`/api/comments/${props.comment.id}`);
      emit('comment-deleted', props.comment.id);
    } catch (error) {
      console.error('Falha ao apagar o comentário:', error);
      alert('Não foi possível apagar o comentário.');
    } finally {
      showMenu.value = false; // Fecha o menu após a ação
    }
  } else {
    showMenu.value = false; // Fecha o menu se o utilizador cancelar
  }
}
</script>

<style scoped>
.comment-item {
  display: flex;
  align-items: flex-start; /* Alinha o avatar e o texto no topo */
  padding: 15px 0;
  border-bottom: 1px solid #3f3f3f;
  position: relative; /* Para o posicionamento do menu dropdown */
  gap: 10px; /* Espaço entre avatar e conteúdo */
}

.comment-avatar {
  flex-shrink: 0; /* Impede que o avatar encolha */
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background-color: #555; /* Cor de fundo para o avatar */
  display: flex;
  justify-content: center;
  align-items: center;
  font-size: 1.2rem;
  font-weight: bold;
  color: #fff;
}

.avatar-letter {
  text-transform: uppercase;
}

.comment-content {
  flex-grow: 1; /* Permite que o conteúdo ocupe o espaço restante */
}

.comment-meta {
  display: flex;
  align-items: center;
  gap: 8px; /* Espaço entre nome e data */
  margin-bottom: 4px;
}

.comment-author {
  font-weight: bold;
  color: #fff;
  font-size: 0.9rem;
}

.comment-date {
  font-size: 0.75rem;
  color: #aaa;
}

.comment-body {
  margin: 0;
  color: #e0e0e0;
  line-height: 1.4;
  font-size: 0.9rem;
}

.comment-actions {
  flex-shrink: 0; /* Impede que as ações encolham */
  position: relative;
}

.more-options-menu {
  cursor: pointer;
  padding: 5px;
  border-radius: 50%;
  transition: background-color 0.2s;
  display: flex; /* Para centralizar o SVG */
  align-items: center;
  justify-content: center;
}

.more-options-menu:hover {
  background-color: rgba(255, 255, 255, 0.1);
}

.more-options-menu svg {
  width: 20px;
  height: 20px;
  color: #aaa;
}

.dropdown-menu {
  position: absolute;
  right: 0; /* Alinha à direita do botão de 3 pontinhos */
  top: 100%; /* Abaixo do botão */
  background-color: #272727;
  border: 1px solid #3f3f3f;
  border-radius: 4px;
  min-width: 120px;
  z-index: 10; /* Garante que fique acima de outros elementos */
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
}

.menu-item {
  padding: 10px 15px;
  color: #e0e0e0;
  cursor: pointer;
}

.menu-item:hover {
  background-color: #3ea6ff;
  color: #0f0f0f;
}
</style>