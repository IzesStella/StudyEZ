<template>
  <div class="prelogin-wrapper">
    <div class="banner-section">
      <img src="/images/bannerStudyEZ.png" alt="Banner StudyEZ" class="banner-bg" />
      <div class="banner-overlay">
        <LogoStudyEZ class="logo" />

        <div class="text-content">
        
          <div class="btn-group">
            <button @click="goToRegister" class="btn orange">Cadastre-se</button>
            <button @click="goToLogin" class="btn white">Entrar</button>
          </div>
        </div>
      </div>
    </div>

    <div class="features">
      <div class="feature">
        <img src="/images/icon-duvidas.png" alt="Dúvidas" />
        <h3>TIRE DÚVIDAS 24h</h3>
        <p>Monitores voluntários sempre por perto.</p>
      </div>
      <div class="feature">
        <img src="/images/icon-comunidade.png" alt="Comunidade" />
        <h3>ENCONTRE SUA COMUNIDADE</h3>
        <p>Junte-se a grupos de estudos em diversas matérias.</p>
      </div>
      <div class="feature">
        <img src="/images/icon-chat.png" alt="Chat" />
        <h3>INICIE CHATS COM MONITORES</h3>
        <p>Poste suas perguntas ou inicie o chat.</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { router } from '@inertiajs/vue3'
import LogoStudyEZ from '@/Components/LogoStudyEZ.vue'

function goToRegister() {
  router.visit(route('register'))
}

function goToLogin() {
  router.visit(route('login'))
}
</script>

<style scoped>

@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap');

* {
  box-sizing: border-box;
}

.prelogin-wrapper {
  font-family: 'Montserrat', sans-serif;
  width: 100%;
  min-height: 100vh;
  overflow-x: hidden;
  overflow-y: auto;
}

.banner-section {
  position: relative;
  width: 100%;
}

.banner-bg {
  width: 100%;
  object-fit: cover;
}

.banner-overlay {
  position: absolute;
  top: 70%;
  left: 0;
  width: 100%;
  display: flex;
  flex-direction: column;
  align-items: flex-start;
}

.logo {
  margin-bottom: 20px;
}


.btn-group {
  margin-top: 24px;
  margin-left: 8vw;
  display: flex;
  gap: 15px;
  justify-content: flex-start;
}

.btn {
  font-size: 16px;
  font-weight: bold;
  padding: 12px 24px;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.btn.orange {
  background-color: #FF9500;
  color: white;
  border: none;
}

.btn.white {
  background-color: white;
  color: #1e1e1e;
  border: 2px solid #002F66;
}

.btn:hover {
  transform: translateY(-2px);
}

.features {
  display: flex;
  justify-content: center;
  align-items: center;
  background-color: #fff;
  flex-wrap: wrap;
  gap: 30px;
}

.feature {
  max-width: 280px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
  margin: 0 24px;
}

.feature img {
  height: 55px;
  margin-bottom: 15px;
}

.feature h3 {
  font-size: 18px;
  font-weight: bold;
  margin-bottom: 10px;
  color: #1e1e1e;
  text-transform: uppercase;
}

.feature p {
  font-size: 15px;
  color: #555;
}
</style>
