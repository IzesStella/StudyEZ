<template>
  <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
      <h2 class="text-2xl font-bold mb-4 text-gray-800">Adicionar Comentário</h2>

      <form @submit.prevent="submitComment">
        <div class="mb-4">
          <label for="commentContent" class="block text-gray-700 text-sm font-bold mb-2">Seu Comentário:</label>
          <textarea
            id="commentContent"
            v-model="newCommentContent"
            class="shadow appearance-none border rounded-lg w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent h-32 resize-none"
            placeholder="Escreva seu comentário aqui..."
            required
          ></textarea>
        </div>

        <div class="flex justify-end space-x-3">
          <button
            type="button"
            @click="$emit('close')"
            class="bg-gray-300 text-gray-800 font-semibold px-5 py-2 rounded-lg hover:bg-gray-400 transition"
          >
            Fechar
          </button>
          <button
            type="submit"
            :disabled="isLoading"
            class="bg-blue-600 text-white font-semibold px-5 py-2 rounded-lg hover:bg-blue-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {{ isLoading ? 'Enviando...' : 'Comentar' }}
          </button>
        </div>

        <p v-if="errorMessage" class="text-red-500 text-sm mt-3">{{ errorMessage }}</p>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';

const props = defineProps({
  post: {
    type: Object,
    required: true,
  },
});

const emit = defineEmits(['close', 'comment-added']);

const newCommentContent = ref('');
const isLoading = ref(false);
const errorMessage = ref('');

const submitComment = async () => {
  isLoading.value = true;
  errorMessage.value = '';

  try {
    // The route defined in web.php and the controller already expect the postId in the URL
    const response = await axios.post(`/posts/${props.post.id}/comments`, {
      content: newCommentContent.value,
    });

    // Emits the event to the parent component (CommunityPage) with the new comment
    emit('comment-added', response.data);
    
    // Clears the form field and closes the modal
    newCommentContent.value = '';
    emit('close');

  } catch (error) {
    console.error('Error posting comment:', error);
    if (error.response && error.response.data && error.response.data.message) {
      errorMessage.value = error.response.data.message;
    } else {
      errorMessage.value = 'An error occurred while sending the comment. Please try again.';
    }
  } finally {
    isLoading.value = false;
  }
};
</script>
