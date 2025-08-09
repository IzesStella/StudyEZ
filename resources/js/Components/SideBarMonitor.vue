<template>
  <div class="sidebar">
    <nav>
      <!-- Foto de perfil na sidebar -->
     
      <div class="nav-buttons">
        <!-- Casinha -->
        <button class="nav-button" @click="goToRoute('/dashboard-monitor')">
          <font-awesome-icon :icon="['fas', 'home']" style="color: #e3971c;" />
        </button>
        <!--lupa -->
        <button class="nav-button" @click="goToRoute('/search')" style="color: #e3971c;">
          <font-awesome-icon :icon="['fas', 'search']" />
        </button>
        <!-- criar comunidade -->
        <button class="nav-button" @click="$emit('open-create-community')" style="color: #e3971c;">
          <font-awesome-icon :icon="['fas', 'users']" />
          <font-awesome-icon :icon="['fas', 'plus']" style="color: #e3971c;" />
         </button>
        <button class="nav-button" style="color: #e3971c;" @click="goToRoute('/chat')">
          <font-awesome-icon :icon="['fas', 'comment-dots']" />
        </button>
      </div>
      <!-- Perfil -->
      <button class="nav-button profile-button" @click="goToRoute('/profile')">
        <template v-if="user.profile_photo">
          <img
            :src="`/storage/${user.profile_photo}`"
            alt="Avatar"
            class="profile-avatar-btn"
          />
        </template>
        <template v-else>
          <font-awesome-icon :icon="['fas', 'user']" class="profile-icon" style="color: #e3971c;" />
        </template>
      </button>

       <!-- Sair -->
      <button class="nav-button logout-button" @click="logout">
        <font-awesome-icon :icon="['fas','sign-out-alt']" style="color: #e3971c;"/>
      </button>
    </nav>
  </div>
</template>

<script>
import { router, usePage } from '@inertiajs/vue3'
import { toast } from 'vue3-toastify'
import 'vue3-toastify/dist/index.css'

export default {
  computed: {
    user() {
      return usePage().props.auth.user;
    }
  },
  methods: {
    goToRoute(route) {
      router.visit(route);
    },
    logout() {
      router.post(route('logout'), {
        onSuccess: () => {
          toast.info('Você saiu com sucesso.')
          router.replace({ name: 'prelogin' })
        },
        onError: () => toast.error('Não foi possível sair.'),
      })
    }
  }
};
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap');

.sidebar {
 position: fixed;
  top: 25%; 
  left: 0;
  width: 50px;
  height: 50%;          
  margin: 0;              
  background-color: #B0D5FF;
  border-top-right-radius: 35px;
  border-bottom-right-radius: 35px;
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 20px 0;
  box-shadow: 2px 0 8px rgba(0, 0, 0, 0.1); 
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
  width: 45px;
  height: 45px;
  background-color: #B0D5FF;
  border: none;
  border-radius: 50%;
  font-size: 1.3rem;
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

.sidebar-avatar {
  border-radius: 50%;
  object-fit: cover;
  margin: 0 auto 12px auto;
  border: 2px solid #ce7b07;
  background: #fff;
}

.profile-avatar-btn {
  width: 35px;
  height: 35px;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid #e3971c;
  background: #fff;
}

@media (max-width: 1024px) {
  .sidebar {
    width: 50px;
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
    width: 50px;
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