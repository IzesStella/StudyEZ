<template>
  <div class="modal-overlay">
    <div class="modal-content">
      <div class="modal-header">
        <h2>Criar Novo Post</h2>
      </div>
      <form @submit.prevent="submit">
        <div class="modal-body">
          <div class="modal-col image-col">
            <label for="image-upload-area">Adicionar Imagem</label>
            <div
              class="image-upload-area"
              id="image-upload-area"
              @click="triggerFileInput"
            >
            </div>
            <input
              type="file"
              multiple
              class="file-input"
              ref="fileInput"
              @change="handleFiles"
            />
          </div>
          <div class="modal-col">
            <label for="post-title">Título</label>
            <input
              id="post-title"
              type="text"
              v-model="form.title"
              class="text-input"
              placeholder=" "
            />
            <label for="post-content">Mensagem</label>
            <textarea
              id="post-content"
              v-model="form.content"
              class="message-textarea"
              placeholder=" "
            />
          </div>
        </div>
        <div class="modal-footer">
          <button
            type="submit"
            class="btn-postar"
            :disabled="form.processing"
          >
            Postar
          </button>
          <button type="button" @click="$emit('close')" class="btn-cancelar">
            Cancelar
          </button>
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
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1);
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
  font-family: 'Inter', sans-serif;
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
  flex-grow: 1;
  cursor: pointer;
  box-sizing: border-box;
  /* Sombra mais forte e visível */
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.15), 0 1px 3px rgba(0, 0, 0, 0.08);
}

.text-input {
  border: 1.5px solid black;
  border-radius: 10px;
  width: 100%;
  padding: 10px;
  font-size: 1rem;
  /* Sombra mais forte e visível */
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.15), 0 1px 3px rgba(0, 0, 0, 0.08);
  margin-bottom: 1rem;
}

.message-textarea {
  border: 1.5px solid black;
  border-radius: 10px;
  width: 100%;
  padding: 10px;
  font-size: 1rem;
  resize: vertical;
  /* Sombra mais forte e visível */
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.15), 0 1px 3px rgba(0, 0, 0, 0.08);
  min-height: 220px;
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