<script setup>
import PlannerBar from '@/Components/PlannerBar.vue'
import SideBar from '@/Components/SideBar.vue'
import SideBarMonitor from '@/Components/SideBarMonitor.vue'
import CreateCommunity from '@/Components/CreateCommunity.vue'
import EditProfileModal from '@/Components/EditProfileModal.vue'
import { usePage } from '@inertiajs/vue3'
import { ref } from 'vue'
import LogoStudyEZ from '@/Components/LogoStudyEZ.vue'

const user = usePage().props.auth.user
const sidebarComponent = user.role === 'monitor' ? SideBarMonitor : SideBar
const showCreateCommunity = ref(false)
const showEditProfile = ref(false)

function goToEdit() {
  showEditProfile.value = true
}
</script>

<template>
  <LogoStudyEZ />
  <div class="dashboard-layout">
    <component :is="sidebarComponent" @open-create-community="showCreateCommunity = true" />
    <div class="profile-page">
      <div class="profile-container">
        <template v-if="user.profile_photo">
          <img class="profile-avatar" :src="`/storage/${user.profile_photo}`" alt="Avatar" />
        </template>
        <template v-else>
          <font-awesome-icon :icon="['fas', 'user']" class="profile-avatar-icon" />
        </template>
        <div class="profile-info">
          <div class="profile-header">
            <h2>{{ user.name }}</h2>
            <button class="edit-btn" @click="goToEdit">Editar Perfil</button>
          </div>
          <div class="info-item">
            <h3>{{ (user.role === 'monitor' ? 'monitor' : 'aluno') }}</h3>
          </div>
          <div class="info-item" v-if="user.bio">
            <span class="value">{{ user.bio }}</span>
          </div>
          <div class="info-item" v-else>
            <span>Nenhuma bio adicionada ainda.</span>
          </div>
        </div>
      </div>
      <PlannerBar />
      <CreateCommunity v-if="showCreateCommunity" @close="showCreateCommunity = false" />
      <EditProfileModal 
        v-if="showEditProfile" 
        :must-verify-email="false"
        @close="showEditProfile = false" 
      />
      <div class="profile-divider"></div>
    </div>
  </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap');

.profile-page {
  padding: 40px 24px;
  max-width: 900px;
  margin: 0 auto;
  font-family: 'Montserrat', sans-serif;
  min-height: 40vh;
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.profile-container {
  display: flex;
  align-items: center;
  gap: 32px;
  margin-bottom: 24px;
}

.profile-avatar {
  width: 160px;
  height: 160px;
  border-radius: 20px;
  object-fit: cover;
  border: 4px solid #b0d5ff;
  box-shadow: 0 8px 16px rgba(176, 213, 255, 0.3);
  flex-shrink: 0;
}

.profile-avatar-icon {
  font-size: 160px;
  color: #b0d5ff;
  border-radius: 20px;
  border: 4px solid #b0d5ff;
  box-shadow: 0 8px 16px rgba(176, 213, 255, 0.3);
  background: #fff;
  width: 160px;
  height: 160px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.profile-info {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 2px;
  font-family: 'Montserrat', sans-serif;
}

.profile-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: -7px;
}

.profile-info h2 {
  margin: 0;
  color: #135572;
  font-family: 'Montserrat', sans-serif;
  font-weight: 700;
  font-size: 1.8rem;
  text-transform: uppercase;
}

.profile-info h3 {
  margin: 0;
  color: #135572;
  font-family: 'Montserrat', sans-serif;
  font-weight: 700;
  font-size: 1.8rem;
  text-transform: lowercase;
}

.info-item {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.label {
  font-weight: 600;
  color: #135572;
  font-size: 0.9rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.value {
  color: #25608a;
  font-weight: 400;
  font-size: 1rem;
  line-height: 1.4;
}

.profile-divider {
  width: 100%;
  height: 2px;
  background: #CED4DA;
  margin: 32px 0;
  opacity: 0.5;
}

.edit-btn {
  padding: 10px 24px;
  background: #FF9500;
  color: #fff;
  border: none;
  border-radius: 10px;
  font-size: 0.9rem;
  font-family: 'Montserrat', sans-serif;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 4px 12px rgba(255, 149, 0, 0.2);
  white-space: nowrap;
}

.edit-btn:hover {
  background: #e68900;
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(255, 149, 0, 0.3);
}

/* Responsividade para telas menores */
@media (max-width: 768px) {
  .profile-container {
    flex-direction: column;
    align-items: center;
    text-align: center;
    gap: 24px;
  }
  
  .profile-header {
    flex-direction: column;
    gap: 16px;
    align-items: center;
  }
  
  .profile-avatar {
    width: 140px;
    height: 140px;
  }
  
  .info-item {
    align-items: center;
  }
}
</style>