<template>
  <div class="dashboard-container">
    <!-- Sidebar -->
    <SideBar />

    <!-- Main Content -->
    <div class="main-content">
      <h1>Olá! O que você deseja aprender hoje?</h1>
      <div class="aside">
        <div v-if="enrichedCourses.length > 0" class="courses-list">
          <div
            class="course-card"
            v-for="course in enrichedCourses"
            :key="course.id"
            :style="{ backgroundColor: course.color }"
          >
            <div class="card-header">
              <img :src="course.image" alt="Ícone da disciplina" />
              <h3>{{ course.name }}</h3>
            </div>
            <p>{{ course.description }}</p>
            <div class="card-footer">
              <button
                class="btn community-button"
                @click="goToCommunity(course.id)"
              >
                ENTRAR
              </button>
            </div>
          </div>
        </div>

        <div v-else class="center-card">
          <p>Parece que você ainda não está inscrito em nenhuma disciplina.</p>
          <p>Explore as disciplinas disponíveis!</p>
          <button class="btn explore-button" @click="goToSearch">
            Clique aqui!
          </button>
        </div>

        <div class="planner-section">
          <div class="planner-right">
            <div class="calendar">
              <Calendar />
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { router } from '@inertiajs/vue3';
import Calendar from '@/Components/Calendar.vue';
import SideBar from '../Components/SideBar.vue';

const DISCIPLINAS = [
  {
    id: 1,
    nome: 'Lógica de Programação',
    cor: '#d9f8c4',
    imagem: '/images/logicadeprogramacao.png',
  },
  {
    id: 2,
    nome: 'Engenharia de Software',
    cor: '#c4f0f8',
    imagem: '/images/engenharia.png',
  },
  {
    id: 3,
    nome: 'Banco de Dados',
    cor: '#e3d9f8',
    imagem: '/images/bancodedados.png',
  },
  {
    id: 4,
    nome: 'Desenvolvimento Web',
    cor: '#f8d9d9',
    imagem: '/images/web.png',
  },
  {
    id: 5,
    nome: 'Design',
    cor: '#fff2d4',
    imagem: '/images/design.png',
  },
  {
    id: 6,
    nome: 'Redes de Computadores',
    cor: '#f8d4e4',
    imagem: '/images/redes.png',
  },
];

export default {
  props: {
    courses: {
      type: Array,
      default: () => [],
    },
  },
  computed: {
    enrichedCourses() {
      return this.courses.map((course) => {
        const disciplina = DISCIPLINAS.find(
          (d) =>
            d.id === course.id ||
            d.nome.toLowerCase() === course.name.toLowerCase()
        );
        return {
          ...course,
          color: disciplina?.cor || '#e3d9f8',
          image: disciplina?.imagem || '/images/default-course.png',
        };
      });
    },
  },
  methods: {
    goToProfile() {
      router.get('/profile');
    },
    goToSearch() {
      this.$inertia.get('/search');
    },
    goToCommunity(courseId) {
      this.$inertia.get(`/community/${courseId}`);
    },
  },
  components: {
    Calendar,
    SideBar,
  },
};
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap');

.dashboard-container {
  display: flex;
  height: 100vh;
  overflow: hidden;
}

.main-content {
  flex: 1;
  margin-left: 130px;
  padding: 20px;
  text-align: left;
  overflow-y: auto;
  height: 100vh;
  box-sizing: border-box;
  display: flex;
  flex-direction: column;
  gap: 20px;
}

h1 {
  font-family: 'Montserrat', sans-serif;
  font-size: 24px;
  font-weight: bold;
  margin-bottom: 0;
  margin-left: 40px;
}

.aside {
  display: flex;
  flex-direction: row;
  align-items: flex-start;
  width: 100%;
  justify-content: space-between;
}

/* Center Card */
.center-card {
  background-color: #bae8ff;
  border-radius: 12px;
  padding: 30px;
  text-align: justify;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
  width: 400px;
  height: fit-content;
  margin-left: 40px;
  display: flex;
  flex-direction: column;
  align-items: flex-end;
}

.center-card p {
  font-size: 16px;
  margin: 10px 0;
  width: 100%;
}

/* Cards dos cursos inscritos */
.courses-list {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 20px;
  margin-left: 40px;
  width: 100%;
  max-width: 1200px;
}

.course-card {
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
  transition:
    transform 0.3s ease,
    box-shadow 0.3s ease;
  min-height: 210px;
  display: flex;
  flex-direction: column;
  max-width: 400px;
}

.course-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
}

.card-header {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 8px;
  font-weight: bold;
}

.card-header img {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  object-fit: cover;
}

.card-header h3 {
  font-size: 20px;
  font-weight: bold;
  margin: 0;
  color: #000;
  font-family:
    system-ui,
    -apple-system,
    BlinkMacSystemFont,
    'Segoe UI',
    Roboto,
    Oxygen,
    Ubuntu,
    Cantarell,
    'Open Sans',
    'Helvetica Neue',
    sans-serif;
}

.course-card p {
  font-size: 16px;
  color: #000;
  line-height: 1.5;
  font-family: 'Montserrat', sans-serif;
  margin: 12px 0;
  flex-grow: 1;
  margin-top: 32px;
  margin-bottom: 16px;
}

.card-footer {
  text-align: right;
  margin-top: auto;
}

.community-button {
  background-color: #ffffff;
  color: #000;
  font-weight: bold;
  border: none;
  border-radius: 8px;
  padding: 10px 20px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.community-button:hover {
  background-color: #d1d5db;
}

.explore-button {
  background-color: #ffffff;
  color: #0a60e0;
  border: none;
  padding: 10px 20px;
  border-radius: 8px;
  cursor: pointer;
}

.explore-button:hover {
  background-color: #0056b3;
}

/* Planner Section - Estilo original */
.planner-section {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  gap: 20px;
  width: 400px;
  height: 200px;
  z-index: 10;
  margin-top: -3%;
}

.planner-right {
  width: 100%;
  min-width: 300px;
}

.calendar {
  background-color: #fdf5cc;
  padding: 5px;
  border-radius: 12px;
}

/* Responsividade */
@media (max-width: 1200px) {
  .aside {
    flex-direction: column;
  }

  .planner-section {
    width: 100%;
    margin-top: 20px;
  }
}

@media (max-width: 900px) {
  .courses-list {
    grid-template-columns: 1fr;
  }

  .course-card {
    max-width: none;
  }
}

@media (max-width: 768px) {
  .courses-list {
    margin-left: 0;
  }

  .center-card {
    width: auto;
    margin-left: 0;
  }

  h1 {
    margin-left: 0;
    text-align: center;
  }

  .planner-section {
    margin-top: 20px;
  }
}
</style>