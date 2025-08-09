<template>
  <LogoStudyEZ />
  <div class="dashboard-layout">
    <SideBar @open-create-community="showCreateCommunity = true" />
    <main class="main-content">
      <header class="header">
        <h1>O que você deseja ensinar hoje?</h1>
      </header>

      <div v-if="isLoading" class="loading-state">
        Carregando suas comunidades...
      </div>

      <div v-else-if="communities.length === 0">
        <div class="balloon">
          Humm... Parece que você ainda não tem nenhuma comunidade cadastrada. Selecione o botão de “+” na barra lateral e crie uma comunidade.
        </div>
        <div class="center-image">
          <img src="/images/imgDashboard.png" alt="Menina perguntando se já está dentro de alguma comunidade" />
        </div>
      </div>

      <div v-else class="communities-container">
        <div class="card-grid">
          <div
            v-for="community in communities"
            :key="community.id"
            class="community-card"
          >
            <div>
              <h3 class="community-name">{{ community.name }}</h3>
              <p class="community-monitor">Monitor: {{ community.creator?.name || 'Você' }}</p>
            </div>
            <a
              :href="route('community.page', community.id)"
              class="enter-button"
            >
              Gerenciar
            </a>
          </div>
        </div>
      </div>

      <PlannerBar />
    </main>

    <CreateCommunity v-if="showCreateCommunity" @close="handleCloseCreateCommunity" />
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import axios from 'axios'
import { router } from '@inertiajs/vue3'
import { toast } from 'vue3-toastify'
import 'vue3-toastify/dist/index.css'
import LogoStudyEZ from '@/Components/LogoStudyEZ.vue'
import SideBar from '@/Components/SideBarMonitor.vue'
import PlannerBar from '@/Components/PlannerBar.vue'
import CreateCommunity from '@/Components/CreateCommunity.vue'

const communities = ref([])
const isLoading = ref(true)
const showCreateCommunity = ref(false)

const fetchCommunities = async () => {
  isLoading.value = true
  try {
    const response = await axios.get('/my-communities')
    communities.value = response.data
  } catch (error) {
    console.error('Erro ao buscar comunidades:', error)
    toast.error('Erro ao carregar comunidades.')
  } finally {
    isLoading.value = false
  }
}

onMounted(() => {
  fetchCommunities()
})

function handleCloseCreateCommunity() {
  showCreateCommunity.value = false
  fetchCommunities()
}
</script>

<style scoped>
.dashboard-layout {
  display: flex;
}
.main-content {
  flex-grow: 1;
  /* Alinhado com o dashboard do aluno */
  margin-left: 100px;
  padding: 20px;
  font-family: 'Montserrat', sans-serif;
}
.header {
  padding: 15px;
  font-size: 24px;
  font-weight: bold;
  color: #002F66;
  display: flex;
  justify-content: space-between;
  align-items: center;
}
/* Estilo para a mensagem de carregamento */
.loading-state {
  display: flex;
  justify-content: center;
  align-items: center;
  height: calc(100vh - 100px);
  font-size: 1.5rem;
  font-weight: 600;
  color: #002F66;
}
/* Estilo para o estado vazio */
.balloon {
  max-width: 450px;
  /* Alinhado com o dashboard do aluno */
  margin: 42px 0 0 100px;
  background: #e3f0ff;
  color: #002F66;
  font-size: 16px;
  padding: 20px 30px;
  border-radius: 50px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.10);
  text-align: center;
  position: relative;
}
.balloon::after {
  content: '';
  position: absolute;
  left: 75%;
  top: 100%;
  transform: translateX(-25%);
  border-width: 34px;
  border-style: solid;
  border-color: #e3f0ff transparent transparent transparent;
}
.center-image {
  display: flex;
  justify-content: center;
  margin-top: 20px;
}
.communities-container {
    /* Removido o estilo, pois o card-grid já tem a margem correta */
}
/* Estilo para a lista de comunidades */
.card-grid {
  display: flex;
  flex-wrap: wrap;
  /* Alinhado com o dashboard do aluno */
  margin: 20px 100px;
  gap: 20px;
}
.community-card {
  background: #1e40af;
  color: white;
  border-radius: 12px;
  padding: 24px 20px 18px 20px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  min-height: 130px;
  width: 320px;
  box-shadow: 0 4px 12px rgba(30, 64, 175, 0.15);
  transition: background 0.2s, box-shadow 0.2s;
}
.community-card:hover {
  background: #1e3a8a;
  box-shadow: 0 8px 20px rgba(30, 64, 175, 0.22);
}
.community-name {
  font-size: 1.2rem;
  font-weight: 700;
  margin: 0 0 4px 0;
  color: white;
}
.community-monitor {
  font-size: 0.95rem;
  margin: 0 0 8px 0;
  color: #c7d2fe;
}
.enter-button {
  background: white;
  color: #1e40af;
  font-weight: 600;
  padding: 8px 18px;
  border-radius: 6px;
  border: none;
  cursor: pointer;
  font-family: 'Montserrat', sans-serif;
  font-size: 1rem;
  align-self: flex-end;
  margin-top: 12px;
  text-decoration: none;
  transition: background 0.2s, color 0.2s;
  box-shadow: 0 1px 4px rgba(30, 64, 175, 0.08);
}
.enter-button:hover {
  background: #f1f5f9;
  color: #1e3a8a;
}
</style>