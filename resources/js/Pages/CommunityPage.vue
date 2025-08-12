<template>
  <LogoStudyEZ />
  <div class="max-w-5xl mx-auto p-6">
    <component :is="sidebarComponent" @open-create-community="showCreateCommunity = true" />
    <CreateCommunity v-if="showCreateCommunity" @close="showCreateCommunity = false" />
    <PlannerBar v-if="user && user.role === 'student'" />

    <WarningModal v-if="showWarningModal" @close="showWarningModal = false" />

    <CreatePostModal
      v-if="showCreatePostModal"
      @close="showCreatePostModal = false"
      :community="community"
    />

    <CommentModal
      v-if="showCommentModal"
      @close="showCommentModal = false"
      @comment-added="handleCommentAdded"
      :post="selectedPostForComment"
    />

    <div
      class="community-header bg-blue-800 text-white rounded-lg p-6 mb-8 flex flex-col md:flex-row justify-between items-start md:items-center"
    >
      <div class="flex-1 min-w-0">
        <h1 class="text-3xl font-bold">{{ community.name }}</h1>
        <p class="text-sm mt-2">Monitor: {{ community.creator.name }}</p>
        <p class="mt-6">{{ community.description }}</p>
      </div>
      <div class="mt-4 md:mt-0 button-group flex-shrink-0 md:ml-6">
        <button
          v-if="user && isStudent && !isSubscribed"
          @click="subscribe"
          class="btn-subscribe"
        >
          Inscrever-se
        </button>
        <button
          v-if="user && isStudent && isSubscribed"
          @click="unsubscribe"
          class="btn-unsubscribe"
        >
          Desinscrever-se
        </button>
        
        <button
          v-if="user && isStudent && isSubscribed"
          @click="startChat"
          class="btn-action"
        >
          Iniciar Chat
        </button>

        <button
          v-if="user && (isStudent || (user.role === 'monitor' && user.id === community.creator.id))"
          @click="handlePostClick"
          class="btn-action"
        >
          + Postar
        </button>
      </div>
    </div>

    <div v-if="localPosts.length === 0" class="text-center text-gray-500 py-10">
      Nenhum post ainda. Seja o primeiro a postar!
    </div>
    <div v-else v-for="post in localPosts" :key="post.id" :id="`post-${post.id}`" class="post-card border-b border-gray-200 pb-6 mb-6">
      <div class="flex items-center mb-2">
        <template v-if="post.user.profile_photo">
          <img 
            :src="`/storage/${post.user.profile_photo}`" 
            :alt="post.user.name" 
            class="w-10 h-10 rounded-full object-cover border-2 cursor-pointer hover:opacity-80 transition"
            :class="post.user.role === 'monitor' ? 'border-yellow-500' : 'border-orange-500'"
            @click="goToProfile(post.user.id)"
          />
        </template>
        <template v-else>
          <div
            class="avatar w-10 h-10 rounded-full flex items-center justify-center text-white font-bold cursor-pointer hover:opacity-80 transition"
            :class="post.user.role === 'monitor' ? 'avatar-monitor' : 'avatar-student'"
            @click="goToProfile(post.user.id)"
          >
            {{ post.user.name.charAt(0) }}
          </div>
        </template>
        <div class="ml-3">
          <p
            class="font-semibold flex items-center cursor-pointer hover:underline transition"
            :class="post.user.role === 'monitor' ? 'text-yellow-600' : 'text-gray-900'"
            @click="goToProfile(post.user.id)"
          >
            {{ post.user.name }}
            <span v-if="post.user.role === 'monitor'" class="ml-2">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-500" viewBox="0 0 20 20" fill="currentColor">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
              </svg>
            </span>
          </p>
          <p class="text-sm text-gray-500">
            {{ new Date(post.created_at).toLocaleString() }}
          </p>
        </div>
      </div>
      <h2 class="text-xl font-semibold mb-2">{{ post.title }}</h2>
      <p class="text-gray-700 mb-4">{{ post.content }}</p>
      
      <button
        @click="openCommentModal(post)"
        class="flex items-center text-gray-500 hover:text-gray-700 transition"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-6 w-6 mr-1"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8-1.446 0-2.813-.253-4.033-.707L3 20l1.293-4.657C3.253 14.813 3 13.446 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"
          />
        </svg>
        <span>Comentários ({{ post.comments ? post.comments.length : 0 }})</span>
      </button>

      <div v-if="post.comments && post.comments.length > 0" class="comments-section mt-6">
        <div class="comments-header">
          <h4 class="comments-title">Comentários</h4>
        </div>
        <div class="comments-list">
          <div v-for="comment in post.comments" :key="comment.id" class="comment-item">
            <div class="comment-avatar-container">
              <template v-if="comment.user.profile_photo">
                <img 
                  :src="`/storage/${comment.user.profile_photo}`" 
                  :alt="comment.user.name" 
                  class="comment-avatar-image cursor-pointer hover:opacity-80 transition"
                  @click="goToProfile(comment.user.id)"
                />
              </template>
              <template v-else>
                <div
                  class="comment-avatar-fallback cursor-pointer hover:opacity-80 transition"
                  :class="comment.user.role === 'monitor' ? 'avatar-monitor' : 'avatar-student'"
                  @click="goToProfile(comment.user.id)"
                >
                  {{ comment.user.name.charAt(0) }}
                </div>
              </template>
            </div>
            <div class="comment-content">
              <div class="comment-header">
                <p 
                  class="comment-author cursor-pointer hover:underline transition"
                  :class="comment.user.role === 'monitor' ? 'text-yellow-600 font-semibold' : 'text-gray-800 font-semibold'"
                  @click="goToProfile(comment.user.id)"
                >
                  {{ comment.user.name }}
                  <span v-if="comment.user.role === 'monitor'" class="monitor-badge">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-yellow-500" viewBox="0 0 20 20" fill="currentColor">
                      <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                  </span>
                </p>
                <span class="comment-date">{{ new Date(comment.created_at).toLocaleString() }}</span>
              </div>
              <p class="comment-text">{{ comment.content }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import PlannerBar from '@/Components/PlannerBar.vue'
import SideBar from '@/Components/SideBar.vue'
import SideBarMonitor from '@/Components/SideBarMonitor.vue'
import CreateCommunity from '@/Components/CreateCommunity.vue'
import CreatePostModal from '@/Components/CreatePostModal.vue'
import LogoStudyEZ from '@/Components/LogoStudyEZ.vue'
import WarningModal from '@/Components/WarningModal.vue'
import CommentModal from '@/Components/CommentModal.vue'

import { defineProps, ref, watch, onMounted, nextTick } from 'vue'
import { router, usePage } from '@inertiajs/vue3'

const props = defineProps({
  community: { type: Object, required: true },
  user: { type: Object, required: false, default: null },
  isSubscribed: { type: Boolean, required: false, default: false },
  posts: { type: Array, required: true, default: () => [] },
})

// Funcionalidade de scroll automático para posts específicos
onMounted(() => {
  nextTick(() => {
    const hash = window.location.hash
    if (hash && hash.startsWith('#post-')) {
      const element = document.querySelector(hash)
      if (element) {
        element.scrollIntoView({ behavior: 'smooth', block: 'center' })
        // Adiciona um destaque temporário ao post
        element.classList.add('highlighted-post')
        setTimeout(() => {
          element.classList.remove('highlighted-post')
        }, 3000)
      }
    }
  })
})

const localPosts = ref([...props.posts])
watch(() => props.posts, newPosts => {
  localPosts.value = [...newPosts]
}, { deep: true, immediate: true })

const showCommentModal = ref(false)
const selectedPostForComment = ref(null)

const sidebarComponent = props.user?.role === 'monitor' ? SideBarMonitor : SideBar
const showCreateCommunity = ref(false)
const isStudent = props.user?.role === 'student'
const showCreatePostModal = ref(false)
const showWarningModal = ref(false)

function subscribe() {
  router.post(`/communities/${props.community.id}/subscribe`, {}, { preserveScroll: true })
}
function unsubscribe() {
  router.delete(`/communities/${props.community.id}/unsubscribe`, {}, { preserveScroll: true })
}
function goToProfile(userId) {
  router.get(`/profile/${userId}`)
}
function handlePostClick() {
  if (props.user.role === 'student' && !props.isSubscribed) {
    showWarningModal.value = true
  } else {
    showCreatePostModal.value = true
  }
}
function openCommentModal(post) {
  selectedPostForComment.value = post
  showCommentModal.value = true
}

// A função handleCommentAdded corrigida para usar Inertia.js
function handleCommentAdded() {
  router.reload({ only: ['posts'], preserveScroll: true })
  showCommentModal.value = false
}

function startChat() {
    router.post(`/chat/start/${props.community.creator.id}`, {}, {
        preserveScroll: true,
        onSuccess: () => {
            // O redirecionamento será feito automaticamente pelo backend
        }
    });
}
</script>

<style scoped>
.community-header {
    background: #1e3a8a; 
}

.button-group {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
    margin-top: 1rem;
    align-items: center; /* Alinha os botões verticalmente no centro */
    justify-content: flex-end; /* Alinha os botões à direita */
}
@media (min-width: 768px) {
    .button-group {
        margin-top: 0;
        margin-left: 1.5rem; /* Adiciona um espaço à esquerda em telas maiores */
    }
}

.btn-unsubscribe {
    background-color: #dc2626; 
    color: white;
    font-weight: 600;
    padding: 0.5rem 1.25rem;
    border-radius: 0.5rem;
    transition: all 0.2s ease-in-out;
}
.btn-unsubscribe:hover {
    background-color: #b91c1c; 
}

.btn-action {
    background-color: #2563eb; 
    color: white;
    font-weight: 600;
    padding: 0.5rem 1.25rem;
    border-radius: 0.5rem;
    transition: all 0.2s ease-in-out;
}
.btn-action:hover {
    background-color: #1d4ed8; 
}

.btn-subscribe {
    background-color: white;
    color: #1e3a8a;
    font-weight: 600;
    padding: 0.5rem 1.25rem;
    border-radius: 0.5rem;
    transition: all 0.2s ease-in-out;
}
.btn-subscribe:hover {
    background-color: #f3f4f6;
}

/* Os posts individuais agora são itens dentro do container */
.post-card {
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1), 0 1px 2px rgba(0, 0, 0, 0.06);
    padding: 1.5rem;
    margin-bottom: 1.5rem;
}

