<script setup>
import { useForm, router, usePage } from '@inertiajs/vue3'  // adicionei usePage
import { toast } from 'vue3-toastify'
import 'vue3-toastify/dist/index.css'
import { watch } from 'vue'
import CorujaImg from '@/assets/images/CorujaSEMFUNDO.png'

// Verifica se já está logado, se sim redireciona direto pro dashboard
const auth = usePage().props.auth
if (auth && auth.user) {
  router.replace('/dashboard')
}

const form = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  role: 'student',
})

watch(
  () => form.errors,
  (errs) => {
    Object.values(errs).flat().forEach(msg => toast.error(msg))
  }
)

function submit() {
  form.post(route('register'), {
    onSuccess: () => {
      toast.success('Registrado com sucesso! Faça login.')
      router.replace(route('login')) // trocar para replace ao invés de get
    },
  })
}
</script>

<template>
  <div class="flex h-screen">
    <div class="w-1/2 flex items-center justify-center bg-yellow-100">
      <img :src="CorujaImg" alt="Coruja Acadêmica" class="w-96" />
    </div>
    <div class="w-1/2 flex items-center justify-center bg-white p-10">
      <div class="w-full max-w-md">
        <h2 class="text-2xl font-bold text-center mb-6">Cadastre-se</h2>
        <form @submit.prevent="submit" class="space-y-4">
          <!-- Nome -->
          <input
            v-model="form.name"
            type="text"
            placeholder="Nome"
            class="campo-entrada"
            :class="{ 'border-red-500': form.errors.name }"
          />

          <!-- Email -->
          <input
            v-model="form.email"
            type="email"
            placeholder="E-mail"
            class="campo-entrada"
            :class="{ 'border-red-500': form.errors.email }"
          />

          <!-- Senha -->
          <input
            v-model="form.password"
            type="password"
            placeholder="Senha"
            class="campo-entrada"
            :class="{ 'border-red-500': form.errors.password }"
          />

          <!-- Confirma Senha -->
          <input
            v-model="form.password_confirmation"
            type="password"
            placeholder="Confirmar senha"
            class="campo-entrada"
            :class="{ 'border-red-500': form.errors.password_confirmation }"
          />

          <!-- Role -->
          <select
            v-model="form.role"
            class="campo-entrada"
            :class="{ 'border-red-500': form.errors.role }"
          >
            <option value="student">Aluno</option>
            <option value="monitor">Monitor</option>
          </select>

          <button
            type="submit"
            class="botao-enviar"
            :disabled="form.processing"
          >
            {{ form.processing ? 'Enviando...' : 'Cadastre-se' }}
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<style scoped>
.campo-entrada {
  width: 100%;
  padding: 12px;
  border: 1px solid #ccc;
  border-radius: 8px;
  font-size: 16px;
  outline: none;
  transition: border-color 0.3s;
}
.campo-entrada:focus {
  border-color: #3b82f6;
}
.border-red-500 {
  border-color: #f87171 !important;
}
.botao-enviar {
  width: 100%;
  background-color: #135572;
  color: white;
  font-weight: bold;
  padding: 12px;
  border-radius: 8px;
  cursor: pointer;
  transition: background-color 0.3s;
}
.botao-enviar[disabled] {
  opacity: 0.6;
  cursor: not-allowed;
}
.botao-enviar:hover:enabled {
  background-color: #53bbe9;
}
</style>
