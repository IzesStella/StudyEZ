<template>
  <div class="planner-bar" :class="{ open: isOpen }">
    <button class="toggle-btn" @click="toggleBar">
      <div class="toggle-line"></div>
    </button>

    <div v-if="isOpen" class="content">
      <h2 class="titulo">Seu Planner</h2>

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
// Adicionado 'isValid' para a verificação de datas
import { format, parseISO, isValid } from 'date-fns';
import { utcToZonedTime } from 'date-fns-tz';

const isOpen = ref(false);
const note = ref('');
const selectedDate = ref(''); // Inicializado como string vazia
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

  // Evita reprocessar se a data já estiver selecionada
  if (selectedDate.value === dateString) return;
  selectedDate.value = dateString;
}

watch(selectedDate, async (dateString) => {
  // Se não há data selecionada, limpa a nota e sai
  if (!dateString) {
    note.value = '';
    return;
  }
  
  isLoading.value = true;
  note.value = ''; // Limpa a nota enquanto carrega

  try {
    const response = await fetch(`/planner?date=${dateString}`, {
      headers: { 'Accept': 'application/json' },
    });
    if (!response.ok) throw new Error(`Erro ao buscar nota: ${response.status}`);
    const data = await response.json();
    
    // Garante que a nota carregada corresponde à data ainda selecionada
    if (selectedDate.value === dateString) {
      note.value = data.note || '';
    }
  } catch (error) {
    console.error('Falha ao carregar a nota:', error);
  } finally {
    // Garante que isLoading seja falso apenas se a data ainda for a mesma
    if (selectedDate.value === dateString) {
      isLoading.value = false;
    }
  }
});

async function saveNote() {
  const dateString = selectedDate.value;
  // Não salva se não há data selecionada ou se já está salvando
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
    note.value = data.note; // Atualiza a nota com o que o servidor retornou (pode ser útil para confirmação)

    showSuccessMessage.value = true;
    setTimeout(() => { showSuccessMessage.value = false; }, 3000); // Esconde a mensagem após 3 segundos

  } catch (error) {
    console.error('Erro ao salvar nota:', error);
  } finally {
    isSaving.value = false;
  }
}

// PROPRIEDADE COMPUTADA COM A CORREÇÃO
const calendarAttributes = computed(() => {
  // 1. Se selectedDate.value for uma string vazia, retorna um array vazio.
  // Isso evita que parseISO receba uma string vazia e crie um objeto de data inválido.
  if (!selectedDate.value) {
    return [];
  }

  const dateForCalendar = parseISO(selectedDate.value);

  // 2. Adiciona uma verificação extra com isValid para garantir que a data parseada seja realmente válida.
  // Se não for, também retorna um array vazio.
  if (!isValid(dateForCalendar)) {
    console.warn('Data inválida detectada para o calendário:', selectedDate.value);
    return [];
  }

  return [{
    key: 'selected',
    highlight: {
      style: {
        backgroundColor: 'transparent',
        border: '2px solid #135572',
      },
      contentStyle: {
        color: '#135572',
      }
    },
    dates: dateForCalendar,
  }];
});
</script>

<style scoped>
/* ESTILOS CSS - NENHUMA ALTERAÇÃO AQUI */
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

.vc-day.selected {
  background-color: transparent !important;
  border: 2px solid #135572 !important;
  color: #135572 !important;
}

.vc-day:hover {
  background-color: transparent !important;
  color: #135572 !important;
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
  /* Cor atualizada para #B0D5FF */
  background: #B0D5FF;
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
  background-color: transparent;
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
  background-color: transparent;
}

.custom-calendar .vc-day {
  background-color: transparent;
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