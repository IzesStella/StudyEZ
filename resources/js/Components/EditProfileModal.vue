<script setup>
import { ref } from 'vue'
import UpdateProfileInformationForm from '@/Pages/Profile/Partials/UpdateProfileInformationForm.vue'
import UpdatePasswordForm from '@/Pages/Profile/Partials/UpdatePasswordForm.vue'
import DeleteUserForm from '@/Pages/Profile/Partials/DeleteUserForm.vue'
import AvatarUpload from '@/Components/AvatarUpload.vue'

defineProps({
    mustVerifyEmail: {
        type: Boolean,
        default: false
    },
    status: String
})

const emit = defineEmits(['close'])
const avatarUploadRef = ref(null)

function closeModal() {
    emit('close')
}

function handleFormSuccess() {
    // Fechar modal quando algum formulário for salvo com sucesso
    closeModal()
}
</script>

<template>
    <div class="modal-overlay" @click="closeModal">
        <div class="modal-content" @click.stop>
            <div class="modal-header">
                <h2>Editar Perfil</h2>
                <button class="close-btn" @click="closeModal">&times;</button>
            </div>
            
            <div class="modal-body">
                <!-- Seção de Upload de Avatar -->
                <div class="form-section">
                    <AvatarUpload ref="avatarUploadRef" />
                </div>

                <!-- Informações do Perfil -->
                <div class="form-section">
                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                        @success="handleFormSuccess"
                    />
                </div>

                <!-- Alterar Senha -->
                <div class="form-section">
                    <UpdatePasswordForm @success="handleFormSuccess" />
                </div>

                <!-- Deletar Conta -->
                <div class="form-section">
                    <DeleteUserForm />
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap');

.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
    backdrop-filter: blur(2px);
}

.modal-content {
    background: #FEFEFE;
    border-radius: 20px;
    max-width: 650px;
    width: 90%;
    max-height: 85vh;
    overflow: hidden;
    box-shadow: 0 20px 40px rgba(19, 85, 114, 0.15);
    border: 2px solid #B0D5FF;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 24px 28px;
    background:#b0d5ff;
    color: white;
}

.modal-header h2 {
    margin: 0;
    font-family: 'Montserrat', sans-serif;
    font-weight: 700;
    font-size: 1.5rem;
    color: white;
}

.close-btn {
    background: none;
    border: none;
    font-size: 28px;
    cursor: pointer;
    color: white;
    padding: 0;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: all 0.3s ease;
}

.close-btn:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: rotate(90deg);
}

.modal-body {
    padding: 28px;
    max-height: calc(85vh - 120px);
    overflow-y: auto;
}

.form-section {
    margin-bottom: 24px;
    padding: 20px;
    background: #F8FBFF;
    border-radius: 12px;
    border: 1px solid #E0F0FF;
    transition: all 0.3s ease;
}

.form-section:hover {
    border-color: #B0D5FF;
    box-shadow: 0 4px 12px rgba(19, 85, 114, 0.08);
}

.form-section:last-child {
    margin-bottom: 0;
}

/* Scrollbar personalizada */
.modal-body::-webkit-scrollbar {
    width: 6px;
}

.modal-body::-webkit-scrollbar-track {
    background: #F1F5F9;
    border-radius: 3px;
}

.modal-body::-webkit-scrollbar-thumb {
    background: #B0D5FF;
    border-radius: 3px;
}

.modal-body::-webkit-scrollbar-thumb:hover {
    background: #25608A;
}
</style>