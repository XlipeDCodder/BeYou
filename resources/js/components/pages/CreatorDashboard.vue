<template>
  <div class="page-container">
    <header class="dashboard-header">
      <h1>Painel do Criador</h1>
      <p v-if="channel">Bem-vindo ao seu estúdio, {{ channel.name }}!</p>
    </header>

    <div v-if="auth.user && !channel" class="create-channel-wrapper">
      <h2>Crie o seu Canal para Começar</h2>
      <p>Dê um nome ao seu canal para poder começar a partilhar os seus vídeos com o mundo.</p>
      <form @submit.prevent="createChannel" class="create-channel-form">
        <div v-if="channelError" class="error-message">{{ channelError }}</div>
        <input type="text" v-model="newChannelName" placeholder="Nome do seu canal" required>
        <button type="submit">Criar Canal</button>
      </form>
    </div>

    <div v-if="auth.user && channel">

      <div class="upload-area">
        <div
          class="upload-box"
          :class="{ 'dragover': isDragOver }"
          @dragover.prevent="onDragOver"
          @dragleave.prevent="onDragLeave"
          @drop.prevent="onDrop"
        >
          <input
            type="file"
            id="videoFile"
            @change="handleFileSelect"
            accept="video/mp4,video/quicktime,video/x-msvideo"
            hidden
            ref="fileInput"
          >
          <label for="videoFile" class="upload-label">
            <div v-if="!videoFile">
              <svg class="upload-icon" viewBox="0 0 24 24"><path d="M9 16h6v-6h4l-7-7-7 7h4v6zm-4 4h14v-2H5v2z"></path></svg>
              <div class="upload-text">Arraste e solte o ficheiro de vídeo para fazer o upload</div>
              <div class="upload-or">ou</div>
              <button type="button" @click="triggerFileSelect" class="select-file-btn">SELECIONAR FICHEIROS</button>
            </div>
            <div v-else class="file-selected-text">
              Ficheiro selecionado: {{ videoFile.name }}
            </div>
          </label>
        </div>

        <form @submit.prevent="handleVideoUpload" class="details-form">
          <h3>Detalhes do Vídeo</h3>
          <div v-if="uploadError" class="error-message">{{ uploadError }}</div>
          <div v-if="uploadSuccessMessage" class="success-message">{{ uploadSuccessMessage }}</div>

          <div class="form-group">
            <label for="title">Título (obrigatório)</label>
            <input type="text" id="title" v-model="videoTitle" required>
          </div>

          <div class="form-group">
            <label for="description">Descrição</label>
            <textarea id="description" v-model="videoDescription" rows="5"></textarea>
          </div>

          <div v-if="isUploading" class="progress-wrapper">
            <p>A enviar o vídeo... {{ uploadProgress }}%</p>
            <progress :value="uploadProgress" max="100"></progress>
          </div>

          <button type="submit" :disabled="!videoFile || isUploading" class="submit-btn">
            {{ isUploading ? 'A ENVIAR...' : 'ENVIAR VÍDEO' }}
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useAuthStore } from '@/stores/auth';
import axios from 'axios';

const auth = useAuthStore();
const channel = computed(() => auth.user?.channel);


const newChannelName = ref('');
const channelError = ref(null);

async function createChannel() {
  if (!newChannelName.value.trim()) return;
  channelError.value = null;
  try {
    await axios.post('/api/channels', { name: newChannelName.value });
    await auth.fetchUser();
    alert('Canal criado com sucesso!');
  } catch (error) {
    console.error('Erro ao criar o canal:', error);
    channelError.value = 'Não foi possível criar o canal. Tente outro nome.';
  }
}


const videoTitle = ref('');
const videoDescription = ref('');
const videoFile = ref(null);
const isUploading = ref(false);
const uploadProgress = ref(0);
const uploadError = ref(null);
const uploadSuccessMessage = ref(null);
const fileInput = ref(null);
const isDragOver = ref(false);

function triggerFileSelect() {
  fileInput.value.click();
}

function handleFileSelect(event) {
  const file = event.target.files[0];
  if (file) {
    videoFile.value = file;
    uploadSuccessMessage.value = null; 
    uploadError.value = null;
  }
}

function onDragOver() {
  isDragOver.value = true;
}

function onDragLeave() {
  isDragOver.value = false;
}

function onDrop(event) {
  isDragOver.value = false;
  const file = event.dataTransfer.files[0];
  if (file && file.type.startsWith('video/')) {
    videoFile.value = file;
    uploadSuccessMessage.value = null; 
    uploadError.value = null;
    const dataTransfer = new DataTransfer();
    dataTransfer.items.add(file);
    fileInput.value.files = dataTransfer.files;
  }
}

