<template>
  <div class="modal-overlay">
    <div class="modal-content">
      <div class="modal-header">
        <h2>Criar Novo Post</h2>
      </div>
      <form @submit.prevent="submit">
        <div class="modal-body">
          <div class="modal-col">
            <label>Adicionar Imagens</label>
            <input type="file" multiple class="file-input" ref="fileInput" @change="handleFiles" />
            <div
              class="image-upload-area"
              @click="triggerFileInput"
            >
              <span class="plus-icon">+</span>
            </div>
          </div>
          <div class="modal-col">
            <label for="post-title">Título</label>
            <input
              id="post-title"
              type="text"
              v-model="form.title"
              class="message-textarea mb-4"
              placeholder="Digite o título do seu post..."
            />
            <label for="post-content">Mensagem</label>
            <textarea
              id="post-content"
              v-model="form.content"
              class="message-textarea"
              placeholder="Digite o conteúdo do seu post..."
            />
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn-postar" :disabled="form.processing">
            Postar
          </button>
          <button type="button" @click="$emit('close')" class="btn-cancelar">Cancelar</button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useForm, router } from '@inertiajs/vue3';

const props = defineProps({
  community: { type: Object, required: true },
});

const emit = defineEmits(['close']);

const form = useForm({
  title: '',
  content: '',
  community_id: props.community.id,
});

const fileInput = ref(null);

function triggerFileInput() {
  fileInput.value && fileInput.value.click();
}

function handleFiles(event) {
  // Lógica para lidar com o upload de arquivos pode ser implementada aqui
}

function submit() {
  form.post(route('posts.store', { community: props.community.id }), {
    onSuccess: () => {
      form.reset(); // Limpa o formulário
      emit('close'); // Fecha o modal
    },
    preserveScroll: true,
  });
}
</script>

<style scoped>
/* Seu estilo CSS permanece o mesmo */
.modal-overlay {
  position: fixed;
  inset: 0;
  z-index: 50;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(0, 0, 0, 0.3);
}
.modal-content {
  background: #fff;
  border-radius: 18px;
  box-shadow: 0 8px 32px rgba(30, 64, 175, 0.18);
  padding: 32px;
  max-width: 600px;
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
}
.modal-body {
  display: flex;
  flex-direction: column;
  gap: 32px;
  margin-bottom: 32px;
}
@media (min-width: 768px) {
  .modal-body {
    flex-direction: row;
  }
}
.modal-col {
  flex: 1;
  display: flex;
  flex-direction: column;
}
.modal-col label {
  font-weight: 600;
  margin-bottom: 8px;
}
.file-input {
  display: none;
}
.image-upload-area {
  border: 2px solid #ff9500;
  border-radius: 10px;
  background: #fff;
  min-height: 220px;
  min-width: 220px;
  max-width: 240px;
  max-height: 240px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
}
.plus-icon {
  font-size: 3.5rem;
  color: #ff9500;
  font-weight: bold;
  user-select: none;
  pointer-events: none;
}
.message-textarea {
  border: 1.5px solid #b0d5ff;
  border-radius: 10px;
  min-height: 220px;
  width: 100%;
  padding: 10px;
  font-size: 1rem;
  resize: vertical;
  box-shadow: 0 1px 4px rgba(30, 64, 175, 0.08);
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
</style>