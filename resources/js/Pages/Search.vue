<script setup>
import PlannerBar from '@/Components/PlannerBar.vue'
import SideBar from '@/Components/SideBar.vue'
import SideBarMonitor from '@/Components/SideBarMonitor.vue'
import CreateCommunity from '@/Components/CreateCommunity.vue'
import { faSearch } from '@fortawesome/free-solid-svg-icons'
import { library } from '@fortawesome/fontawesome-svg-core'
import { usePage } from '@inertiajs/vue3'
import { ref } from 'vue'
library.add(faSearch)

const user = usePage().props.auth.user
const sidebarComponent = user.role === 'monitor' ? SideBarMonitor : SideBar
const showCreateCommunity = ref(false)
</script>

<template>
  <div class="dashboard-layout">
    <component :is="sidebarComponent" @open-create-community="showCreateCommunity = true" />
    <div class="search-page">
      <div class="search-input-wrapper">
        <input
          class="search-input"
          type="text"
          placeholder="Busque por uma nova comunidade..."
        />
        <font-awesome-icon :icon="['fas', 'search']" class="search-icon" />
      </div>
      <div class="search-results">
        <!-- Resultados vão aqui -->
      </div>
      <PlannerBar />
      <CreateCommunity v-if="showCreateCommunity" @close="showCreateCommunity = false" />
    </div>
  </div>
</template>

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
  padding: 14px 48px 14px 24px; /* espaço extra à direita para a lupa */
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
  top: 35%;
  transform: translateY(-50%);
  color: #3885a7;
  font-size: 1.3em;
  pointer-events: none;
}
</style>