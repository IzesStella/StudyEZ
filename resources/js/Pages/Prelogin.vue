<template>
  <MainLayout>
    <div class="prelogin-container">
      <SideBar/> <!-- Sidebar -->

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
            <button @click="goToLogin" class="btn primary">ENTRAR</button>
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
              comunidade possui um título com a matéria relacionada, uma <br />
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
  </MainLayout>
</template>

<script>
import { router } from '@inertiajs/vue3';
import SideBar from '../Components/SideBar.vue';

export default {
  methods: {
    goToLogin() {
      router.get('/login');
    },
    goToRegister() {
      router.get('/register');
    },
    goToAbout() {
      router.get('/about');
    },
  },

  components: {
    SideBar,
  },

  name: 'Prelogin',
};
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap');

.prelogin-container {
  display: flex;
  flex-direction: column;
  height: 100vh;
  overflow: auto;
}

.small-logo {
  width: 80px;
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

.banner {
  top: 80px;
  z-index: 1000;
}

.banner-image {
  max-width: 90%;
  height: 50%;
  display: block;
  margin: 0 auto;
  border-radius: 10px;
  object-fit: cover;
}

.perfil-img {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  object-fit: cover;
}

.faq {
  padding: 40px 20px;
  width: 80%;
  margin: 0 auto; /* Centraliza o bloco, mas mantém o texto alinhado à esquerda */
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
  margin-bottom: 10px; /* Espaço entre o título e o texto */
  text-align: left;
}
.faq-item p {
  font-size: 18px;
  color: #333;
  line-height: 1.6;
  margin-left: 0;
  text-align: left;
}


@media (max-width: 1024px) {
  .sidebar {
    width: 70px;
    padding: 10px 0;
  }

  .small-logo {
    width: 60px;
  }

  .main-content {
    margin-left: 90px;
  }

  .top-bar {
    flex-direction: column;
    gap: 10px;
    padding: 10px;
  }

  .auth-options {
    flex-direction: column;
    gap: 5px;
  }
}

@media (max-width: 768px) {
  .main-content {
    margin-left: 0;
    padding: 10px;
  }

  .banner-image {
    max-width: 100%;
    height: auto;
  }

  .join-button {
    width: 90%;
    max-width: 200px;
    padding: 10px;
    border-radius: 20px;
    background-color: white;
    border: none;
    position: relative;
    margin-top: auto;
  }

  .faq
   {
    width: 100%;
    padding: 20px;
  }

}
</style>