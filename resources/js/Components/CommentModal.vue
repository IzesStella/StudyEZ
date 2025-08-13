<template>
  <div class="modal-overlay">
    <div class="modal-content">
      <div class="modal-header">
        <h2>{{ commentToEdit ? 'Editar Comentário' : 'Adicionar Comentário' }}</h2>
      </div>

      <form @submit.prevent="submitComment">
        <div class="modal-body">
          <div class="modal-col full-width">
            <label for="commentContent">Seu Comentário:</label>
            <textarea
              id="commentContent"
              v-model="newCommentContent"
              class="message-textarea"
              placeholder="Escreva seu comentário aqui..."
              required
            ></textarea>
          </div>
        </div>

        <div class="modal-footer">
          <button
            type="submit"
            :disabled="isLoading"
            class="btn-postar"
          >
            {{ isLoading ? 'Enviando...' : (commentToEdit ? 'Salvar' : 'Comentar') }}
          </button>
          <button
            type="button"
            @click="$emit('close')"
            class="btn-cancelar"
          >
            Fechar
          </button>
        </div>

        <p v-if="errorMessage" class="error-message">{{ errorMessage }}</p>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import axios from 'axios';

const props = defineProps({
  post: {
    type: Object,
    required: true,
  },
  commentToEdit: {
    type: Object,
    default: null,
  },
});

const emit = defineEmits(['close', 'comment-added', 'comment-updated']);

const newCommentContent = ref('');
const isLoading = ref(false);
const errorMessage = ref('');

watch(() => props.commentToEdit, (newComment) => {
  if (newComment) {
    newCommentContent.value = newComment.content;
  } else {
    newCommentContent.value = '';
  }
}, { immediate: true });

const submitComment = async () => {
  isLoading.value = true;
  errorMessage.value = '';

  try {
    if (props.commentToEdit) {
      // Rota corrigida para posts.comments.update
      const response = await axios.put(
        route('posts.comments.update', { post: props.post.id, comment: props.commentToEdit.id }),
        { content: newCommentContent.value }
      );
      emit('comment-updated', response.data);
    } else {
      // Rota corrigida para posts.comments.store
      const response = await axios.post(
        route('posts.comments.store', { post: props.post.id }),
        { content: newCommentContent.value }
      );
      emit('comment-added', response.data);
    }
    
    newCommentContent.value = '';
    emit('close');

  } catch (error) {
    console.error('Error posting/updating comment:', error);
    if (error.response && error.response.data && error.response.data.message) {
      errorMessage.value = error.response.data.message;
    } else {
      errorMessage.value = 'Ocorreu um erro ao enviar/editar o comentário. Tente novamente.';
    }
  } finally {
    isLoading.value = false;
  }
};
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  inset: 0;
  z-index: 50;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(0, 0, 0, 0.3);
}
/* ... resto do seu CSS */
.modal-content {
  background: #fff;
  border-radius: 18px;
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1);
  padding: 32px;
  max-width: 500px;
  width: 100%;
  position: relative;
}
.modal-header {
  background: #b5d6fa;
  border-radius: 16px 16px 0 0;
  margin: -32px -32px 24px -32px;
  padding: 18px 0 14px 0;
  text-align: center;
}
.modal-header h2 {
  color: #153a6b;
  font-size: 2rem;
  font-weight: bold;
  margin: 0;
  font-family: 'Inter', sans-serif;
}
.modal-body {
  display: flex;
  flex-direction: column;
  margin-bottom: 32px;
}
.modal-col {
  display: flex;
  flex-direction: column;
  gap: 16px;
}
.full-width {
  width: 100%;
}
.modal-col label {
  font-weight: 600;
  color: #153a6b;
  font-size: 1rem;
  font-family: 'Inter', sans-serif;
}
.message-textarea {
  border: 1.5px solid #153a6b;
  border-radius: 10px;
  width: 100%;
  padding: 12px 16px;
  font-size: 1rem;
  font-family: 'Inter', sans-serif;
  resize: vertical;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  min-height: 140px;
  transition: border-color 0.2s, box-shadow 0.2s;
}
.message-textarea:focus {
  outline: none;
  border-color: #ff9500;
  box-shadow: 0 0 0 3px rgba(255, 149, 0, 0.1);
}
.modal-footer {
  display: flex;
  justify-content: center;
  gap: 24px;
  margin-top: 12px;
}
.btn-postar {
  background: #b5d6fa;
  color: #111;
  font-weight: 600;
  padding: 10px 32px;
  border-radius: 10px;
  font-size: 1.15rem;
  border: none;
  cursor: pointer;
  transition: background 0.2s;
}
.btn-postar:hover {
  background: #8fc3fa;
}
.btn-postar:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}
.btn-cancelar {
  background: #ff9500;
  color: #111;
  font-weight: 600;
  padding: 10px 32px;
  border-radius: 10px;
  font-size: 1.15rem;
  border: none;
  cursor: pointer;
  transition: background 0.2s;
}
.btn-cancelar:hover {
  background: #e07d00;
}
.error-message {
  color: #dc2626;
  font-size: 0.875rem;
  margin-top: 16px;
  text-align: center;
  font-weight: 500;
}
</style>
