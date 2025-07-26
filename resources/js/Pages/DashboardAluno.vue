<script setup>
import { router } from '@inertiajs/vue3'
import SideBar from '@/Components/SideBar.vue'
import PlannerBar from '@/Components/PlannerBar.vue'
import { toast } from 'vue3-toastify'
import 'vue3-toastify/dist/index.css'

defineProps({
  communities: Array
})

function logout() {
  router.post(route('logout'), {
    onSuccess: () => {
      toast.info('Você saiu com sucesso.')
      router.replace({ name: 'prelogin' })
    },
    onError: () => toast.error('Não foi possível sair.'),
  })
}
</script>

<template>
  <div class="dashboard-layout">
    <SideBar />
    <main class="main-content">
      <header class="header">
        <h1>O que você deseja aprender hoje?</h1>
        <button class="logout-btn" @click="logout">Sair</button>
      </header>

      <div v-if="communities.length === 0">
        <div class="balloon">
          Humm... Parece que você ainda não está inscrito em nada, selecione o botão de lupa na barra lateral e se cadastre em uma das nossas comunidades.
        </div>
        <div class="center-image">
          <img src="/images/imgDashboard.png" alt="Menina perguntando se já está dentro de alguma comunidade" />
        </div>
      </div>

      <div v-else class="communities-container">
        <h2 class="section-title">Minhas Comunidades</h2>
        <div class="card-grid">
          <div
            v-for="community in communities"
            :key="community.id"
            class="community-card"
          >
            <h3>{{ community.name }}</h3>
            <p>Criada por: {{ community.creator.name }}</p>
            <!-- adicione outras infos aqui -->
          </div>
        </div>
      </div>

      <PlannerBar />
    </main>
  </div>
</template>

<style scoped>
.main-content {
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

.logout-btn {
  background: #FF9500;
  color: #fff;
  border: none;
  border-radius: 8px;
  padding: 10px 22px;
  font-size: 1rem;
  font-weight: 700;
  cursor: pointer;
}

.logout-btn:hover {
  background: #e07d00;
}

.balloon {
  max-width: 450px;
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

.section-title {
  margin-left: 100px;
  font-size: 1.5rem;
  color: #002F66;
  font-weight: bold;
}

.card-grid {
  display: flex;
  flex-wrap: wrap;
  margin: 20px 100px;
  gap: 20px;
}

.community-card {
  background-color: #ffffff;
  border: 1px solid #ccc;
  padding: 15px 20px;
  border-radius: 10px;
  box-shadow: 0 2px 6px rgba(0,0,0,0.1);
  width: 300px;
}
</style>
