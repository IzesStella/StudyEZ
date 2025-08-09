<template>
  <div class="modal-overlay" @click.self="$emit('close')">
    <div class="modal-content">
      <div class="modal-header">
        <h1>Criar Comunidade</h1>
      </div>
      <div class="modal-body">
        <form @submit.prevent="submit" class="w-full">
          <!-- Nome da Comunidade -->
          <div class="info-section">
            <h2>Informações da Comunidade</h2>
            <input
              v-model="form.name"
              type="text"
              placeholder="Ex: Matemática Avançada"
              class="modal-input"
              required
            />
          </div>

          <!-- Descrição (opcional) -->
           <div class="desc-section">
            <h2>Descrição</h2>
            <textarea
              v-model="form.description"
              placeholder="Descrição (opcional)"
              class="modal-textarea"
              maxlength="130"
            ></textarea>
            <p class="char-count">
              {{ form.description.length }}/130 caracteres
            </p>
          </div>

          <!-- Botões -->
          <div class="btn-right">
            <button
              type="submit"
              class="save-btn"
              :disabled="form.processing"
            >
              {{ form.processing ? 'Criando...' : 'Criar Comunidade' }}
            </button>
            <button
              type="button"
              class="save-btn ml-2"
              @click="$emit('close')"
            >
              Cancelar
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { defineEmits } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { toast } from 'vue3-toastify'
import 'vue3-toastify/dist/index.css'

const emit = defineEmits(['close','created'])

// useForm do Inertia já trata de post, state e erros
const form = useForm({
  name: '',
  description: '',
})

function submit() {
  form.post(route('communities.store'), {
    onSuccess: () => {
      toast.success('Comunidade criada com sucesso!')
      form.reset()          // limpa o formulário
      emit('created')  
      emit('close') 
    },
    onError: (errors) => {
      Object.values(errors).flat().forEach(msg => toast.error(msg))
    },
  })
}
</script>

<style scoped>
.modal-overlay {
  position: fixed; top: 0; left: 0; right: 0; bottom: 0;
  background: rgba(0,0,0,0.3);
  display: flex; align-items: center; justify-content: center;
  z-index: 999;
}
.modal-content {
  background: #fff; border-radius: 16px;
  box-shadow: 0 2px 16px rgba(0,0,0,0.3);
  width: 600px; max-width: 95vw;
}
.modal-header {
  background: #b0d5ff; padding: 24px 0;
  border-radius: 16px 16px 0 0; text-align: center;
}
.modal-header h1 {
  margin: 0; font-family: 'Montserrat', sans-serif;
  font-size: 2rem; color: #002F66; font-weight: 700;
}
.modal-body { padding: 24px; display: flex; flex-direction: column; gap: 16px; }
.modal-input, .modal-textarea {
  width: 100%; padding: 10px 14px;
  border: 1.5px solid #001733; border-radius: 8px;
  font-family: 'Montserrat', sans-serif; font-size: 1rem;
  box-shadow: 0 0 8px rgba(0,0,0,0.2);
}
.modal-textarea { min-height: 120px; resize: vertical; }

.char-count {
  font-size: 0.875rem; 
  color: #6b7280;     
  text-align: right;  
  margin-top: 4px;    
}
.btn-right {
  display: flex; justify-content: flex-end; gap: 8px; margin-top: 16px;
}
.save-btn {
  background: #b0d5ff; color: #002F66; border: none;
  border-radius: 8px; padding: 10px 24px;
  font-family: 'Montserrat', sans-serif;
  font-weight: 700; cursor: pointer; transition: background 0.2s;
}
.save-btn:hover:enabled { background: #8bbbe6; }
.save-btn:disabled { opacity: 0.6; cursor: not-allowed; }

.cancel-btn {
  background: #e5e7eb;
  color: #4b5563;
  border: none;
  border-radius: 8px;
  padding: 10px 24px;
  font-family: 'Montserrat', sans-serif;
  font-weight: 700;
  cursor: pointer;
  transition: background 0.2s;
}
.cancel-btn:hover {
  background: #d1d5db;
}
</style>