<template>
  <div class="planner-bar" :class="{ open: isOpen }">
    <button class="toggle-btn" @click="toggleBar">
      <div class="toggle-line"></div>
    </button>

    <div v-if="isOpen" class="content">
      <h2 class="titulo">Seu Planner</h2>

      <!-- Calendário agora com a lógica de data e destaque corrigida -->
      <v-calendar
        :attributes="calendarAttributes"
        locale="pt-BR"
        :weekdays="['Dom','Seg','Ter','Qua','Qui','Sex','Sáb']"
        title-position="center"
        :show-weeknumbers="false"
        @dayclick="onDateSelect"
        class="custom-calendar"
      />

      <div class="notes-section">
        <div class="caderno-furos">
          <span v-for="n in 5" :key="n"></span>
        </div>
        <div class="caderno-linha-vertical"></div>

        <textarea
          v-model="note"
          :disabled="isLoading"
          :placeholder="isLoading ? 'Carregando...' : 'Escreva suas anotações aqui...'">
        </textarea>

        <button class="save-btn" @click="saveNote" :disabled="isSaving || isLoading">
          {{ isSaving ? 'Salvando...' : 'Salvar' }}
        </button>

        <div v-if="showSuccessMessage" class="success-notification">
          Nota salva com sucesso!
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { Calendar as VCalendar } from 'v-calendar';
import 'v-calendar/style.css';
import { format, toDate } from 'date-fns';
import { utcToZonedTime } from 'date-fns-tz';

const isOpen = ref(false);
const note = ref('');
const selectedDate = ref('');
const isLoading = ref(false);
const isSaving = ref(false);
const showSuccessMessage = ref(false);

function toggleBar() {
  isOpen.value = !isOpen.value;
}

function onDateSelect(day) {
  const userTimeZone = Intl.DateTimeFormat().resolvedOptions().timeZone;
  const zonedDate = utcToZonedTime(day.date, userTimeZone);
  const dateString = format(zonedDate, 'yyyy-MM-dd');

  if (selectedDate.value === dateString) return;
  selectedDate.value = dateString;
}

watch(selectedDate, async (dateString) => {
  if (!dateString) {
    note.value = '';
    return;
  }
  isLoading.value = true;
  note.value = '';

  try {
    const response = await fetch(`/planner?date=${dateString}`, {
      headers: { 'Accept': 'application/json' },
    });
    if (!response.ok) throw new Error(`Erro ao buscar nota: ${response.status}`);
    const data = await response.json();
    if (selectedDate.value === dateString) {
      note.value = data.note || '';
    }
  } catch (error) {
    console.error('Falha ao carregar a nota:', error);
  } finally {
    if (selectedDate.value === dateString) {
      isLoading.value = false;
    }
  }
});

async function saveNote() {
  const dateString = selectedDate.value;
  if (!dateString || isSaving.value) return;

  isSaving.value = true;
  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
  if (!csrfToken) {
    console.error('Token CSRF não encontrado!');
    isSaving.value = false;
    return;
  }

  try {
    const response = await fetch('/planner', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
      },
      body: JSON.stringify({
        date: dateString,
        note: note.value,
      }),
    });

    if (!response.ok) {
      const errorText = await response.text();
      throw new Error(`Falha ao salvar a nota: ${response.status} ${errorText}`);
    }

    const data = await response.json();
    note.value = data.note;

    showSuccessMessage.value = true;
    setTimeout(() => { showSuccessMessage.value = false; }, 3000);

  } catch (error) {
    console.error('Erro ao salvar nota:', error);
  } finally {
    isSaving.value = false;
  }
}

const calendarAttributes = computed(() => {
  if (!selectedDate.value) return [];

  const dateForCalendar = toDate(selectedDate.value, { timeZone: 'UTC' });

  return [{
    key: 'selected',
    highlight: {
      style: {
        backgroundColor: 'transparent', // Garantir que o fundo azul seja removido
        border: '2px solid #135572',     // Borda azul ao invés de fundo azul
      },
      contentStyle: {
        color: '#135572',  // Cor do número do dia 
      }
    },
    dates: dateForCalendar,
  }];
});
</script>

<style scoped>
.success-notification {
  position: absolute;
  bottom: 20px;
  left: 50%;
  transform: translateX(-50%);
  background-color: #28a745;
  color: white;
  padding: 10px 20px;
  border-radius: 8px;
  z-index: 110;
  font-size: 0.9rem;
  font-weight: bold;
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
  animation: fadeInOut 3s forwards;
  pointer-events: none;
}

