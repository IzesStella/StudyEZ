<template>
  <LogoStudyEZ />
  <div class="prelogin-container">
    <SideBar />

      <div class="main-content">
        <!-- Top Bar -->
        <div class="top-bar">
          <div class="auth-options">
            <p>Não tem uma conta ainda?</p>
            <button @click="goToRegister" class="btn secondary">
              CADASTRE-SE
            </button>
          </div>
          <div class="auth-options">
            <p>Já é cadastrado?</p>
            <button @click="goToLogin" class="btn primary">
              ENTRAR
            </button>
          </div>
        </div>

        <!-- Banner -->
        <div class="banner">
          <img
            src="/images/Banner.png"
            alt="Banner StudyEZ"
            class="banner-image"
          />
        </div>

        <!-- FAQ -->
        <section class="faq">
          <h2>FAQ - COMO FUNCIONA NOSSO SITE?</h2>
          <div class="faq-item">
            <h3>O QUE É O STUDYEZ?</h3>
            <p>
              O StudyEZ é uma plataforma de monitorias online onde alunos podem
              receber ajuda em diversas matérias, tirar dúvidas e organizar seus
              estudos de forma eficiente.
            </p>
          </div>

          <div class="faq-item">
            <h3>COMO POSSO ENCONTRAR UMA MONITORIA?</h3>
            <p>
              Você pode navegar pelas comunidades e se unir a elas. Cada
              comunidade possui um título com a matéria relacionada, uma
              breve descrição e um botão para saber mais e dentro dela tem o
              espaço de perguntas e respostas.
            </p>
          </div>

          <div class="faq-item">
            <h3>O QUE É O PLANNER DO SITE?</h3>
            <p>
              O planner é uma ferramenta integrada ao aplicativo que permite
              organizar seus estudos. Você pode anotar tarefas e adicionar
              lembretes diários.
            </p>
          </div>
        </section>
      </div>
    </div>
</template>

<script setup>
import { router } from '@inertiajs/vue3'
import SideBar from '../Components/SideBar.vue'
import LogoStudyEZ from '@/Components/LogoStudyEZ.vue'

// Navegação pura — middleware guest resolve quem está logado
function goToRegister() {
  router.visit(route('register'))
}

function goToLogin() {
  router.visit(route('login'))
}
</script>


<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap');

.prelogin-container {
  display: flex;
  flex-direction: column;
  height: 100vh;
  overflow: auto;
}

.main-content {
  flex: 1;
  padding: 20px;
  text-align: center;
  overflow-y: auto;
  height: 100vh;
  box-sizing: border-box;
}

.top-bar {
  width: 100%;
  background-color: white;
  z-index: 900;
  display: flex;
  justify-content: center;
  padding: 15px 0;
  gap: 25px;
  font-size: 120%;
}

.auth-options {
  display: flex;
  align-items: center;
  gap: 15px;
  font-weight: bold;
  font-size: 120%;
  margin: 0 auto;
}

.auth-options p {
  font-size: 90%;
  margin: 0;
}

.btn {
  font-family: 'Montserrat', sans-serif;
  border: none;
  padding: 10px 20px;
  font-size: 15px;
  border-radius: 5px;
  cursor: pointer;
  background-color: #B0D5FF;
  color: #383838;
  box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);
}

.btn:hover {
  transform: translateY(-2px);
}

.banner-image {
  max-width: 90%;
  height: 50%;
  display: block;
  margin: 20px auto;
  border-radius: 10px;
  object-fit: cover;
}

.faq {
  padding: 40px 20px;
  width: 80%;
  margin: 0 auto;
  font-family: 'Montserrat', sans-serif;
}

.faq h2 {
  font-size: 28px;
  margin-bottom: 40px;
  color: #000;
  font-weight: bold;
  width: 50%;
  text-align: left;
}

.faq-item {
  margin-bottom: 30px;
}

.faq-item h3 {
  font-size: 22px;
  font-weight: bold;
  color: #000;
  margin-bottom: 10px;
  text-align: left;
}

.faq-item p {
  font-size: 18px;
  color: #333;
  line-height: 1.6;
  text-align: left;
}
</style>
