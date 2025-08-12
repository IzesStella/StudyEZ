<template>
  <div class="flex h-screen bg-gray-100">
    <component :is="sidebarComponent" v-if="user" />

    <div class="w-80 bg-white border-r border-gray-200 flex flex-col">
      <div class="p-4 border-b border-gray-200">
        <input
          type="text"
          placeholder="Buscar..."
          class="w-full px-4 py-2 rounded-full bg-gray-200 focus:outline-none"
        />
      </div>
      <div class="flex-1 overflow-y-auto">
        <div
          v-for="chat in chats"
          :key="chat.id"
          @click="openChat(chat.id)"
          :class="[
            'flex items-center p-4 border-b border-gray-100 cursor-pointer hover:bg-gray-50',
            isChatActive(chat.id) ? 'bg-gray-100' : ''
          ]"
        >
          <div
            v-if="chat.user"
            class="w-12 h-12 rounded-full bg-gray-400 flex items-center justify-center text-white text-lg font-semibold flex-shrink-0"
          >
            {{ chat.user.name.charAt(0) }}
          </div>
          <div v-if="chat.user" class="ml-4 truncate">
            <p class="font-semibold text-gray-800">{{ chat.user.name }}</p>
            <p class="text-sm text-gray-500 truncate">{{ chat.lastMessage }}</p>
          </div>
        </div>
      </div>
    </div>

    <div class="flex-1 flex flex-col">
      <template v-if="currentChat && currentChat.user">
        <div class="p-4 border-b border-gray-200 bg-white">
          <div class="flex items-center">
            <div
              class="w-10 h-10 rounded-full bg-yellow-500 flex items-center justify-center text-white text-lg font-semibold"
            >
              {{ currentChat.user.name.charAt(0) }}
            </div>
            <div class="ml-3">
              <p class="font-semibold text-lg text-gray-800">{{ currentChat.user.name }}</p>
              <p class="text-sm text-gray-500">{{ currentChat.user.role }}</p>
            </div>
          </div>
        </div>

        <div ref="messageContainer" class="flex-1 overflow-y-auto p-6 space-y-4 bg-gray-50">
          <div
            v-for="message in messages"
            :key="message.id"
            :class="message.user.id === user.id ? 'flex justify-end' : 'flex'"
          >
            <div
              :class="[
                message.user.id === user.id
                  ? 'bg-blue-600 text-white'
                  : 'bg-gray-200 text-gray-800',
                'p-3 rounded-lg max-w-sm'
              ]"
            >
              <p>{{ message.content }}</p>
            </div>
          </div>
        </div>

        <div class="p-4 border-t border-gray-200 bg-white">
          <div class="flex items-center space-x-2">
            <input
              type="text"
              v-model="newMessage"
              @keyup.enter="sendMessage"
              placeholder="Mensagem"
              class="flex-1 px-4 py-3 rounded-full border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
            <button
              @click="sendMessage"
              class="bg-blue-600 text-white p-3 rounded-full hover:bg-blue-700 transition"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-6 w-6"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M14 5l7 7m0 0l-7 7m7-7H3"
                />
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
import { defineProps, computed, ref, onMounted, onUnmounted, watch, nextTick } from 'vue'
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
  }
})

const user = computed(() => usePage().props.auth.user)
const newMessage = ref('')
const messageContainer = ref(null);
let socket = null

const sidebarComponent = computed(() => {
  return user.value.role === 'monitor' ? SideBarMonitor : SideBar
})

function openChat(chatId) {
  router.get(`/chat/${chatId}`)
}

function isChatActive(chatId) {
  return props.currentChat && props.currentChat.id === chatId
}

function sendMessage() {
  if (!newMessage.value.trim() || !props.currentChat) {
    return
  }

  // AQUI: Armazenamos o valor da mensagem antes de limpar o campo
  const messageToSend = newMessage.value;

  // AQUI: Limpamos o campo de texto imediatamente
  newMessage.value = '';

  router.post(
    '/chat/send',
    {
      chat_id: props.currentChat.id,
      content: messageToSend
    },
    {
      preserveScroll: true,
      onSuccess: () => {
        // A lógica de recarga já está sendo tratada pelo WebSocket,
        // então não precisamos fazer mais nada aqui.
      }
    }
  )
}

function scrollToBottom() {
  nextTick(() => {
    if (messageContainer.value) {
      messageContainer.value.scrollTop = messageContainer.value.scrollHeight;
    }
  });
}

// O watcher para rolar a tela quando as mensagens são atualizadas
watch(() => props.messages, () => {
  scrollToBottom();
}, { immediate: true });


onMounted(() => {
  if (props.currentChat) {
    const channelName = `chat.${props.currentChat.id}`;
    const appKey = 'minha-chave-app';
    const socketUrl = `ws://127.0.0.1:6001/app/${appKey}?protocol=7&client=js&version=4.4.0&flash=false`;

    socket = new WebSocket(socketUrl);

    socket.onopen = () => {
      console.log('WebSocket conectado');
      socket.send(
        JSON.stringify({
          event: 'pusher:subscribe',
          data: {
            channel: channelName
          }
        })
      );
    };
    
    socket.onmessage = (event) => {
      try {
        const payload = JSON.parse(event.data);
        console.log('Mensagem WebSocket recebida:', payload); 

        if (payload.event === 'pusher_internal:subscription_succeeded') {
            console.log(`Inscrito com sucesso no canal: ${payload.channel}`);
        }
        
        // Adicionada a verificação para o formato de evento direto.
        if (payload.event === 'pusher:event' && payload.data) {
          const eventData = JSON.parse(payload.data);
          if (eventData.event === 'App\\Events\\NewChatMessage') {
            console.log('Evento de nova mensagem recebido via WebSocket. Recarregando dados...');
            router.reload({ only: ['messages'], preserveScroll: true });
          }
        } 
        else if (payload.event === 'NewChatMessage' && payload.channel === channelName) {
            console.log('Evento de nova mensagem recebido diretamente. Recarregando dados...');
            router.reload({ only: ['messages'], preserveScroll: true });
        }
        
      } catch (e) {
        console.error('Erro WS:', e);
      }
    };

    socket.onerror = (err) => {
      console.error('Erro WebSocket:', err);
    };
  }
});

onUnmounted(() => {
  if (socket) {
    socket.close();
  }
});
</script>
