<template>
  <div class="sidebar">
    <img src="/images/StudyEZ.png" alt="StudyEZ Logo" class="logo small-logo" />
    <nav>
      <div class="nav-buttons">
        <!-- Casinha -->
        <button class="nav-button" @click="goToRoute('/dashboard-monitor')">
          <font-awesome-icon :icon="['fas', 'home']" style="color: #FF9500;" />
        </button>
        <!--lupa -->
        <button class="nav-button" @click="goToRoute('/search')" style="color: #FF9500;">
          <font-awesome-icon :icon="['fas', 'search']" />
        </button>
        <!-- Botão extra para monitor -->
        <button class="nav-button" @click="$emit('open-create-community')" style="color: #FF9500;">
          <font-awesome-icon :icon="['fas', 'users']" />
          <font-awesome-icon :icon="['fas', 'plus']" style="color: #FF9500;" />
        </button>
      </div>
      <!-- Perfil -->
      <button class="nav-button profile-button" @click="goToRoute('/profile')">
        <font-awesome-icon :icon="['fas', 'user']" class="profile-icon" style="color: #FF9500;" />
      </button>
    </nav>
  </div>
</template>

<script>
import { router } from '@inertiajs/vue3' 

export default {
  methods: {
    goToRoute(route) {
      router.visit(route);
    }
  },
};
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap');

.sidebar {
  width: 100px;
  background-color: #B0D5FF;
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 20px 0;
  top: 0;
  left: 0;
  margin-bottom: 15px;
  position: fixed;
  height: 100%;
  margin: 0%;
  border-top-right-radius: 50px;     
  border-bottom-right-radius: 50px; 
}

.small-logo {
  width: 85px;
  margin-bottom: 20px;
  margin-top: 30px;
}

nav {
  display: flex;
  flex-direction: column;
  height: 100%;
}

.nav-buttons {
  flex: 1;
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.nav-button {
  display: flex;
  justify-content: center;
  align-items: center;
  width: 50px;
  height: 50px;
  background-color: #B0D5FF;
  border: none;
  border-radius: 50%;
  font-size: 1.5rem;
  cursor: pointer;
  margin-bottom: 10px;
  transition: all 0.2s ease-in-out;
}

.nav-button:hover {
  color: #000;
}

/* O botão de perfil já está posicionado no final */
.profile-button {
  margin-top: auto;
}

.profile-icon {
  color: #CE9E10;
}

@media (max-width: 1024px) {
  .sidebar {
    width: 80px;
    padding: 10px 0;
  }

  .small-logo {
    width: 60px;
  }

  .nav-button {
    width: 40px;
    height: 40px;
    font-size: 1.2rem;
  }
}

@media (max-width: 768px) {
  .sidebar {
    width: 60px;
    padding: 5px 0;
    z-index: 3;
  }

  .small-logo {
    width: 50px;
  }

  .nav-button {
    width: 35px;
    height: 35px;
    font-size: 1rem;
  }
}

@media (max-width: 480px) {
  .sidebar {
    display: none;
  }
}
</style>