/* Remover o fundo azul do dia selecionado no calendário */
.vc-day.selected {
  background-color: transparent !important; /* Removendo o fundo azul */
  border: 2px solid #135572 !important; /* Adicionando borda ao dia selecionado */
  color: #135572 !important; /* Definindo a cor do número do dia */
}

/* Alterar o fundo do dia quando o cursor passar sobre ele */
.vc-day:hover {
  background-color: transparent !important; /* Removendo o fundo azul do hover */
  color: #135572 !important; /* Definindo a cor do número do dia no hover */
}

@keyframes fadeInOut {
  0% { opacity: 0; transform: translate(-50%, 10px); }
  20% { opacity: 1; transform: translate(-50%, 0); }
  80% { opacity: 1; transform: translate(-50%, 0); }
  100% { opacity: 0; transform: translate(-50%, 10px); }
}

.planner-bar {
  position: fixed;
  top: 0;
  right: 0;
  height: 100vh;
  background: transparent; /* Removido o fundo azul */
  box-shadow: -2px 0 10px rgba(0,0,0,0.07);
  border-top-left-radius: 32px;
  border-bottom-left-radius: 32px;
  z-index: 100;
  display: flex;
  flex-direction: column;
  align-items: stretch;
  transition: width 0.3s, transform 0.3s;
  width: 60px;
}

.planner-bar.open {
  width: 310px;
  transform: translateX(0);
}

.toggle-btn {
  position: absolute;
  left: 12px;
  top: 50%;
  transform: translateY(-50%);
  border: none;
  border-radius: 50%;
  width: auto;
  height: auto;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 101;
  background: transparent;
}

.toggle-line {
  width: 8px;
  height: 70px;
  background: #135572;
  border-radius: 10px;
}

.content {
  padding: 32px 32px 24px 40px;
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: flex-start;
  opacity: 1;
  transition: opacity 0.3s ease-in-out;
  overflow: hidden;
}

.planner-bar:not(.open) .content {
    opacity: 0;
    pointer-events: none;
}

.content h2 {
  font-size: 20px;
  color: #135572;
  font-weight: bold;
  padding: 10px;
  margin-bottom: 10px;
}

.notes-section {
  position: relative;
  width: 100%;
  margin-top: 16px;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.notes-section textarea {
  width: 250px;
  min-height: 250px;
  max-height: 300px;
  border-radius: 16px;
  border: none;
  padding: 24px 16px 16px 32px;
  font-size: 1rem;
  resize: vertical;
  background: repeating-linear-gradient(
    to bottom,
    #fff 0px,
    #fff 23px,
    #e3f0ff 24px,
    #fff 25px
  );
  box-shadow: 0 2px 8px rgba(0,0,0,0.05);
  outline: none;
  scrollbar-width: thin;
  scrollbar-color: #FF9500 #e3f0ff;
  transition: background-color 0.2s;
}

.notes-section textarea:disabled {
    background-color: #f0f0f0;
    cursor: wait;
}

.notes-section .caderno-furos {
  position: absolute;
  top: 8px;
  left: 32px;
  display: flex;
  gap: 20px;
}

.notes-section .caderno-furos span {
  width: 20px;
  height: 20px;
  background: #b0d5ff;
  border-radius: 50%;
  display: inline-block;
  box-shadow: 0 1px 2px rgba(0,0,0,0.07);
}

.notes-section .caderno-linha-vertical {
  position: absolute;
  top: 0px;
  left: 30px;
  width: 1px;
  height: 100%;
  max-height: 250px;
  background: #FF9500;
  border-radius: 2px;
  z-index: 2;
  pointer-events: none;
}

.custom-calendar {
  margin: 0 auto;
  border: none;
  border-radius: 8px;
  width: 100%;
  background-color: transparent; /* Removido o fundo azul do calendário */
}

.custom-calendar .vc-day {
  background-color: transparent; /* Removido o fundo azul dos dias */
}

.save-btn {
  margin-top: 12px;
  padding: 8px 16px;
  background: #135572;
  color: #fff;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-weight: bold;
  transition: background-color 0.2s, opacity 0.2s;
  width: 120px;
  text-align: center;
}
.save-btn:hover {
  opacity: 0.9;
}

.save-btn:disabled {
    background-color: #5a7a8a;
    cursor: not-allowed;
    opacity: 0.7;
}

.notes-section textarea::-webkit-scrollbar {
  width: 12px;
}
</style>
