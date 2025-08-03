<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage, router } from '@inertiajs/vue3';
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';

const props = defineProps({
    mustVerifyEmail: Boolean,
    status: String,
});

const emit = defineEmits(['success']);

const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    email: user.email,
    bio: user.bio || '',
});

const updateProfile = () => {
    form.patch(route('profile.update'), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Perfil atualizado com sucesso!');
            emit('success');
            // Aguarda um pouco antes de navegar para mostrar o toast
            setTimeout(() => {
                router.visit(route('profile.show'));
            }, 1000);
        },
        onError: (errors) => {
            console.log('Erros:', errors);
            toast.error('Erro ao atualizar perfil');
        }
    });
};
</script>

<template>
    <section>
        <header>
            <h2>Informações do Perfil</h2>
        </header>
        <form @submit.prevent="updateProfile">
            <div class="form-group">
                <InputLabel for="name" value="Nome" />
                <TextInput
                    id="name"
                    type="text"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                />
                <InputError :message="form.errors.name" />
            </div>
            <div class="form-group">
                <InputLabel for="email" value="E-mail" />
                <TextInput
                    id="email"
                    type="email"
                    v-model="form.email"
                    required
                    autocomplete="username"
                />
                <InputError :message="form.errors.email" />
            </div>
            <div class="form-group">
                <InputLabel for="bio" value="Biografia" />
                <textarea
                    id="bio"
                    v-model="form.bio"
                    rows="3"
                    maxlength="500"
                    placeholder="Conte um pouco sobre você..."
                ></textarea>
                <InputError :message="form.errors.bio" />
                <p class="bio-count">{{ form.bio ? form.bio.length : 0 }}/500 caracteres</p>
            </div>
            <div v-if="props.mustVerifyEmail && user.email_verified_at === null" class="verify-block">
                <p>
                    Seu endereço de e-mail não está verificado.
                    <Link
                        :href="route('verification.send')"
                        method="post"
                        as="button"
                        class="verify-link"
                    >
                        Clique aqui para reenviar o e-mail de verificação.
                    </Link>
                </p>
                <div v-show="props.status === 'verification-link-sent'" class="verify-success">
                    Um novo link de verificação foi enviado para seu e-mail.
                </div>
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
.form-group textarea:focus {
    border: 1.5px solid #60a5fa;
}
.form-group textarea {
    resize: none;
    min-height: 38px;
    max-height: 120px;
}
.bio-count {
    color: #111;
    font-size: 0.92rem;
    margin-top: 4px;
}
.verify-block {
    margin-top: 8px;
    width: 100%;
}
.verify-link {
    text-decoration: underline;
    color: #25608A;
    font-size: 0.97rem;
    border-radius: 4px;
    margin-left: 2px;
    transition: color 0.2s;
}
.verify-link:hover {
    color: #135572;
}
.verify-success {
    margin-top: 6px;
    font-weight: 500;
    color: #059669;
    font-size: 0.97rem;
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
