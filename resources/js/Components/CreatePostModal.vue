<template>
  <div class="modal-overlay">
    <div class="modal-content">
      <div class="modal-header">
        <h2>{{ postToEdit ? 'Editar Post' : 'Criar Novo Post' }}</h2>
      </div>
      <form @submit.prevent="submit">
        <div class="modal-body">
          <div class="modal-col full-width">
            <label for="post-title">Título</label>
            <input
              id="post-title"
              type="text"
              v-model="form.title"
              class="text-input"
              placeholder="Digite o título do post"
              required
            />
            <label for="post-content">Mensagem</label>
            <textarea
              id="post-content"
              v-model="form.content"
              class="message-textarea"
              placeholder="Escreva sua mensagem aqui..."
              required
            />
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn-postar" :disabled="form.processing">
            {{ postToEdit ? 'Salvar' : 'Postar' }}
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
import { useForm } from '@inertiajs/vue3';
import { defineProps, defineEmits, watch } from 'vue';

const props = defineProps({
  community: { type: Object, required: true },
  postToEdit: {
    type: Object,
    default: null,
  },
});

const emit = defineEmits(['close']);

const form = useForm({
  title: props.postToEdit ? props.postToEdit.title : '',
  content: props.postToEdit ? props.postToEdit.content : '',
  community_id: props.community.id,
});

watch(() => props.postToEdit, (newPost) => {
  if (newPost) {
    form.title = newPost.title;
    form.content = newPost.content;
  } else {
    form.title = '';
    form.content = '';
  }
});

function submit() {
  if (props.postToEdit) {
    // Chamada de rota corrigida para incluir o ID da comunidade e do post
    form.put(route('communities.posts.update', {
      community: props.community.id,
      post: props.postToEdit.id
    }), {
      onSuccess: () => {
        form.reset();
        emit('close');
      },
      preserveScroll: true,
    });
  } else {
    // Rota corrigida para usar 'posts.store', que não é aninhada
    form.post(route('posts.store'), {
      onSuccess: () => {
        form.reset();
        emit('close');
      },
      preserveScroll: true,
    });
  }
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
.text-input {
  border: 1.5px solid #153a6b;
  border-radius: 10px;
  width: 100%;
  padding: 12px 16px;
  font-size: 1rem;
  font-family: 'Inter', sans-serif;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  transition: border-color 0.2s, box-shadow 0.2s;
}
.text-input:focus {
  outline: none;
  border-color: #ff9500;
  box-shadow: 0 0 0 3px rgba(255, 149, 0, 0.1);
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
  min-height: 180px;
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
