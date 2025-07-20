<template>
  <div class="dashboard-layout">
    <SideBar @open-create-community="showCreateCommunity = true" />
    <main class="main-content">
      <header class="header">
        <h1>O que você deseja ensinar hoje?</h1>
        <button class="logout-btn" @click="logout">Sair</button>
      </header>

       <div class="balloon">
        Humm... Parece que você ainda não tem nenhuma comunidade cadastrada, selecione o botão de “+” na barra lateral e crie uma comunidade.
      </div>
      <div class="center-image">
        <img src="/images/imgDashboard.png" alt="Menina perguntando se já está dentro de alguma comunidade" />
      </div>
      <PlannerBar />
    </main>
    <CreateCommunity v-if="showCreateCommunity" @close="showCreateCommunity = false" />
  </div>
</template>

<script setup>
import SideBar from '@/Components/SideBarMonitor.vue'
import PlannerBar from '@/Components/PlannerBar.vue'
import CreateCommunity from '@/Components/CreateCommunity.vue'
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import { toast } from 'vue3-toastify'
import 'vue3-toastify/dist/index.css'

const showCreateCommunity = ref(false)

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
  margin: 42px 0 0 100px;
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

</style>