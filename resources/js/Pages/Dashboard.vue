<template>
  <div class="dashboard-layout">
    <SideBar />
    <main class="main-content">
      <header class="header">
        <h1>O que você deseja aprender hoje?</h1>
      </header>

      <div class="center-image">
        <img src="/images/imgDashboard.png" alt="Menina perguntando se já está dentro de alguma comunidade" />
      </div>
      <!-- restante do dashboard -->
      <PlannerBar />
    </main>
  </div>
</template>

<script setup>
import SideBar from '@/Components/SideBar.vue'
import PlannerBar from '@/Components/PlannerBar.vue'
import { router } from '@inertiajs/vue3'
import { toast } from 'vue3-toastify'
import 'vue3-toastify/dist/index.css'

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
}

.center-image {
  display: flex;
  justify-content: center;
  align-items: center;
  margin: 24px 0; 
  margin-top: 10%;
}



</style>