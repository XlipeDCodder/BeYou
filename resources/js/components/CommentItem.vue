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

      <div class="comment-footer-actions">
        <button v-if="auth.isAuthenticated" class="action-btn" @click="showReplyForm = !showReplyForm">Responder</button>
      </div>

      <div v-if="showReplyForm" class="reply-form">
        <textarea v-model="replyBody" placeholder="Adicione uma resposta..." rows="1"></textarea>
        <div class="reply-controls">
          <button @click="showReplyForm = false" class="cancel-btn">Cancelar</button>
          <button @click="postReply" :disabled="!replyBody.trim()" class="reply-btn">Responder</button>
        </div>
      </div>

      <div v-if="comment.replies_count > 0 && replies.length === 0" class="replies-toggle" @click="fetchReplies">
        Ver {{ comment.replies_count }} respostas
      </div>

      <div v-if="replies.length > 0" class="replies-container">
        <CommentItem
          v-for="reply in replies"
          :key="reply.id"
          :comment="reply"
          :video-id="videoId"
          @comment-deleted="handleReplyDeleted"
        />
      </div>
    </div>

    <div v-if="comment.can_delete" class="comment-actions">
      <div class="more-options-menu" @click="toggleMenu" v-on:blur="showMenu = false" tabindex="0">
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
import { useAuthStore } from '@/stores/auth';

// O componente agora também aceita o videoId para poder postar respostas
const props = defineProps({
  comment: { type: Object, required: true },
  videoId: { type: String, required: true },
});

const emit = defineEmits(['comment-deleted']);
const auth = useAuthStore();

// --- Lógica do Menu de Apagar ---
const showMenu = ref(false);
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
      showMenu.value = false;
    }
  } else {
    showMenu.value = false;
  }
}

// --- Lógica para Respostas ---
const replies = ref([]);
const showReplyForm = ref(false);
const replyBody = ref('');
const isLoadingReplies = ref(false);

async function fetchReplies() {
  if (isLoadingReplies.value) return;
  isLoadingReplies.value = true;
  try {
    const response = await axios.get(`/api/comments/${props.comment.id}/replies`);
    replies.value = response.data.data;
  } catch (error) {
    console.error('Erro ao buscar respostas:', error);
  } finally {
    isLoadingReplies.value = false;
  }
}

async function postReply() {
  if (replyBody.value.trim() === '') return;
  try {
    const response = await axios.post(`/api/videos/${props.videoId}/comments`, {
      body: replyBody.value,
      parent_id: props.comment.id, // A chave para criar uma resposta!
    });
    replies.value.unshift(response.data.data); // Adiciona a nova resposta à lista local
    replyBody.value = '';
    showReplyForm.value = false;
  } catch (error) {
    console.error('Erro ao postar resposta:', error);
    alert('Não foi possível publicar a sua resposta.');
  }
}

// Quando uma resposta (que é um CommentItem filho) é apagada, este método é chamado
function handleReplyDeleted(deletedReplyId) {
  replies.value = replies.value.filter(reply => reply.id !== deletedReplyId);
}
</script>

<style scoped>
.comment-item { display: flex; align-items: flex-start; padding: 15px 0; border-bottom: 1px solid #3f3f3f; position: relative; gap: 10px; }
.comment-avatar { flex-shrink: 0; width: 40px; height: 40px; border-radius: 50%; background-color: #555; display: flex; justify-content: center; align-items: center; font-size: 1.2rem; font-weight: bold; color: #fff; }
.avatar-letter { text-transform: uppercase; }
.comment-content { flex-grow: 1; }
.comment-meta { display: flex; align-items: center; gap: 8px; margin-bottom: 4px; }
.comment-author { font-weight: bold; color: #fff; font-size: 0.9rem; }
.comment-date { font-size: 0.75rem; color: #aaa; }
.comment-body { margin: 0; color: #e0e0e0; line-height: 1.4; font-size: 0.9rem; }
.comment-actions { flex-shrink: 0; position: relative; }
.more-options-menu { cursor: pointer; padding: 5px; border-radius: 50%; transition: background-color 0.2s; display: flex; align-items: center; justify-content: center; }
.more-options-menu:hover { background-color: rgba(255, 255, 255, 0.1); }
.more-options-menu svg { width: 20px; height: 20px; color: #aaa; }
.dropdown-menu { position: absolute; right: 0; top: 100%; background-color: #272727; border: 1px solid #3f3f3f; border-radius: 4px; min-width: 120px; z-index: 10; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3); }
.menu-item { padding: 10px 15px; color: #e0e0e0; cursor: pointer; }
.menu-item:hover { background-color: #3ea6ff; color: #0f0f0f; }

/* --- NOVOS ESTILOS PARA RESPOSTAS --- */
.comment-footer-actions { margin-top: 10px; }
.action-btn { background: none; border: none; color: #aaa; cursor: pointer; font-weight: 500; font-size: 0.8rem; padding: 5px 10px; border-radius: 15px; }
.action-btn:hover { background-color: rgba(255, 255, 255, 0.1); }
.replies-toggle { color: #3ea6ff; cursor: pointer; font-weight: 500; font-size: 0.9rem; margin-top: 15px; display: inline-block; padding: 5px 10px; border-radius: 15px; }
.replies-toggle:hover { background-color: rgba(62, 166, 255, 0.1); }
.replies-container { border-left: 2px solid #3f3f3f; margin-top: 15px; margin-left: 20px; padding-left: 20px; }
.reply-form { margin-top: 15px; }
.reply-form textarea { width: 100%; background: none; border: none; border-bottom: 1px solid #666; color: #fff; padding: 5px 0; font-size: 0.9rem; resize: none; }
.reply-form textarea:focus { outline: none; border-bottom-color: #3ea6ff; }
.reply-controls { display: flex; justify-content: flex-end; gap: 10px; margin-top: 10px; }
.reply-controls button { padding: 8px 16px; border-radius: 18px; font-weight: 500; cursor: pointer; border: none; }
.cancel-btn { background-color: transparent; color: #aaa; }
.cancel-btn:hover { background-color: rgba(255, 255, 255, 0.1); color: #fff; }
.reply-btn { background-color: #3ea6ff; color: #0f0f0f; }
.reply-btn:disabled { background-color: #3f3f3f; color: #888; cursor: not-allowed; }
</style>