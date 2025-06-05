<template>
  <div class="tela">
    <div class="caixa">
      <!-- Lado esquerdo (imagem) -->
      <div class="lado-esquerdo">
        <img src="/images/imgLogin.png" alt="Login" class="Login" />
      </div>

      <!-- Lado direito (formulário) -->
      <div class="lado-direito">
        <h2 class="titulo">Login</h2>
        <form @submit.prevent="login">
          <div class="campo">
            <div class="input-box">
              <span class="icone">📧</span>
              <input
                type="email"
                v-model="form.email"
                placeholder="E-mail"
                class="input"
              />
            </div>
          </div>

          <div class="campo">
            <div class="input-box">
              <span class="icone">🔒</span>
              <input
                type="password"
                v-model="form.password"
                placeholder="Senha"
                class="input"
              />
            </div>
          </div>

          <div class="esqueci">
            <a href="#" class="link">Esqueceu sua senha?</a>
          </div>

          <button type="submit" class="botao">Entrar</button>
        </form>
      </div>
    </div>
  </div>
</template>

<script>

export default {
  components: {
   
  },
  data() {
    return {
      form: {
        email: '',
        password: '',
      },
      errors: [],
      loading: false,
    };
  },
  methods: {
    login() {
      this.loading = true;
      this.$inertia.post('/login', this.form, {
        onSuccess: () => {
          toast.success('Login bem-sucedido!', {
            position: 'top-center',
            duration: 3000,
          });
          // Redirecionamento pode ser definido aqui ou no controller
        },
        onError: (errors) => {
          const errorMessage =
            errors.message || 'Ocorreu um erro ao fazer login.';
          toast.error(errorMessage, {
            position: 'top-center',
            duration: 3000,
          });
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
.tela {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 100vh;
  background-image: url('/images/BackgroundLogin.png'); 
  background-size: cover;
}

.caixa {
  display: flex;
  background: white;
  box-shadow: 0px 4px 10px rgba(135, 135, 135, 0.2);
  border-radius: 10px;
  overflow: hidden;
  max-width: 900px;
  width: 90%;
}

.lado-esquerdo {
  background: #002F66;
  display: flex;
  align-items: center;
  justify-content: center;
  flex: 1;
  padding: 20px;
}

.Login {
  width: 100%;
  max-width: none;
  height: auto;
  display: block;
}

.lado-direito {
  flex: 1;
  padding: 40px;
}

.titulo {
  text-align: center;
  font-size: 24px;
  font-weight: bold;
  margin-bottom: 20px;
}

.campo {
  margin-bottom: 15px;
  margin-top: 40px;
}

.input-box {
  position: relative;
}

.icone {
  position: absolute;
  left: 10px;
  top: 50%;
  transform: translateY(-50%);
  color: #777;
}

.input {
  width: 100%;
  padding: 10px 10px 10px 35px;
  border: 1px solid #ccc;
  border-radius: 5px;
  outline: none;
}

.input:focus {
  border-color: #ffffff;
  box-shadow: 0 0 5px rgba(0, 123, 255, 0.3);
}

.esqueci {
  text-align: right;
  font-size: 12px;
  margin-bottom: 10px;
}

.link {
  color: #333;
  text-decoration: none;
}

.link:hover {
  text-decoration: underline;
}

.botao {
  width: 50%;
  background: #B0D5FF;
  color: #333;
  padding: 12px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  font-size: 16px;
  display: block;
  margin: 20px auto 0;
}

.botao:hover {
  background: #53bbe9;
}

@media (max-width: 768px) {
  .caixa {
    flex-direction: column;
    text-align: center;
  }

  .lado-esquerdo {
    order: -1;
    padding: 30px;
  }

  .coruja {
    max-width: 200px;
  }

  .lado-direito {
    padding: 20px;
  }
}
</style>