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
    <div v-else v-for="post in localPosts" :key="post.id" class="post-card border-b border-gray-200 pb-6 mb-6">
      <div class="flex items-center mb-2">
        <div
          class="avatar w-10 h-10 rounded-full flex items-center justify-center text-white font-bold"
          :class="post.user.role === 'monitor' ? 'avatar-monitor' : 'avatar-student'"
        >
          {{ post.user.name.charAt(0) }}
        </div>
        <div class="ml-3">
          <p
            class="font-semibold flex items-center"
            :class="post.user.role === 'monitor' ? 'text-yellow-600' : 'text-gray-900'"
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

      <div v-if="post.comments && post.comments.length > 0" class="comment-list">
        <div v-for="comment in post.comments" :key="comment.id" class="flex items-start comment-item">
          <div
            class="comment-avatar w-8 h-8 rounded-full flex-shrink-0 flex items-center justify-center text-white font-bold text-sm bg-gray-500"
          >
            {{ comment.user.name.charAt(0) }}
          </div>
          <div class="ml-3 bg-gray-100 rounded-lg p-3 flex-grow comment-text-box">
            <p class="font-semibold text-gray-800">{{ comment.user.name }}</p>
            <p class="text-gray-700 break-words">{{ comment.content }}</p>
            <p class="text-xs text-gray-500 mt-1">{{ new Date(comment.created_at).toLocaleString() }}</p>
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

import { defineProps, ref, watch } from 'vue'
import { router, usePage } from '@inertiajs/vue3'

const props = defineProps({
  community: { type: Object, required: true },
  user: { type: Object, required: false, default: null },
  isSubscribed: { type: Boolean, required: false, default: false },
  posts: { type: Array, required: true, default: () => [] },
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

// AAdicionada a nova função startChat
function startChat() {
  // Faz a navegação para a rota de chat
  // `props.community.creator.id` obtém o ID do monitor
  router.get(`/chat/${props.community.creator.id}`);
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

.comment-list {
    margin-left: 3rem;
    margin-top: 1rem;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.comment-item {
    display: flex;
    align-items: flex-start;
    width: 100%;
}

.comment-avatar {
    width: 2rem;
    height: 2rem;
    border-radius: 9999px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    font-size: 0.875rem;
    background-color: #6b7280;
}
.comment-text-box {
    background-color: #f3f4f6;
    border-radius: 8px;
    padding: 0.75rem;
    flex-grow: 1;
    overflow-wrap: break-word;
}
</style>