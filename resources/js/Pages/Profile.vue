<script setup>
import PlannerBar from '@/Components/PlannerBar.vue'
import SideBar from '@/Components/SideBar.vue'
import SideBarMonitor from '@/Components/SideBarMonitor.vue'
import CreateCommunity from '@/Components/CreateCommunity.vue'
import { usePage, router } from '@inertiajs/vue3'
import { ref } from 'vue'

const user = usePage().props.auth.user
const sidebarComponent = user.role === 'monitor' ? SideBarMonitor : SideBar
const showCreateCommunity = ref(false)

function goToEdit() {
  router.visit('/profile/edit')
}
</script>

<template>
  <div class="dashboard-layout">
    <component :is="sidebarComponent" @open-create-community="showCreateCommunity = true" />
    <div class="profile-page">
        <img class="profile-avatar" :src="user.avatar || '/images/default-avatar.png'" alt="Avatar" />
        <div class="profile-info">
          <h2>{{ user.name }}</h2>
          <p>Email: {{ user.email }}</p>
          <p>Tipo: {{ user.role }}</p>
          <button class="edit-btn" @click="goToEdit">Editar Perfil</button>
        </div>
      <PlannerBar />
      <CreateCommunity v-if="showCreateCommunity" @close="showCreateCommunity = false" />
    </div>
  </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap');

.profile-page {
  padding: 40px 24px;
  max-width: 700px;
  margin: 0 auto;
  font-family: 'Montserrat', sans-serif;
}
.profile-avatar {
  width: 160px;
  height: 160px;
  border-radius: 25%;
  object-fit: cover;
  border: 4px solid #b0d5ff;
  box-shadow: 0 2px 8px rgba(0,0,0,0.07);
}

.profile-info {
  display: flex;
  flex-direction: column;
  gap: 10px;
  font-family: 'Montserrat', sans-serif;
}

.profile-info h2 {
  margin: 0 0 8px 0;
  color: #135572;
  font-family: 'Montserrat', sans-serif;
}

.profile-info p {
  margin: 4px 0;
  color: #25608a;
  font-family: 'Montserrat', sans-serif;
}

.edit-btn {
  margin-top: 18px;
  padding: 10px 28px;
  background: #FF9500;
  color: #fff;
  border: none;
  border-radius: 8px;
  font-size: 1rem;
  font-family: 'Montserrat', sans-serif;
  font-weight: 700;
  cursor: pointer;
  transition: background 0.3s;
}

.edit-btn:hover {
  background: #e68900;
}
</style>