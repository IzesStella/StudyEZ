<template>
  <div class="sidebar">
    <img src="/images/StudyEZ.png" alt="StudyEZ Logo" class="logo small-logo" />
    <nav>
      <div class="nav-buttons">
        <button class="nav-button" @click="goToRoute('/dashboard')">
          <font-awesome-icon icon="home" style="color: #CE9E10;" />
        </button>
        <button class="nav-button" @click="goToRoute('/search')" style="color: #3885A7;">
          <font-awesome-icon icon="plus" />
        </button>
      </div>
      <button class="nav-button profile-button" @click="goToRoute('/profile')">
        <font-awesome-icon icon="user" class="profile-icon" />
      </button>
    </nav>
  </div>
</template>

<script>
import { router } from '@inertiajs/vue3';

export default {
  methods: {
    goToRoute(route) {
      router.visit(route);
    },
  },
};
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap');

.sidebar {
  width: 100px;
  background-color: #EBF4FF;
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
}

.small-logo {
  width: 50px;
  margin-bottom: 20px;
  margin-top: 30px;
}

nav {
  display: flex;
  flex-direction: column;
  height: 100%;
}

/* Ajusta o contêiner dos botões para ocupar o espaço disponível e centralizá-los */
.nav-buttons {
  flex: 1;
  display: flex;
  flex-direction: column;
  justify-content: center;
  /* Removemos height e margin-top para deixar o flex cuidar do posicionamento */
}

.nav-button {
  display: flex;
  justify-content: center;
  align-items: center;
  width: 50px;
  height: 50px;
  background-color: #ffffff;
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