<script setup>
import DangerButton from '@/Components/DangerButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;

    nextTick(() => passwordInput.value.focus());
};

const deleteUser = () => {
    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;

    form.reset();
};
</script>

<template>
    <section class="delete-account-row">
        <span class="delete-label">Deseja remover sua conta?</span>
        <button class="delete-btn" @click="confirmUserDeletion">Remover Conta</button>

        <Modal :show="confirmingUserDeletion" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">
                    Tem certeza que deseja remover sua conta?
                </h2>
                <p class="mt-1 text-sm text-gray-600">
                    Esta ação é irreversível. Para confirmar, digite sua senha.
                </p>
                <div class="mt-6">
                    <InputLabel for="password" value="Senha" class="sr-only" />
                    <TextInput
                        id="password"
                        ref="passwordInput"
                        v-model="form.password"
                        type="password"
                        class="mt-1 block w-3/4"
                        placeholder="Senha"
                        @keyup.enter="deleteUser"
                    />
                    <InputError :message="form.errors.password" class="mt-2" />
                </div>
                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeModal">Cancelar</SecondaryButton>
                    <button
                        class="delete-btn-modal ml-3"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        @click="deleteUser"
                    >Remover Conta</button>
                </div>
            </div>
        </Modal>
    </section>
</template>
<style scoped>
.delete-account-row {
    display: flex;
    align-items: center;
    gap: 18px;
    margin-top: 32px;
    margin-bottom: 32px;
}
.delete-label {
    font-size: 1.15rem;
    color: #222;
    font-family: 'Montserrat', sans-serif;
    font-weight: 700;
}
.delete-btn {
    background: #FF9500;
    color: #222;
    font-family: 'Montserrat', sans-serif;
    font-weight: 600;
    font-size: 1rem;
    border: none;
    border-radius: 10px;
    padding: 10px 24px;
    cursor: pointer;
    transition: background 0.2s, color 0.2s;
    box-shadow: none;
}
.delete-btn:hover:not(:disabled), .delete-btn-modal:hover:not(:disabled) {
    background: #e68900;
    color: #fff;
}
.delete-btn:disabled, .delete-btn-modal:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}
.delete-btn-modal {
    background: #FF9500;
    color: #222;
    font-family: 'Montserrat', sans-serif;
    font-weight: 600;
    font-size: 1rem;
    border: none;
    border-radius: 10px;
    padding: 10px 24px;
    cursor: pointer;
    transition: background 0.2s, color 0.2s;
    box-shadow: none;
}
</style>
