<template>
  <div class="chat-wrapper">
    <LogoStudyEZ />
  
    <div class="chat-page">
      <!-- Sidebar -->
      <component :is="sidebarComponent" v-if="user" />

      <!-- Main Chat Container -->
      <div class="chat-container">
      <!-- Chat List Sidebar -->
      <div class="chat-list">
        <!-- Header da Lista de Chats -->

        <div class="chat-list-header">
          <h2>Mensagens</h2>
          <input
            type="text"
            placeholder="Buscar conversas..."
            class="search-input"
          />
        </div>

        <!-- Lista de Chats -->
        <div class="chat-list-body">
          <div
            v-for="chat in chats"
            :key="chat.id"
            @click="openChat(chat.id)"
            :class="[
              'chat-item',
              isChatActive(chat.id) ? 'chat-item-active' : ''
            ]"
          >
            <!-- Avatar -->
            <div class="chat-avatar">
              <template v-if="chat.user && chat.user.profile_photo">
                <img 
                  :src="`/storage/${chat.user.profile_photo}`" 
                  :alt="chat.user.name" 
                  class="avatar-image"
                />
              </template>
              <template v-else-if="chat.user">
                <div class="avatar-fallback">
                  {{ chat.user.name.charAt(0) }}
                </div>
              </template>
            </div>

            <!-- Info do Chat -->
            <div v-if="chat.user" class="chat-info">
              <div class="chat-name">
                <span class="user-name">
                  {{ chat.user.name }}
                </span>
              </div>
              <p class="chat-last-message">{{ chat.lastMessage || 'Iniciar conversa' }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Area de Conversa -->
      <div class="chat-area">
        <template v-if="currentChat && currentChat.user">
          <!-- Header da Conversa -->
          <div class="chat-header">
            <!-- Avatar do Usuário Atual -->
            <div class="header-avatar">
              <template v-if="currentChat.user && currentChat.user.profile_photo">
                <img 
                  :src="`/storage/${currentChat.user.profile_photo}`" 
                  :alt="currentChat.user.name" 
                  class="avatar-image"
                />
              </template>
              <template v-else>
                <div class="avatar-fallback">
                  {{ currentChat.user.name.charAt(0) }}
                </div>
              </template>
            </div>

            <!-- Info do Usuário -->
            <div class="header-info">
              <div class="header-name">
                <span class="user-name">
                  {{ currentChat.user.name }}
                </span>
              </div>
              <p class="header-role">{{ currentChat.user.role === 'monitor' ? 'Monitor' : 'Aluno' }}</p>
            </div>
          </div>

          <!-- Area de Mensagens -->
          <div ref="messageContainer" class="messages-container">
            <div
              v-for="message in messages"
              :key="message.id"
              :class="[
                'message-wrapper',
                message.user.id === user.id ? 'message-sent' : 'message-received'
              ]"
            >
              <!-- Balão da Mensagem -->
              <div
                :class="[
                  'message-bubble',
                  message.user.id === user.id ? 'message-bubble-sent' : 'message-bubble-received'
                ]"
              >
                <p class="message-content">{{ message.content }}</p>
                <span class="message-time">
                  {{ new Date(message.created_at).toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' }) }}
                </span>
              </div>
            </div>
          </div>

          <!-- Input de Mensagem -->
          <div class="message-input-container">
            <div class="message-input-wrapper">
              <input
                type="text"
                v-model="newMessage"
                @keyup.enter="sendMessage"
                placeholder="Digite sua mensagem..."
                class="message-input"
              />
              <button
                @click="sendMessage"
                :disabled="!newMessage.trim()"
                class="send-button"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="send-icon"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"
                  />
                </svg>
              </button>
            </div>
          </div>
        </template>

        <!-- Estado Vazio -->
        <div v-else class="empty-state">
          <div class="empty-icon">💬</div>
          <h3>Selecione uma conversa</h3>
          <p>Escolha um chat para começar a conversar</p>
        </div>
      </div>
    </div>
  </div>
  </div>
</template>

<script setup>
import { defineProps, computed, ref, onMounted, onUnmounted, watch, nextTick } from 'vue'
import { router, usePage } from '@inertiajs/vue3'

import SideBar from '@/Components/SideBar.vue'
import SideBarMonitor from '@/Components/SideBarMonitor.vue'
import LogoStudyEZ from '@/Components/LogoStudyEZ.vue'
import PlannerBar from '@/Components/PlannerBar.vue'

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

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap');

/* Wrapper Principal */
.chat-wrapper {
  min-height: 100vh;
  background: #e8f4f8;
}

/* Layout Principal */
.chat-page {
  display: flex;
  height:100vh;
  background: #e8f4f8;
  font-family: 'Inter', sans-serif;
  padding: 10px;
}

.chat-container {
  display: flex;
  flex: 1;
  margin-left: 120px;
  margin-right: 80px;
  height: 100%;
  background: transparent;
  gap: 10px;
}

/* Lista de Chats */
.chat-list {
  width: 320px;
  background: #ffffff;
  display: flex;
  flex-direction: column;
  border-radius: 12px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  height: 100%;
}

.chat-list-header {
  padding: 20px;
  background: #ffffff;
  border-radius: 12px 12px 0 0;
}

.chat-list-header h2 {
  font-family: 'Montserrat', sans-serif;
  font-size: 18px;
  font-weight: 600;
  color: #2d3748;
  margin: 0 0 12px 0;
  text-align: left;
}

.search-input {
  width: 100%;
  padding: 8px 12px;
  border: 1px solid #e2e8f0;
  border-radius: 20px;
  font-size: 13px;
  background: #f7fafc;
  color: #4a5568;
  transition: all 0.2s ease;
}

.search-input:focus {
  outline: none;
  border-color: #4299e1;
  background: white;
}

.chat-list-body {
  flex: 1;
  overflow-y: auto;
}

/* Item de Chat */
.chat-item {
  display: flex;
  align-items: center;
  padding: 12px 20px;
  cursor: pointer;
  transition: background-color 0.2s ease;
  border-bottom: 1px solid #f1f5f9;
}

.chat-item:hover {
  background: #f8fafc;
}

.chat-item-active {
  background: #e6fffa;
  border-left: 4px solid #38b2ac;
}

.chat-avatar {
  flex-shrink: 0;
  margin-right: 15px;
}

.avatar-image {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  object-fit: cover;
}

.avatar-fallback {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-weight: 600;
  font-size: 14px;
  background: #a0aec0;
}

.chat-info {
  flex: 1;
  min-width: 0;
}

.chat-name {
  display: flex;
  align-items: center;
  gap: 5px;
  font-weight: 600;
  font-size: 15px;
  color: #1f2937;
  margin-bottom: 4px;
}

.user-name {
  color: #1f2937;
}

.chat-last-message {
  font-size: 13px;
  color: #6b7280;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  margin: 0;
}

/* Area de Conversa */
.chat-area {
  flex: 1;
  display: flex;
  flex-direction: column;
  background: #ffffff;
  border-radius: 12px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  overflow: hidden;
  height: 100%;
}

/* Header da Conversa */
.chat-header {
  padding: 15px 20px;
  background: #bee3f8;
  display: flex;
  align-items: center;
}

.header-avatar {
  margin-right: 12px;
}

.header-avatar .avatar-image,
.header-avatar .avatar-fallback {
  width: 40px;
  height: 40px;
  border-radius: 50%;
}

.header-avatar .avatar-image {
  object-fit: cover;
}

.header-avatar .avatar-fallback {
  background: #4a5568;
}

.header-info {
  flex: 1;
}

.header-name {
  display: flex;
  align-items: center;
  gap: 8px;
  font-family: 'Inter', sans-serif;
  font-size: 16px;
  font-weight: 600;
  color: #2d3748;
  margin-bottom: 2px;
}

.header-name .user-name {
  color: #2d3748;
}

.header-role {
  font-size: 12px;
  color: #4a5568;
  font-weight: 400;
  margin: 0;
}

/* Area de Mensagens */
.messages-container {
  flex: 1;
  overflow-y: auto;
  padding: 20px;
  background: #ffffff;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.message-wrapper {
  display: flex;
  align-items: flex-start;
  max-width: 80%;
}

.message-sent {
  align-self: flex-end;
  flex-direction: row-reverse;
}

.message-received {
  align-self: flex-start;
}

/* Balões de Mensagem */
.message-bubble {
  padding: 8px 12px;
  border-radius: 8px;
  position: relative;
  word-wrap: break-word;
  max-width: 100%;
}

.message-bubble-sent {
  background: #90cdf4;
  color: #2d3748;
}

.message-bubble-received {
  background: #e2e8f0;
  color: #2d3748;
}

.message-content {
  font-size: 14px;
  line-height: 1.4;
  margin: 0 0 4px 0;
}

.message-time {
  font-size: 10px;
  opacity: 0.6;
  display: block;
  text-align: right;
}

.message-bubble-received .message-time {
  color: #4a5568;
}

/* Input de Mensagem */
.message-input-container {
  padding: 15px 20px;
  background: #f7fafc;
  border-top: 1px solid #e2e8f0;
}

.message-input-wrapper {
  display: flex;
  gap: 10px;
  align-items: center;
}

.message-input {
  flex: 1;
  padding: 8px 12px;
  border: 1px solid #e2e8f0;
  border-radius: 20px;
  font-size: 13px;
  background: white;
  transition: all 0.2s ease;
  color: #4a5568;
}

.message-input:focus {
  outline: none;
  border-color: #4299e1;
}

.message-input::placeholder {
  color: #a0aec0;
}

.send-button {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  border: none;
  background: #4299e1;
  color: white;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s ease;
}

.send-button:hover:not(:disabled) {
  background: #3182ce;
}

.send-button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.send-icon {
  width: 16px;
  height: 16px;
}

/* Estado Vazio */
.empty-state {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 40px;
  text-align: center;
}

.empty-icon {
  font-size: 80px;
  margin-bottom: 20px;
  opacity: 0.5;
}

.empty-state h3 {
  font-family: 'Inter', sans-serif;
  font-size: 18px;
  font-weight: 500;
  color: #4a5568;
  margin: 0 0 8px 0;
}

.empty-state p {
  font-size: 13px;
  color: #718096;
  margin: 0;
}

/* Responsividade */
@media (max-width: 1200px) {
  .chat-container {
    margin-left: 240px;
  }
  
  .chat-list {
    width: 300px;
  }
}

@media (max-width: 1024px) {
  .chat-container {
    margin-left: 0;
  }
  
  .chat-list {
    width: 280px;
  }
  
  .message-wrapper {
    max-width: 85%;
  }
}

@media (max-width: 768px) {
  .chat-list {
    width: 280px;
  }
  
  .chat-list-header {
    padding: 20px;
  }
  
  .chat-item {
    padding: 12px 16px;
  }
  
  .chat-header {
    padding: 16px 20px;
  }
  
  .messages-container {
    padding: 16px 20px;
  }
  
  .message-input-container {
    padding: 16px 20px;
  }
}
</style>
