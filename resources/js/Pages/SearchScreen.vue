<template>
  <MainLayout>
    <div class="container">
      <!-- Top Bar com busca -->
      <div class="top-bar">
        <input
          type="text"
          v-model="searchQuery"
          placeholder="Pesquisar novas comunidades..."
        />
      </div>

      <!-- Seção dos Cards -->
      <section class="cards-section">
        <h2>Conheça novas disciplinas:</h2>
        <div class="cards-grid">
          <div
            v-for="disciplina in filteredDisciplinas"
            :key="disciplina.nome"
            class="card"
            :style="{ backgroundColor: disciplina.cor }"
          >
            <!-- Cabeçalho com imagem e título -->
            <div class="card-header">
              <img
                :src="disciplina.imagem"
                alt="Ícone da disciplina"
              />
              <h3>{{ disciplina.nome }}</h3>
            </div>
            <p>{{ disciplina.descricao }}</p>
            <div class="card-footer">
              <button @click="goToCommunityByDiscipline(disciplina)">
                ENTRAR
              </button>
            </div>
          </div>
        </div>
      </section>
    </div>
  </MainLayout>
</template>

<script>
import { router } from '@inertiajs/vue3';
import SideBar from '../Components/SideBar.vue';
import MainLayout from '@/Layouts/MainLayout.vue';

export default {
  components: {
    SideBar,
    MainLayout,
  },
  data() {
    return {
      searchQuery: '',
      disciplinas: [
        {
          id: 1,
          imagem: '/images/logicadeprogramacao.png',
          nome: 'Lógica de Programação',
          descricao: 'Aprenda a resolver problemas com código e algoritmos.',
          cor: '#d9f8c4',
        },
        {
          id: 2,
          imagem: '/images/engenharia.png',
          nome: 'Engenharia de Software',
          descricao: 'Estude metodologias para criar software de qualidade.',
          cor: '#c4f0f8',
        },
        {
          id: 3,
          imagem: '/images/bancodedados.png',
          nome: 'Banco de Dados',
          descricao: 'Gerencie e otimize bancos de dados com SQL.',
          cor: '#e3d9f8',
        },
        {
          id: 4,
          imagem: '/images/web.png',
          nome: 'Desenvolvimento Web',
          descricao:
            'Crie sites e apps com HTML, CSS, JavaScript e frameworks.',
          cor: '#f8d9d9',
        },
        {
          id: 5,
          imagem: '/images/design.png',
          nome: 'Design',
          descricao: 'Crie interfaces funcionais e responsivas.',
          cor: '#fff2d4',
        },
        {
          id: 6,
          imagem: '/images/redes.png',
          nome: 'Redes de Computadores',
          descricao:
            'Entenda como funcionam as redes e a segurança da informação.',
          cor: '#f8d4e4',
        },
      ],
    };
  },
  computed: {
    filteredDisciplinas() {
      if (!this.searchQuery) {
        return this.disciplinas;
      }
      const query = this.searchQuery.toLowerCase();
      return this.disciplinas.filter((disciplina) => {
        return (
          disciplina.nome.toLowerCase().includes(query) ||
          disciplina.descricao.toLowerCase().includes(query)
        );
      });
    },
  },
  methods: {
    goToCommunityByDiscipline(disciplina) {
      router.get(`/community/${disciplina.id}`);
    },
  },
};
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap');

/* Container principal (equivalente a flex h-screen overflow-hidden) */
.container {
  display: flex;
  flex-direction: column;
  height: 100vh;
  overflow: hidden;
  font-family: 'Montserrat', sans-serif;
}

/* Top Bar: centralizado, fundo branco, com padding */
.top-bar {
  display: flex;
  justify-content: center;
  align-items: center;
  width: 100%;
  padding: 8px 0;
  background-color: #ffffff;
  z-index: 10;
  box-sizing: border-box;
}

/* Estilo do input de busca */
.top-bar input[type="text"] {
  width: 100%;
  max-width: 1090px;
  padding: 8px;
  font-size: 16px;
  border: 1px solid #d1d5db;
  border-radius: 9999px;
  outline: none;
}

/* Seção de cards */
.cards-section {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 16px;
  box-sizing: border-box;
}

/* Título da seção */
.cards-section h2 {
  font-size: 20px;
  font-weight: bold;
  margin-top: 32px;
  margin-bottom: 16px;
}

/* Grid de cards: 1 coluna por padrão */
.cards-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 20px;
  width: 100%;
  padding: 0 20px;
  box-sizing: border-box;
}

/* 2 colunas para telas maiores que 830px */
@media (min-width: 830px) {
  .cards-grid {
    grid-template-columns: 1fr 1fr;
  }
}

/* 3 colunas para telas maiores que 1200px */
@media (min-width: 1200px) {
  .cards-grid {
    grid-template-columns: 1fr 1fr 1fr;
  }
}

/* Card individual (equivalente ao div com classes do Tailwind) */
.card {
  background-color: #f9fafb; /* Cor de fundo padrão, mas sobrescrita via inline com disciplina.cor */
  border-radius: 12px;
  padding: 20px;
  text-align: left;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  width: 100%;
  max-width: 350px;
  margin: 0 auto;
  min-height: 210px;
  display: flex;
  flex-direction: column;
  position: relative;
}

/* Efeito hover: eleva o card e aumenta a sombra */
.card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
}

/* Cabeçalho do card: exibe a imagem e o título */
.card-header {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 8px;
}

/* Imagem circular do card */
.card-header img {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  object-fit: cover;
}

/* Título do card */
.card-header h3 {
  font-size: 18px;
  font-weight: bold;
  margin: 0;
  color: #000;
}

/* Parágrafo de descrição do card */
.card p {
  font-size: 16px;
  color: #000;
  line-height: 1.5;
  margin: 12px 0;
  flex-grow: 1;
}

/* Botão do card */
.card-footer {
  text-align: right;
  
}
.card-footer button {
  background-color: #ffffff;
  color: #000;
  font-weight: bold;
  border: none;
  border-radius: 8px;
  padding: 10px 20px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}
.card-footer button:hover {
  background-color: #d1d5db;
}
</style>