<script setup>
import PlannerBar from '@/Components/PlannerBar.vue'
import SideBar from '@/Components/SideBar.vue'
import SideBarMonitor from '@/Components/SideBarMonitor.vue'
import CreateCommunity from '@/Components/CreateCommunity.vue'
import EditProfileModal from '@/Components/EditProfileModal.vue'
import { usePage, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import LogoStudyEZ from '@/Components/LogoStudyEZ.vue'

const props = defineProps({
  user: Object,
  communities: {
    type: Array,
    default: () => []
  },
  posts: {
    type: Array,
    default: () => []
  },
  comments: {
    type: Array,
    default: () => []
  },
  isOwnProfile: {
    type: Boolean,
    default: true
  }
})

const authUser = usePage().props.auth.user
const sidebarComponent = authUser.role === 'monitor' ? SideBarMonitor : SideBar
const showCreateCommunity = ref(false)
const showEditProfile = ref(false)

function goToEdit() {
  showEditProfile.value = true
}

function goToPost(post) {
  router.visit(`/community/${post.community.id}#post-${post.id}`)
}

function goToCommentPost(comment) {
  if (comment.community) {
    router.visit(`/community/${comment.community.id}#post-${comment.post.id}`)
  }
}

function goToCommunity(community) {
  router.visit(`/community/${community.id}`)
}
</script>

<template>
  <LogoStudyEZ />
  <div class="dashboard-layout">
    <component :is="sidebarComponent" @open-create-community="showCreateCommunity = true" />
    <div class="profile-page">
      <div class="profile-container">
        <template v-if="props.user.profile_photo">
          <img class="profile-avatar" :src="`/storage/${props.user.profile_photo}`" alt="Avatar" />
        </template>
        <template v-else>
          <font-awesome-icon :icon="['fas', 'user']" class="profile-avatar-icon" />
        </template>
        <div class="profile-info">
          <div class="profile-header">
            <h2>{{ props.user.name }}</h2>
            <button v-if="isOwnProfile" class="edit-btn" @click="goToEdit">Editar Perfil</button>
          </div>
          <div class="info-item">
            <h3>{{ (props.user.role === 'monitor' ? 'monitor' : 'aluno') }}</h3>
          </div>
          <div class="info-item" v-if="props.user.bio">
            <span class="value">{{ props.user.bio }}</span>
          </div>
          <div class="info-item" v-else>
            <span>Nenhuma bio adicionada ainda.</span>
          </div>
        </div>
      </div>
      
      <!-- Seção de Comunidades (apenas para monitores) -->
      <div v-if="props.user.role === 'monitor' && communities.length > 0" class="profile-section">
        <h3 class="section-title">Comunidades Criadas</h3>
        <div class="items-grid">
          <div 
            v-for="community in communities" 
            :key="community.id" 
            class="item-card community-card"
            @click="goToCommunity(community)"
          >
            <div class="item-header">
              <h4 class="item-title">{{ community.name }}</h4>
              <span class="item-date">{{ community.created_at }}</span>
            </div>
            <p class="item-content">{{ community.description }}</p>
            <div class="item-stats">
              <span class="stat-item">
                <font-awesome-icon :icon="['fas', 'file-alt']" />
                {{ community.posts_count }} {{ community.posts_count === 1 ? 'post' : 'posts' }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Seção de Posts -->
      <div v-if="posts.length > 0" class="profile-section">
        <h3 class="section-title">Posts</h3>
        <div class="items-grid">
          <div 
            v-for="post in posts" 
            :key="post.id" 
            class="item-card post-card"
            @click="goToPost(post)"
          >
            <div class="item-header">
              <h4 class="item-title">{{ post.title }}</h4>
              <span class="item-date">{{ post.created_at }}</span>
            </div>
            <p class="item-content">{{ post.content.substring(0, 150) }}{{ post.content.length > 150 ? '...' : '' }}</p>
            <div class="item-stats">
              <span class="community-name">
                <font-awesome-icon :icon="['fas', 'users']" />
                {{ post.community.name }}
              </span>
              <span class="stat-item">
                <font-awesome-icon :icon="['fas', 'comments']" />
                {{ post.comments_count }} {{ post.comments_count === 1 ? 'comentário' : 'comentários' }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Seção de Comentários -->
      <div v-if="comments.length > 0" class="profile-section">
        <h3 class="section-title">Comentários</h3>
        <div class="items-grid">
          <div 
            v-for="comment in comments" 
            :key="comment.id" 
            class="item-card comment-card"
            @click="goToCommentPost(comment)"
          >
            <div class="item-header">
              <h4 class="item-title">Comentário em: {{ comment.post.title }}</h4>
              <span class="item-date">{{ comment.created_at }}</span>
            </div>
            <p class="item-content">{{ comment.content }}</p>
            <div class="item-stats">
              <span class="community-name" v-if="comment.community">
                <font-awesome-icon :icon="['fas', 'users']" />
                {{ comment.community.name }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Mensagem quando não há conteúdo -->
      <div v-if="posts.length === 0 && comments.length === 0 && (props.user.role !== 'monitor' || communities.length === 0)" class="no-content">
        <font-awesome-icon :icon="['fas', 'inbox']" class="no-content-icon" />
        <p>Nenhuma atividade encontrada ainda.</p>
        <p class="no-content-subtitle">
          {{ props.user.role === 'monitor' 
             ? 'Comece criando comunidades e fazendo posts!' 
             : 'Comece participando de comunidades e fazendo comentários!' 
          }}
        </p>
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
}

.profile-info h3 {
  margin: 0;
  color: #135572;
  font-family: 'Montserrat', sans-serif;
  font-weight: 700;
  font-size: 1.4 rem;
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

/* Seções do perfil */
.profile-section {
  margin: 32px 0;
}

.section-title {
  font-family: 'Montserrat', sans-serif;
  font-size: 1.5rem;
  font-weight: 700;
  color: #135572;
  margin-bottom: 20px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.items-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 20px;
}

.item-card {
  background: #fff;
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 4px 12px rgba(19, 85, 114, 0.1);
  border: 1px solid #e8f4f8;
  transition: all 0.3s ease;
  cursor: pointer;
}

.item-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 24px rgba(19, 85, 114, 0.15);
  border-color: #b0d5ff;
}

.community-card {
  border-left: 4px solid #FF9500;
}

.post-card {
  border-left: 4px solid #135572;
}

.comment-card {
  border-left: 4px solid #25608a;
}

.item-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 12px;
  gap: 12px;
}

.item-title {
  font-family: 'Montserrat', sans-serif;
  font-size: 1.1rem;
  font-weight: 600;
  color: #135572;
  margin: 0;
  line-height: 1.3;
  flex: 1;
}

.item-date {
  font-size: 0.85rem;
  color: #6c757d;
  font-weight: 500;
  white-space: nowrap;
}

.item-content {
  font-family: 'Montserrat', sans-serif;
  color: #25608a;
  font-size: 0.95rem;
  line-height: 1.5;
  margin: 0 0 16px 0;
}

.item-stats {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 12px;
}

.community-name {
  font-size: 0.9rem;
  color: #FF9500;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 6px;
}

.stat-item {
  font-size: 0.9rem;
  color: #6c757d;
  font-weight: 500;
  display: flex;
  align-items: center;
  gap: 6px;
}

.no-content {
  text-align: center;
  padding: 60px 20px;
  color: #6c757d;
}

.no-content-icon {
  font-size: 4rem;
  color: #b0d5ff;
  margin-bottom: 20px;
}

.no-content p {
  font-family: 'Montserrat', sans-serif;
  font-size: 1.1rem;
  margin: 8px 0;
}

.no-content-subtitle {
  font-size: 0.95rem !important;
  color: #8a9aa9 !important;
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

  .items-grid {
    grid-template-columns: 1fr;
  }
  
  .item-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 8px;
  }
  
  .item-stats {
    flex-direction: column;
    align-items: flex-start;
    gap: 8px;
  }
}
</style>