.avatar-monitor {
    background-color: #f59e0b; 
}
.avatar-student {
    background-color: #ea580c;
}

/* Nova seção de comentários */
.comments-section {
    border-top: 1px solid #e5e7eb;
    padding-top: 1.5rem;
    margin-top: 1.5rem;
}

.comments-header {
    margin-bottom: 1rem;
}

.comments-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #374151;
    font-family: 'Montserrat', sans-serif;
}

.comments-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.comment-item {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    padding: 1rem;
    background-color: #f9fafb;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    transition: all 0.2s ease;
    background-color: #e7f4ff;
}

.comment-item:hover {
    background-color: #f3f4f6;
    border-color: #d1d5db;
}

.comment-avatar-container {
    flex-shrink: 0;
}

.comment-avatar-image {
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #e5e7eb;
}

.comment-avatar-fallback {
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    font-size: 1rem;
    border: 2px solid #e5e7eb;
}

.comment-content {
    flex: 1;
    min-width: 0;
}

.comment-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.comment-author {
    font-size: 0.9rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.25rem;
    font-family: 'Montserrat', sans-serif;
}

.monitor-badge {
    display: inline-flex;
    align-items: center;
}

.comment-date {
    font-size: 0.75rem;
    color: #6b7280;
    font-weight: 500;
}

.comment-text {
    font-size: 0.9rem;
    color: #374151;
    line-height: 1.5;
    word-wrap: break-word;
    font-family: 'Montserrat', sans-serif;
}

/* Responsividade para comentários */
@media (max-width: 640px) {
    .comment-item {
        padding: 0.75rem;
        gap: 0.5rem;
    }
    
    .comment-avatar-image,
    .comment-avatar-fallback {
        width: 2rem;
        height: 2rem;
        font-size: 0.875rem;
    }
    
    .comment-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.25rem;
    }
}

/* Destaque para posts navegados */
.highlighted-post {
    background-color: #fef3c7;
    border: 2px solid #f59e0b;
    border-radius: 8px;
    transition: all 0.3s ease;
}
</style>