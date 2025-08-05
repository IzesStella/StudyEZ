<template>
  <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
    <div
      class="absolute inset-0 bg-gray-900 opacity-75"
      @click="$emit('close')"
    ></div>

    <div class="relative bg-white rounded-lg shadow-xl max-w-sm w-full">
      <div class="bg-[#b0d5ff] text-white p-4 rounded-t-lg flex justify-between items-center">
        <h2 class="text-xl font-bold">Atenção!</h2>
        <button @click="$emit('close')" class="text-white hover:text-yellow-400">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <div class="p-6">
        <div class="flex items-start">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-yellow-500 mr-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
          </svg>
          <div>
            <p class="text-lg font-semibold text-gray-800">
              Você precisa ser um membro desta comunidade para postar.
            </p>
            <p class="mt-2 text-gray-600">
              Clique em "Inscrever-se" para se juntar à comunidade e começar a interagir!
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { defineEmits } from 'vue';

const emits = defineEmits(['close']);
</script>