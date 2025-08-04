<template>
  <LogoStudyEZ />
  <div class="dashboard-layout">
    <component
      :is="sidebarComponent"
      @open-create-community="showCreateCommunity = true"
    />
    <div class="search-page">
      <!-- Busca -->
      <div class="search-input-wrapper">
        <input
          v-model="form.search"
          @keyup.enter="submit"
          class="search-input"
          type="text"
          placeholder="Busque por uma nova comunidade..."
        />
        <font-awesome-icon
          :icon="['fas','search']"
          class="search-icon"
          @click="submit"
        />
      </div>

      <!-- Resultados -->
      <div class="search-results">
        <div v-if="!communities.length" class="text-gray-500">
          Nenhuma comunidade encontrada.
        </div>
        <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          <div
            v-for="(comm, index) in communities"
            :key="comm.id"
            :class="['community-card', randomColors[index]]"
          >
            <div>
              <h2 class="text-xl font-bold mb-1">{{ comm.name }}</h2>
              <p class="text-sm">Monitor: {{ comm.creator.name }}</p>
            </div>
            <Link
              :href="route('community.page', comm.id)"
              class="enter-button"
            >
              Entrar
            </Link>
          </div>
        </div>
      </div>

      <!-- PlannerBar e Modal -->
      <PlannerBar />
      <CreateCommunity
        v-if="showCreateCommunity"
        @close="showCreateCommunity = false"
        @created="handleCommunityCreated"
      />
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { usePage, useForm, Link } from '@inertiajs/vue3'
import { library } from '@fortawesome/fontawesome-svg-core'
import { faSearch } from '@fortawesome/free-solid-svg-icons'

import PlannerBar from '@/Components/PlannerBar.vue'
import SideBar from '@/Components/SideBar.vue'
import SideBarMonitor from '@/Components/SideBarMonitor.vue'
import CreateCommunity from '@/Components/CreateCommunity.vue'
import LogoStudyEZ from '@/Components/LogoStudyEZ.vue'

library.add(faSearch)

// Sidebar conforme o papel do usuário
const user = usePage().props.auth.user
const sidebarComponent = user.role === 'monitor' ? SideBarMonitor : SideBar

// Controle do modal
const showCreateCommunity = ref(false)

// Props iniciais (comunidades vindas do servidor)
const props = defineProps({
  communities: { type: Array, default: () => [] },
  search:      { type: String, default: '' }
})

const form = useForm({ search: props.search })
const communities = ref(props.communities)

// As três classes de cor disponíveis
const baseColors = ['card-color-1','card-color-2','card-color-3']

// Mapeamento { [communityId]: className }, persistido no localStorage
const colorMap = ref({})

/** Carrega do localStorage ou inicializa map vazio */
function loadColorMap() {
  try {
    const stored = localStorage.getItem('communityColorMap')
    colorMap.value = stored ? JSON.parse(stored) : {}
  } catch {
    colorMap.value = {}
  }
}

/** Salva o map atualizado no localStorage */
function saveColorMap() {
  localStorage.setItem('communityColorMap', JSON.stringify(colorMap.value))
}

/** Garante que cada comunidade tenha uma cor no map */
function ensureColors() {
  let changed = false
  communities.value.forEach(comm => {
    if (!(comm.id in colorMap.value)) {
      // atribui cor aleatória
      const random = baseColors[Math.floor(Math.random() * baseColors.length)]
      colorMap.value[comm.id] = random
      changed = true
    }
  })
  if (changed) saveColorMap()
}

// Array reativo de cores na mesma ordem de `communities`
const randomColors = computed(() => {
  return communities.value.map(comm => {
    // se ainda não carregou, garantimos a cor
    if (!colorMap.value[comm.id]) ensureColors()
    return colorMap.value[comm.id]
  })
})

/** Busca no backend e atualiza comunidades */
function fetchCommunities() {
  form.get(route('search'), {
    preserveState: true,
    onSuccess: ({ props }) => {
      communities.value = props.communities
      ensureColors()
    }
  })
}

/** Chamado ao digitar enter ou clicar na lupa */
function submit() {
  fetchCommunities()
}

/** Chamado quando CreateCommunity emite "created" */
function handleCommunityCreated() {
  showCreateCommunity.value = false
  fetchCommunities()
}

// Na montagem: carrega map e garante cores iniciais
onMounted(() => {
  loadColorMap()
  ensureColors()
})
</script>

<style>
.search-page {
  padding: 40px 24px;
  max-width: 1200px;
  margin: 0 auto;
}

.search-input-wrapper {
  display: flex;
  align-items: center;
  width: 100%;
  margin-bottom: 32px;
}

.search-input {
  flex: 1;
  padding: 14px 48px 14px 24px;
  border-radius: 12px;
  border: 1.5px solid #b0d5ff;
  font-size: 1.1rem;
  outline: none;
  transition: border 0.2s;
  box-sizing: border-box;
}

.search-input:focus {
  border-color: #3885a7;
}

.search-icon {
  margin-left: -48px;
  color: #3885a7;
  font-size: 1.3em;
  cursor: pointer;
}

.community-card {
  border-radius: 12px;
  padding: 24px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  transition: 0.2s;
  color: white;
}

/* Variações de cor */
.card-color-1 { background-color: #0a1d63; }
.card-color-2 { background-color: #2f46d8; }
.card-color-3 { background-color: #6a8dff; }

.enter-button {
  align-self: flex-end;
  margin-top: 16px;
  background-color: #add8ff;
  color: #003b73;
  font-weight: 600;
  padding: 8px 16px;
  border-radius: 8px;
  transition: 0.2s;
  text-align: center;
}
.enter-button:hover { background-color: #d2eaff; }
</style>
