<template>
  <LogoStudyEZ />
  <div class="dashboard-layout">
    <SideBar @open-create-community="showCreateCommunity = true" />
    <main class="main-content">
      <header class="header">
        <h1>O que você deseja ensinar hoje?</h1>
      </header>

      <!-- Se o monitor tiver comunidades, mostra elas -->
      <div v-if="communities.length > 0" class="communities-list">
        <div class="communities-grid">
          <div
            v-for="community in communities"
            :key="community.id"
            class="community-card"
          >
            <div>
              <h3 class="community-name">{{ community.name }}</h3>
              <p class="community-description">{{ community.description }}</p>
              <p class="community-monitor">Monitor: {{ community.creator?.name || 'Você' }}</p>
            </div>
            <button
              class="enter-button"
              @click="goToCommunity(community.id)"
            >
              Gerenciar
            </button>
          </div>
        </div>
      </div>

      <!-- Se não tiver comunidade, mostra a imagem e balão -->
      <div v-else>
        <div class="balloon">
          Humm... Parece que você ainda não tem nenhuma comunidade cadastrada. Selecione o botão de “+” na barra lateral e crie uma comunidade.
        </div>
        <div class="center-image">
          <img src="/images/imgDashboard.png" alt="Menina perguntando se já está dentro de alguma comunidade" />
        </div>
      </div>

      <PlannerBar />
    </main>

    <!-- Modal de criação -->
    <CreateCommunity v-if="showCreateCommunity" @close="handleCloseCreateCommunity" />
  </div>
</template>

<script setup>
import SideBar from '@/Components/SideBarMonitor.vue'
import PlannerBar from '@/Components/PlannerBar.vue'
import CreateCommunity from '@/Components/CreateCommunity.vue'
import { ref, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import axios from 'axios'
import { toast } from 'vue3-toastify'
import 'vue3-toastify/dist/index.css'
import LogoStudyEZ from '@/Components/LogoStudyEZ.vue'

const showCreateCommunity = ref(false)
const communities = ref([])

// Busca comunidades ao carregar a tela
const fetchCommunities = async () => {
  try {
    const response = await axios.get('/my-communities')
    console.log('Minhas comunidades:', response.data)
    communities.value = response.data
  } catch (error) {
    toast.error('Erro ao carregar comunidades.')
    console.error(error)
  }
}

onMounted(() => {
  fetchCommunities()
})

function handleCloseCreateCommunity() {
  showCreateCommunity.value = false
  fetchCommunities() // Recarrega comunidades após criar uma nova
}



function goToCommunity(communityId) {
  router.visit(route('community.page', communityId))
}
</script>

<style scoped>
.main-content {
  margin-left: 80px; 
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

.logout-btn {
  background: #FF9500;
  color: #fff;
  border: none;
  border-radius: 8px;
  padding: 10px 22px;
  font-size: 1rem;
  font-family: 'Montserrat', sans-serif;
  font-weight: 700;
  cursor: pointer;
  transition: background 0.2s;
}
.logout-btn:hover {
  background: #e07d00;
}

.center-image {
  display: flex;
  justify-content: center;
  align-items: center;
  margin: 24px 0; 
  margin-top: 0%;
}

.balloon {
  max-width: 450px;
  margin: 42px 0 0 80px;
  background: #e3f0ff;
  color: #002F66;
  font-family: 'Montserrat', sans-serif;
  font-size: 16px;
  padding: 20px 30px;
  border-radius: 50px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.20);
  position: relative;
  text-align: center;
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

/* Lista de comunidades */
.communities-list {
  margin-left: 80px;
  margin-top: 32px;
  font-family: 'Montserrat', sans-serif;
}

.communities-list h2 {
  color: #002F66;
  font-size: 1.5rem;
  font-weight: 700;
  margin-bottom: 24px;
}


.communities-grid {
  display: flex;
  flex-wrap: wrap;
  gap: 20px;
  align-items: flex-start;
}

.community-card {
  background: #1e40af;
  color: white;
  border-radius: 12px;
  padding: 18px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  align-items: flex-start;
  transition: all 0.3s ease;
  box-shadow: 0 4px 12px rgba(30, 64, 175, 0.2);
  width: 340px;
  height: 140px;
  margin-bottom: 20px;
}

.community-card:hover {
  background: #1e3a8a;
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(30, 64, 175, 0.3);
}

.community-name {
  font-size: 1.25rem;
  font-weight: 700;
  margin: 0 0 4px 0;
  color: white;
}

.community-description {
  font-size: 0.9rem;
  margin: 0 0 4px 0;
  color: #e0e7ff;
  line-height: 1.3;
}

.community-monitor {
  font-size: 0.8rem;
  margin: 0;
  color: #c7d2fe;
}

.enter-button {
  background: white;
  color: #1e40af;
  font-weight: 600;
  padding: 8px 16px;
  border-radius: 6px;
  border: none;
  cursor: pointer;
  transition: all 0.2s ease;
  font-family: 'Montserrat', sans-serif;
  white-space: nowrap;
  font-size: 0.9rem;
  align-self: flex-end;
  margin-left: 0;
}

.enter-button:hover {
  background: #f1f5f9;
  transform: translateY(-1px);
}
</style>
