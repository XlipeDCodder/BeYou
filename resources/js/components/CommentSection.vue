<template>
  <div class="comments-section">
    <h2 class="comments-title">{{ comments.length }} comentários</h2>

    <div v-if="auth.isAuthenticated" class="comment-input-area">
      <div class="current-user-avatar">
        <span class="avatar-letter">{{ auth.user.name.charAt(0) }}</span>
      </div>
      <div class="input-container">
        <textarea
          v-model="newCommentBody"
          placeholder="Adicione um comentário..."
          rows="1"
          @focus="showCommentControls = true"
          @input="autoGrow"
          ref="commentTextarea"
        ></textarea>
        <div v-if="showCommentControls" class="comment-controls">
          <button @click="cancelComment" class="cancel-btn">Cancelar</button>
          <button @click="postComment" :disabled="!newCommentBody.trim()" class="comment-btn">Comentar</button>
        </div>
      </div>
    </div>
    <p v-else class="login-prompt">
      <router-link to="/login">Faça login</router-link> para adicionar um comentário.
    </p>

    <div class="comments-list">
      <CommentItem
        v-for="comment in comments"
        :key="comment.id"
        :comment="comment"
        @comment-deleted="handleDelete"
      />
      <p v-if="comments.length === 0" class="no-comments-message">Nenhum comentário ainda. Seja o primeiro a comentar!</p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { useAuthStore } from '@/stores/auth';
import axios from 'axios';
import CommentItem from './CommentItem.vue';

const props = defineProps({
  videoId: {
    type: String,
    required: true,
  }
});

const auth = useAuthStore();
const comments = ref([]);
const newCommentBody = ref('');
const showCommentControls = ref(false); // Controla a visibilidade dos botões de comentar/cancelar
const commentTextarea = ref(null); // Referência para a textarea

async function fetchComments() {
  try {
    const response = await axios.get(`/api/videos/${props.videoId}/comments`);
    comments.value = response.data.data;
  } catch (error) {
    console.error('Erro ao buscar comentários:', error);
    comments.value = []; // Garante que a lista não esteja nula em caso de erro
  }
}

async function postComment() {
  if (newCommentBody.value.trim() === '') return;
  try {
    const response = await axios.post(`/api/videos/${props.videoId}/comments`, {
      body: newCommentBody.value,
    });
    comments.value.unshift(response.data.data);
    newCommentBody.value = '';
    showCommentControls.value = false;
    resetTextareaHeight();
  } catch (error) {
    console.error('Erro ao publicar comentário:', error);
    alert('Não foi possível publicar o seu comentário.');
  }
}

function handleDelete(commentId) {
  comments.value = comments.value.filter(c => c.id !== commentId);
}

function cancelComment() {
  newCommentBody.value = '';
  showCommentControls.value = false;
  resetTextareaHeight();
}

function autoGrow() {
  if (commentTextarea.value) {
    commentTextarea.value.style.height = 'auto'; // Reseta para calcular a altura real
    commentTextarea.value.style.height = commentTextarea.value.scrollHeight + 'px';
  }
}

function resetTextareaHeight() {
  if (commentTextarea.value) {
    commentTextarea.value.style.height = 'auto';
  }
}

// Monitora as alterações em newCommentBody para ajustar a altura da textarea
watch(newCommentBody, () => {
  autoGrow();
});

onMounted(fetchComments);
</script>

<style scoped>
.comments-section {
  margin-top: 30px;
  font-family: sans-serif;
  color: #fff;
}

.comments-title {
  font-size: 1.2rem;
  font-weight: 500;
  margin-bottom: 20px;
}

.comment-input-area {
  display: flex;
  gap: 15px;
  margin-bottom: 30px;
}

.current-user-avatar {
  flex-shrink: 0;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background-color: #555;
  display: flex;
  justify-content: center;
  align-items: center;
  font-size: 1.2rem;
  font-weight: bold;
  color: #fff;
}

.input-container {
  flex-grow: 1;
}

.input-container textarea {
  width: 100%;
  background: none; /* Remover fundo */
  border: none; /* Remover borda */
  border-bottom: 1px solid #666; /* Linha de fundo cinza */
  color: #fff;
  padding: 5px 0;
  font-size: 0.95rem;
  resize: none; /* Desativar resize manual */
  overflow-y: hidden; /* Ocultar scrollbar, a altura é controlada por JS */
}

.input-container textarea:focus {
  outline: none; /* Remover outline no focus */
  border-bottom-color: #3ea6ff; /* Linha azul no focus */
}

.comment-controls {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  margin-top: 10px;
}

.comment-controls button {
  padding: 8px 16px;
  border-radius: 18px;
  font-weight: 500;
  cursor: pointer;
  transition: background-color 0.2s, color 0.2s;
}

.cancel-btn {
  background-color: transparent;
  color: #aaa;
  border: none;
}

.cancel-btn:hover {
  background-color: rgba(255, 255, 255, 0.1);
  color: #fff;
}

.comment-btn {
  background-color: #3ea6ff;
  color: #0f0f0f;
  border: none;
}

.comment-btn:disabled {
  background-color: #3f3f3f;
  color: #888;
  cursor: not-allowed;
}

.login-prompt {
  color: #aaa;
  text-align: center;
  margin-bottom: 30px;
}

.login-prompt a {
  color: #3ea6ff;
  text-decoration: none;
  font-weight: bold;
}

.login-prompt a:hover {
  text-decoration: underline;
}

.comments-list {
  margin-top: 20px;
}

.no-comments-message {
    color: #aaa;
    text-align: center;
    margin-top: 30px;
}
</style>