<template>
  <div class="flex h-screen bg-gray-100">

    <component :is="sidebarComponent" v-if="user" />

    <div class="w-80 bg-white border-r border-gray-200 flex flex-col">
      <div class="p-4 border-b border-gray-200">
        <input type="text" placeholder="Buscar..." class="w-full px-4 py-2 rounded-full bg-gray-200 focus:outline-none" />
      </div>
      <div class="flex-1 overflow-y-auto">
        <div 
          v-for="chat in chats" 
          :key="chat.user.id" 
          @click="openChat(chat.user.id)"
          :class="['flex items-center p-4 border-b border-gray-100 cursor-pointer hover:bg-gray-50', isChatActive(chat.user.id) ? 'bg-gray-100' : '']"
        >
          <div class="w-12 h-12 rounded-full bg-gray-400 flex items-center justify-center text-white text-lg font-semibold flex-shrink-0">
            {{ chat.user.name.charAt(0) }}
          </div>
          <div class="ml-4 truncate">
            <p class="font-semibold text-gray-800">{{ chat.user.name }}</p>
            <p class="text-sm text-gray-500 truncate">{{ chat.lastMessage }}</p>
          </div>
        </div>
      </div>
    </div>

    <div class="flex-1 flex flex-col">
      <template v-if="currentChat">
        <div class="p-4 border-b border-gray-200 bg-white">
          <div class="flex items-center">
            <div class="w-10 h-10 rounded-full bg-yellow-500 flex items-center justify-center text-white text-lg font-semibold">
              {{ currentChat.user.name.charAt(0) }}
            </div>
            <div class="ml-3">
              <p class="font-semibold text-lg text-gray-800">{{ currentChat.user.name }}</p>
              <p class="text-sm text-gray-500">Monitor</p>
            </div>
          </div>
        </div>
        <div class="flex-1 overflow-y-auto p-6 space-y-4 bg-gray-50">
          <div v-for="message in messages" :key="message.id" :class="message.sender_id === user.id ? 'flex justify-end' : 'flex'">
            <div :class="[message.sender_id === user.id ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-800', 'p-3 rounded-lg max-w-sm']">
              <p>{{ message.content }}</p>
            </div>
          </div>
        </div>
        <div class="p-4 border-t border-gray-200 bg-white">
          <div class="flex items-center space-x-2">
            <input
              type="text"
              placeholder="Mensagem"
              class="flex-1 px-4 py-3 rounded-full border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
            <button class="bg-blue-600 text-white p-3 rounded-full hover:bg-blue-700 transition">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
              </svg>
            </button>
          </div>
        </div>
      </template>

      <div v-else class="flex-1 flex items-center justify-center text-gray-400">
        Selecione um chat para começar a conversa.
      </div>
    </div>
  </div>
</template>

<script setup>
import { defineProps, computed } from 'vue'
import { router, usePage } from '@inertiajs/vue3'

import SideBar from '@/Components/SideBar.vue'
import SideBarMonitor from '@/Components/SideBarMonitor.vue'

const props = defineProps({
  chats: {
    type: Array,
    required: true,
    default: () => []
  },
  currentChat: {
    type: Object,
    default: null
  },
  messages: {
    type: Array,
    default: () => []
  },
})

const user = computed(() => usePage().props.auth.user)

const sidebarComponent = computed(() => {
  return user.value.role === 'monitor' ? SideBarMonitor : SideBar
})

// Função para navegar para o chat ao clicar na lista
function openChat(monitorId) {
  router.get(`/chat/${monitorId}`);
}

// Verifica se o chat atual está selecionado na lista
function isChatActive(monitorId) {
  return props.currentChat && props.currentChat.user.id === monitorId;
}
</script>