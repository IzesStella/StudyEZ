<template>
  <div class="max-w-5xl mx-auto p-6">
    <CreatePostModal v-if="showCreatePostModal" @close="showCreatePostModal = false" />
    <!-- Banner da Comunidade -->
    <div
      class="bg-blue-800 text-white rounded-lg p-6 flex flex-col md:flex-row justify-between items-start md:items-center mb-8"
    >
      <div>
        <!-- Agora dinâmico -->
        <h1 class="text-3xl font-bold">{{ community.name }}</h1>
        <p class="text-sm mt-2">Monitor: {{ community.creator.name }}</p>
        <p class="mt-2">{{ community.description }}</p>
      </div>
      <div class="mt-4 md:mt-0 space-x-3">
        <!-- Inscrever‑se (apenas aluno não inscrito) -->
        <button
          v-if="user && isStudent && !isSubscribed"
          @click="subscribe"
          class="bg-white text-blue-800 font-semibold px-5 py-2 rounded-lg hover:bg-blue-100 transition"
        >
          Inscrever-se
        </button>
        <!-- Sair da comunidade (apenas aluno inscrito) -->
        <button
          v-if="user && isStudent && isSubscribed"
          @click="unsubscribe"
          class="bg-red-600 text-white font-semibold px-5 py-2 rounded-lg hover:bg-red-700 transition"
        >
          Sair da comunidade
        </button>
        <!-- + Postar (todo aluno OU monitor dono) -->
        <button
          v-if="user && (isStudent || (user.role === 'monitor' && user.id === community.creator.id))"
          @click="showCreatePostModal = true"
          class="bg-blue-600 text-white font-semibold px-5 py-2 rounded-lg hover:bg-blue-700 transition"
        >
          + Postar
        </button>
      </div>
    </div>

    <!-- Exemplo de posts estáticos (você pode trocar por dados reais depois) -->
    <div>
      <div class="border-b border-gray-200 pb-6 mb-6">
        <div class="flex items-center mb-2">
          <div
            class="w-10 h-10 bg-orange-500 rounded-full flex items-center justify-center text-white font-bold"
          >
            P
          </div>
          <div class="ml-3">
            <p class="font-semibold">Pedro Henrique</p>
            <p class="text-sm text-gray-500">há 7 h</p>
          </div>
        </div>
        <h2 class="text-xl font-semibold mb-2">Geometria Analítica</h2>
        <p class="text-gray-700 mb-4">
          Explicação sobre Geometria Analítica com exemplos e detalhes.
        </p>
        <button
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
          <span>Comentários</span>
        </button>
      </div>

      <div class="border-b border-gray-200 pb-6 mb-6">
        <div class="flex items-center mb-2">
          <div
            class="w-10 h-10 bg-orange-500 rounded-full flex items-center justify-center text-white font-bold"
          >
            J
          </div>
          <div class="ml-3">
            <p class="font-semibold">João Inácio</p>
            <p class="text-sm text-gray-500">há 2 h</p>
          </div>
        </div>
        <h2 class="text-xl font-semibold mb-2">
          Como estudar Geometria Analítica?
        </h2>
        <p class="text-gray-700 mb-4">
          Dicas e métodos de estudo para melhorar seu desempenho em Geometria Analítica.
        </p>
        <button
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
          <span>Comentários</span>
        </button>
      </div>

      <div class="border-b border-gray-200 pb-6 mb-6">
        <div class="flex items-center mb-2">
          <div
            class="w-10 h-10 bg-orange-500 rounded-full flex items-center justify-center text-white font-bold"
          >
            I
          </div>
          <div class="ml-3">
            <p class="font-semibold">Isabelly Arruda</p>
            <p class="text-sm text-gray-500">há 7 h</p>
          </div>
        </div>
        <h2 class="text-xl font-semibold mb-2">Geometria Analítica :(</h2>
        <p class="text-gray-700 mb-4">
          Sentimento sobre a matéria e dificuldades encontradas.
        </p>
        <button
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
              d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4-30 8-9-8-1.446 0-2.813-.253-4.033-.707L3 20l1.293-4.657C3.253 14.813 3 13.446 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"
            />
          </svg>
          <span>Comentários</span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { defineProps, ref } from 'vue'
import { router } from '@inertiajs/vue3'
import CreatePostModal from '@/Components/CreatePostModal.vue'

const props = defineProps({
  community:    { type: Object, required: true },
  user:         { type: Object, required: false, default: null },
  isSubscribed: { type: Boolean, required: false, default: false },
})

const isStudent = props.user?.role === 'student'
const showCreatePostModal = ref(false)

function subscribe() {
  router.post(
    `/communities/${props.community.id}/subscribe`,
    {},
    { preserveScroll: true }
  )
}

function unsubscribe() {
  router.delete(
    `/communities/${props.community.id}/unsubscribe`,
    {},
    { preserveScroll: true }
  )
}
</script>
