<template>
  <div class="dashboard-layout">
    <component :is="sidebarComponent" @open-create-community="showCreateCommunity = true" />
    <div class="search-page">
      <div class="search-input-wrapper">
        <input
          v-model="form.search"
          @keyup.enter="submit"
          class="search-input"
          type="text"
          placeholder="Busque por uma nova comunidade..."
        />
        <font-awesome-icon :icon="['fas', 'search']" class="search-icon" @click="submit" />
      </div>

      <div class="search-results">
        <div v-if="!communities.length" class="text-gray-500">
          Nenhuma comunidade encontrada.
        </div>
        <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          <div
            v-for="comm in communities"
            :key="comm.id"
            class="bg-blue-800 hover:bg-blue-900 text-white rounded-lg p-6 flex flex-col justify-between transition"
          >
            <div>
              <h2 class="text-xl font-bold mb-1">{{ comm.name }}</h2>
              <p class="text-sm">Monitor: {{ comm.creator.name }}</p>
            </div>
            <!-- Pega o ID certo na comunidade correta -->
           <Link
            :href="route('community.page', comm.id)"
              class="self-end mt-4 bg-white text-blue-800 font-semibold px-4 py-2 rounded hover:bg-gray-100 transition"
            >
              Entrar
            </Link>
          </div>
        </div>
      </div>

      <PlannerBar />
      <CreateCommunity v-if="showCreateCommunity" @close="showCreateCommunity = false" />
    </div>
  </div>
</template>

<script setup>
import PlannerBar from '@/Components/PlannerBar.vue'
import SideBar from '@/Components/SideBar.vue'
import SideBarMonitor from '@/Components/SideBarMonitor.vue'
import CreateCommunity from '@/Components/CreateCommunity.vue'
import { faSearch } from '@fortawesome/free-solid-svg-icons'
import { library } from '@fortawesome/fontawesome-svg-core'
import { usePage, useForm, Link } from '@inertiajs/vue3'
import { ref } from 'vue'

library.add(faSearch)

const user = usePage().props.auth.user
const sidebarComponent = user.role === 'monitor' ? SideBarMonitor : SideBar
const showCreateCommunity = ref(false)

// Props Inertia
const props = defineProps({
  communities: { type: Array, default: () => [] },
  search: { type: String, default: '' }
})

// Formulário de busca Inertia
const form = useForm({ search: props.search })
// Comunidades retornadas
const communities = ref(props.communities)

function submit() {
  form.get(
    route('search'),
    {
      preserveState: true,
      onSuccess: ({ props }) => {
        communities.value = props.communities
      }
    }
  )
}
</script>

<style>
.search-page {
  padding: 40px 24px;
  max-width: 1200px;
  margin: 0 auto;
}
.search-input-wrapper {
  position: relative;
  width: 100%;
}
.search-input {
  width: 100%;
  padding: 14px 48px 14px 24px;
  border-radius: 12px;
  border: 1.5px solid #b0d5ff;
  font-size: 1.1rem;
  margin-bottom: 32px;
  outline: none;
  transition: border 0.2s;
  box-sizing: border-box;
}
.search-input:focus {
  border-color: #3885a7;
}
.search-icon {
  position: absolute;
  right: 18px;
  top: 50%;
  transform: translateY(-50%);
  color: #3885a7;
  font-size: 1.3em;
  cursor: pointer;
}
</style>
