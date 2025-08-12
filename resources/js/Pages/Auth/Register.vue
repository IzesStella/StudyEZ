<script setup>
import { useForm, router, usePage } from '@inertiajs/vue3'
import { toast } from 'vue3-toastify'
import 'vue3-toastify/dist/index.css'
import { watch } from 'vue'

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
      router.replace(route('login'))
    },
  })
}
</script>

<template>
  <div class="tela flex items-center justify-center p-4 min-h-screen">
    <div class="container-form w-full max-w-md">
      <h2 class="text-2xl font-bold text-center mb-6">Cadastre-se</h2>
      <form @submit.prevent="submit" class="space-y-4">
        <input
          v-model="form.name"
          type="text"
          placeholder="Nome"
          class="campo-entrada"
          :class="{ 'border-red-500': form.errors.name }"
        />

        <input
          v-model="form.email"
          type="email"
          placeholder="E-mail"
          class="campo-entrada"
          :class="{ 'border-red-500': form.errors.email }"
        />

        <input
          v-model="form.password"
          type="password"
          placeholder="Senha"
          class="campo-entrada"
          :class="{ 'border-red-500': form.errors.password }"
        />

        <input
          v-model="form.password_confirmation"
          type="password"
          placeholder="Confirmar senha"
          class="campo-entrada"
          :class="{ 'border-red-500': form.errors.password_confirmation }"
        />

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

      <div class="link-login-container">
        <p class="ja-e-cadastrado">
          Já é cadastrado?
          <a :href="route('login')" class="link-login">Faça seu login!</a>
        </p>
      </div>
    </div>
  </div>
</template>

<style scoped>
.tela {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 100vh;
  background-image: url('/images/BackgroundLogin.png');
  background-size: cover;
}

.container-form {
  background: #f8fafc;
  border-radius: 16px;
  box-shadow: 0 4px 24px 0 rgba(14, 0, 0, 0.08);
  padding: 32px 24px;
  margin: 0 auto;
}
.campo-entrada {
  width: 100%;
  padding: 12px;
  border: 1px solid #ccc;
  border-radius: 8px;
  font-size: 16px;
  outline: none;
  transition: border-color 0.3s;
}
.botao-enviar {
  width: 40%;
  background-color: #B0D5FF;
  color: rgb(0, 0, 0);
  font-weight: bold;
  padding: 12px;
  border-radius: 8px;
  cursor: pointer;
  transition: background-color 0.3s;
  display: block;
  margin: 0 auto;
}
.botao-enviar[disabled] {
  opacity: 0.6;
  cursor: not-allowed;
}
.botao-enviar:hover:enabled {
  background-color: #53bbe9;
}

.link-login-container {
  text-align: center;
  margin-top: 20px;
}

.ja-e-cadastrado {
  font-size: 14px;
  color: #555;
}

.link-login {
  color: #002F66;
  font-weight: bold;
  text-decoration: none;
}

.link-login:hover {
  text-decoration: underline;
}
</style>