async function handleVideoUpload() {
  if (!videoFile.value) {
    uploadError.value = 'Por favor, selecione um ficheiro de vídeo.';
    return;
  }

  isUploading.value = true;
  uploadError.value = null;
  uploadSuccessMessage.value = null;
  uploadProgress.value = 0;

  const formData = new FormData();
  formData.append('title', videoTitle.value);
  formData.append('description', videoDescription.value);
  formData.append('video', videoFile.value);

  try {
    const response = await axios.post('/api/videos', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
      onUploadProgress: (progressEvent) => {
        uploadProgress.value = Math.round((progressEvent.loaded * 100) / progressEvent.total);
      },
    });

    uploadSuccessMessage.value = response.data.message;
    
    videoTitle.value = '';
    videoDescription.value = '';
    videoFile.value = null;
    if (fileInput.value) {
        fileInput.value.value = null;
    }

  } catch (error) {
    console.error('Erro no upload do vídeo:', error);
    if (error.response && error.response.status === 422) {
        uploadError.value = Object.values(error.response.data.errors).flat().join(' ');
    } else {
        uploadError.value = 'Falha no upload. Verifique os dados e o tamanho do ficheiro.';
    }
  } finally {
    isUploading.value = false;
  }
}
</script>

<style scoped>
/* Estilos Gerais da Página */
.page-container { max-width: 1200px; margin: 20px auto; padding: 0 20px; font-family: sans-serif; color: #fff; }
.dashboard-header { margin-bottom: 40px; }
.dashboard-header h1 { font-size: 2rem; }
.dashboard-header p { color: #aaa; font-size: 1.1rem; }

/* Layout da Área de Upload */
.upload-area {
  display: grid;
  grid-template-columns: 1fr 1.5fr;
  gap: 30px;
  align-items: flex-start;
}

/* Caixa de Upload e Drag & Drop */
.upload-box {
  border: 2px dashed #3f3f3f;
  border-radius: 12px;
  display: flex;
  justify-content: center;
  align-items: center;
  transition: border-color 0.2s, background-color 0.2s;
  min-height: 350px; /* Altura mínima para a caixa */
}
.upload-box.dragover {
  border-color: #3ea6ff;
  background-color: rgba(62, 166, 255, 0.1);
}
.upload-label {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  text-align: center;
  padding: 20px;
  width: 100%;
  height: 100%;
  cursor: pointer;
}
.upload-icon {
  width: 60px;
  height: 60px;
  fill: #888;
  margin-bottom: 20px;
}
.upload-text {
  font-size: 1rem;
  color: #aaa;
}
.upload-or {
  margin: 15px 0;
  color: #888;
}
.select-file-btn {
  background-color: #3ea6ff;
  color: #0f0f0f;
  border: none;
  padding: 10px 20px;
  border-radius: 20px;
  font-weight: 500;
  cursor: pointer;
}
.file-selected-text {
  font-size: 1rem;
  color: #4dff88;
  padding: 10px;
  word-break: break-all;
}

/* Formulário de Detalhes */
.details-form {
  background: #272727;
  padding: 30px;
  border-radius: 12px;
}
.details-form h3 {
  margin-top: 0;
  font-size: 1.5rem;
}
.form-group { margin-bottom: 20px; }
.form-group label { display: block; margin-bottom: 8px; font-weight: 500; color: #aaa; }
.form-group input[type="text"], .form-group textarea {
  width: 100%;
  padding: 12px;
  border-radius: 8px;
  border: 1px solid #444;
  background: #121212;
  color: white;
  font-size: 1rem;
}
.form-group textarea {
    resize: vertical;
}
.submit-btn {
  width: 100%;
  padding: 12px;
  border: none;
  background-color: #3ea6ff;
  color: #0f0f0f;
  border-radius: 20px;
  font-size: 1.1rem;
  font-weight: bold;
  cursor: pointer;
  transition: background-color 0.2s;
}
.submit-btn:hover {
    background-color: #6fc1ff;
}
.submit-btn:disabled { background-color: #555; color: #888; cursor: not-allowed; }
.progress-wrapper { margin: 20px 0; }
.progress-wrapper p { text-align: center; margin-bottom: 5px; }
.progress-wrapper progress { width: 100%; -webkit-appearance: none; appearance: none; }
.progress-wrapper progress::-webkit-progress-bar { background-color: #444; border-radius: 5px; }
.progress-wrapper progress::-webkit-progress-value { background-color: #3ea6ff; border-radius: 5px; transition: width 0.1s linear; }
.error-message { color: #ff4d4d; background-color: rgba(255, 77, 77, 0.1); padding: 10px; border-radius: 5px; margin-bottom: 15px; }
.success-message { color: #4dff88; background-color: rgba(77, 255, 136, 0.1); padding: 10px; border-radius: 5px; margin-bottom: 15px; }


.create-channel-wrapper { background-color: #272727; padding: 30px; border-radius: 12px; margin-top: 20px; text-align: center; }
.create-channel-form { display: flex; gap: 10px; margin-top: 20px; }
.create-channel-form input { flex-grow: 1; padding: 10px; border-radius: 5px; border: 1px solid #444; background: #333; color: white; }
.create-channel-form button { padding: 10px 20px; border: none; background-color: #007bff; color: white; border-radius: 5px; cursor: pointer; }
</style>