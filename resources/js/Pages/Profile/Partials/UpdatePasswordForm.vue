<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm, router } from '@inertiajs/vue3';
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';
import { ref } from 'vue';

const emit = defineEmits(['success']);

const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    form.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            toast.success('Senha atualizada com sucesso!');
            emit('success');
            // Aguarda um pouco antes de navegar para mostrar o toast
            setTimeout(() => {
                router.visit(route('profile.show'));
            }, 1000);
        },
        onError: () => {
            toast.error('Erro ao atualizar senha');
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value.focus();
            }
            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value.focus();
            }
        },
    });
};
</script>

<template>
    <section>
        <header>
            <h2>Alterar Senha</h2>
        </header>
        <form @submit.prevent="updatePassword">
            <div class="form-group">
                <InputLabel for="current_password" value="Senha Atual" />
                <TextInput
                    id="current_password"
                    ref="currentPasswordInput"
                    v-model="form.current_password"
                    type="password"
                    autocomplete="current-password"
                />
                <InputError :message="form.errors.current_password" />
            </div>
            <div class="form-group">
                <InputLabel for="password" value="Nova Senha" />
                <TextInput
                    id="password"
                    ref="passwordInput"
                    v-model="form.password"
                    type="password"
                    autocomplete="new-password"
                />
                <InputError :message="form.errors.password" />
            </div>
            <div class="form-group">
                <InputLabel for="password_confirmation" value="Confirmar Nova Senha" />
                <TextInput
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    autocomplete="new-password"
                />
                <InputError :message="form.errors.password_confirmation" />
            </div>
            <div class="form-actions">
                <PrimaryButton :disabled="form.processing">Salvar</PrimaryButton>
                <Transition enter-from-class="opacity-0" leave-to-class="opacity-0" class="transition-fade">
                    <p v-if="form.recentlySuccessful" class="saved-msg">Salvo.</p>
                </Transition>
            </div>
        </form>
    </section>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap');

section {
    font-family: 'Montserrat', sans-serif;
    width: 100%;
    max-width: 500px;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
}
section header {
    width: 100%;
    margin-bottom: 8px;
}
section h2 {
    color: #111;
    font-family: 'Montserrat', sans-serif;
    font-weight: 600;
    font-size: 1.1rem;
    margin-bottom: 2px;
}
section p {
    color: #111;
    font-family: 'Montserrat', sans-serif;
    font-size: 0.97rem;
    margin-bottom: 0;
}
.form-group {
    width: 100%;
    margin-bottom: 10px;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
}
.form-group input[type="text"],
.form-group input[type="email"],
.form-group input[type="password"],
.form-group textarea {
    width: 100%;
    min-width: 350px;
    max-width: 700px;
    border: 1.5px solid #d1d5db;
    border-radius: 8px;
    padding: 4px 14px;
    font-size: 1.12rem;
    font-family: 'Montserrat', sans-serif;
    background: #fff;
    margin-top: 4px;
    margin-bottom: 0;
    box-sizing: border-box;
    outline: none;
    transition: border 0.2s;
}
.form-group input[type="text"]:focus,
.form-group input[type="email"]:focus,
.form-group input[type="password"]:focus,
.form-group textarea:focus {
    border: 1.5px solid #60a5fa;
}
.form-group textarea {
    resize: none;
    min-height: 38px;
    max-height: 120px;
}
.form-actions {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 0.5rem;
    margin-top: 2px;
}
.form-actions button,
.form-actions .PrimaryButton {
    background: #93c5fd;
    color: #222;
    border: none;
    border-radius: 3px;
    padding: 0 14px;
    height: 36px;
    font-weight: 600;
    font-size: 0.95rem;
    box-shadow: none;
    margin-left: auto;
    margin-right: 0;
    transition: background 0.2s;
}
.form-actions button:active,
.form-actions button:focus,
.form-actions button:hover {
    background: #60a5fa;
}
.saved-msg {
    color: #444;
    font-size: 0.97rem;
    margin-left: 8px;
}
.transition-fade {
    transition: opacity 0.2s;
}
</style>
