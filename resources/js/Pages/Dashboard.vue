<template>
 
    <div class="dashboard">
      <header class="header">
        <h1>Bem‑vindo ao Dashboard</h1>
        <button @click="logout" class="btn-logout">Sair</button>
      </header>
      <!-- restante do dashboard -->
    </div>

</template>

<script setup>
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
.btn-logout {
  background: #e74c3c;
  color: white;
  padding: 8px 16px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}
.btn-logout:hover {
  opacity: 0.9;
}
</style>