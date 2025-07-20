<script setup>
import { ref } from 'vue'

const nome = ref('')
const monitor = ref('')
const descricao = ref('')

function salvarInfo() {
  // lógica para salvar informações da comunidade
}

function salvarDescricao() {
  // lógica para salvar descrição
}
</script>

<template>
  <div class="modal-overlay" @click.self="$emit('close')">
    <div class="modal-content">
      <div class="modal-header">
        <h1>Criar Comunidade</h1>
      </div>
      <div class="modal-body">
        <div class="info-section">
          <h2>Informações da Comunidade</h2>
          <input v-model="nome" type="text" placeholder="Nome" />
          <input v-model="monitor" type="text" placeholder="Monitor Responsável" />
          <div class="btn-right">
            <button class="save-btn" @click="salvarInfo">Salvar</button>
          </div>
        </div>
        <div class="desc-section">
          <h2>Descrição</h2>
          <textarea v-model="descricao" placeholder="Descrição"></textarea>
          <div class="btn-right">
            <button class="save-btn" @click="salvarDescricao">Salvar</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@700&display=swap');

.modal-overlay {
  position: fixed;
  top: 0; left: 0; right: 0; bottom: 0;
  background: rgba(0,0,0,0.30);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 999;
}

.modal-content {
  background: #fff;
  border-radius: 16px;
  box-shadow: 0 2px 16px rgba(0,0,0,0.30);
  width: 900px;
  max-width: 98vw;
  padding-bottom: 32px;
}

.modal-header {
  background: #b0d5ff;
  border-radius: 16px 16px 0 0;
  padding: 24px 0 16px 0;
  text-align: center;
}

.modal-header h1 {
  font-family: 'Montserrat', sans-serif;
  font-size: 2rem;
  color: #002F66;
  margin: 0;
  font-weight: 700;
}

.modal-body {
  display: flex;
  gap: 40px;
  justify-content: center;
  padding: 32px 24px 0 24px;
}

.info-section, .desc-section {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.info-section h2, .desc-section h2 {
  font-family: 'Montserrat', sans-serif;
  font-size: 1.2rem;
  font-weight: 700;
  margin-bottom: 8px;
  color: #222;
}

input, textarea {
  font-family: 'Montserrat', sans-serif;
  font-size: 1rem;
  border: 1.5px solid #001733;
  border-radius: 8px;
  padding: 10px 14px;
  outline: none;
  margin-bottom: 8px;
  width: 100%;
  box-sizing: border-box;
  box-shadow: 0px 0px 8px rgba(0,0,0,0.20);
}

textarea {
  min-height: 80px;
  resize: vertical;
}

.save-btn {
  align-self: center;
  background: #b0d5ff;
  color: #002F66;
  border: none;
  border-radius: 8px;
  padding: 8px 28px;
  font-size: 1rem;
  font-family: 'Montserrat', sans-serif;
  font-weight: 700;
  cursor: pointer;
  margin-top: 8px;
  transition: background 0.2s;
}

.save-btn:hover {
  background: #8bbbe6;
}

.btn-right {
  display: flex;
  justify-content: flex-end;
}
</style>