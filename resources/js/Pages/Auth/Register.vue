<template>
  <div class="flex h-screen">
    <!-- Lado esquerdo -->
    <div class="w-1/2 flex items-center justify-center bg-yellow-100">
      <img
        src="/images/CorujaSEMFUNDO.png"
        alt="Coruja Acadêmica"
        class="w-96"
      />
    </div>

    <!-- Lado direito -->

    <div class="w-1/2 flex items-center justify-center bg-white p-10">
      <div class="w-full max-w-md">
        <h2 class="text-2xl font-bold text-center mb-6">Cadastre-se</h2>

        <!-- Mensagens de erro -->
        <div
          v-if="errors.length"
          class="mb-4 p-3 bg-red-100 text-red-600 rounded-lg"
        >
          <ul>
            <li v-for="(error, index) in errors" :key="index">{{ error }}</li>
          </ul>
        </div>

        <!-- Formulário -->
        <form @submit.prevent="sendForm" class="space-y-4">
          <input
            type="text"
            v-model="form.name"
            placeholder="Nome"
            class="campo-entrada"
          />
          <input
            type="email"
            v-model="form.email"
            placeholder="E-mail"
            class="campo-entrada"
          />
          <input
            type="password"
            v-model="form.password"
            placeholder="Senha"
            class="campo-entrada"
          />
          <input
            type="password"
            v-model="form.password_confirmation"
            placeholder="Confirmar senha"
            class="campo-entrada"
          />
          <div class="flex justify-center">
            <button type="submit" class="botao-enviar">Cadastre-se</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { toast } from 'vue3-toastify';

export default {
  data() {
    return {
      //tive que colocar os nomes em ingles, no banco ta em ingles.
      form: {
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
      },
      errors: [],
      loading: false,
    };
  },
  methods: {
    sendForm() {
      this.loading = true;
      if (this.form.password !== this.form.password_confirmation) {
        this.errors = { password: ['As senhas não correspondem.'] };
        toast.error(this.errors.password[0], {
          position: 'top-center',
          duration: 3000,
        });
        this.loading = false;
        return;
      }

      this.errors = {}; // Limpa erros antes de enviar

      this.$inertia.post('/register', this.form, {
        onSuccess: () => {
          console.log('Usuário cadastrado com sucesso!');
          toast.success('Registro bem-sucedido!', {
            position: 'top-center',
            duration: 3000,
          });
          this.$inertia.visit(route('login'));
        },

        onError: (errors) => {
          console.log(errors);
          for (let field in errors) {
            if (errors[field]) {
              toast.error(errors[field], {
                position: 'top-center',
                duration: 3000,
              });
            }
          }
          this.errors = errors;
        },

      onFinish: () => {
          this.loading = false;
        },
      });
    },
  },
};
</script>

<style scoped>
.campo-entrada {
  width: 100%;
  padding: 12px;
  border: 1px solid #ccc;
  border-radius: 8px;
  font-size: 16px;
  outline: none;
  background-color: white;
  transition: border-color 0.3s;
}

.campo-entrada:focus {
  border-color: #3b82f6;
}

.botao-enviar {
  width: 70%;
  background-color: #135572;
  color: white;
  font-weight: bold;
  padding: 12px;
  border-radius: 8px;
  text-align: center;
  transition: background-color 0.3s;
}

.botao-enviar:hover {
  background-color: #53bbe9;
}
</style>