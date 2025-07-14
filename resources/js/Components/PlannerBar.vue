<template>
  <div class="planner-bar" :class="{ open: isOpen }">
    <button class="toggle-btn" @click="toggleBar">
      <div class="toggle-line"></div>
    </button>
    <div v-if="isOpen" class="content">
      <h2 class="titulo">Seu Planner</h2>
      <v-calendar
        :attributes="calendarAttributes"
        :locale="'pt-BR'"
        :weekdays="['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb']"
        :title-position="'center'"
        :show-weeknumbers="false"
        @dayclick="onDateSelect"
        class="custom-calendar"
      />
      <div class="notes-section">
        <div class="caderno-furos">
          <span></span>
          <span></span>
          <span></span>
          <span></span>
          <span></span>
        </div>
        <div class="caderno-linha-vertical"></div>
        <textarea v-model="note" placeholder="Escreva suas anotações aqui..." />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Calendar as VCalendar } from 'v-calendar'
import 'v-calendar/style.css'

const isOpen = ref(false)
const note = ref('')
const selectedDate = ref(null)

function toggleBar() {
  isOpen.value = !isOpen.value
}

function onDateSelect(day) {
  selectedDate.value = day.date
}

const calendarAttributes = computed(() => {
  return selectedDate.value
    ? [{
        key: 'selected',
        highlight: true,
        dates: selectedDate.value,
        customData: {},
        class: 'selected-day'
      }]
    : []
})
</script>

<style scoped>
.planner-bar {
  position: fixed;
  top: 0;
  right: 0;
  height: 100vh;
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
  justify-content: center;  
}   

.content h2 {
  font-size: 20px;
  color: #135572;
  font-weight: bold;
  padding: 10px;
}
.notes-section {
  position: relative;
  width: 100%;
  margin-top: 16px;
}

.notes-section textarea {
  width: 250px;
  min-height: 300px;
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
}


/* Furos do caderno */
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

/* Linha vertical do caderno */
.notes-section .caderno-linha-vertical {
  position: absolute;
  top: 0px;
  left: 30px;
  width: 1px;
  height: 300px;
  background: #FF9500;
  border-radius: 2px;
  z-index: 2;
  pointer-events: none;
}

/* Personalize o tamanho dos nomes dos dias */
.custom-calendar .vc-weekday {
  font-size: 0.85rem;
  padding: 2px 0;
}

.custom-calendar {
  margin: 0 auto;
}

/* WebKit (Chrome, Edge, Safari) */
.notes-section textarea::-webkit-scrollbar {
  width: 12px;
}
</style>