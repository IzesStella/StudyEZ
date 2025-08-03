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
            
            <div class="modal-body column-layout">
                <!-- Foto de Perfil -->
                <div class="profile-section profile-photo">
                    <AvatarUpload ref="avatarUploadRef" />

                </div>
                <!-- Informações de Perfil -->
                <div class="profile-section profile-info">

                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                        @success="handleFormSuccess"
                    />
                </div>
                <!-- Alterar Senha -->
                <div class="profile-section profile-password">
                    <UpdatePasswordForm @success="handleFormSuccess" />
                </div>
                <!-- Remover Conta -->
                <div class="remove-account-row">

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
    flex-direction: row;
    justify-content: center;
    align-items: center;
    padding: 24px 28px;
    background: #b0d5ff;
    color: white;
    position: relative;
}

.modal-header h2 {
    margin: 0 auto;
    font-family: 'Montserrat', sans-serif;
    font-weight: 700;
    font-size: 1.5rem;
    color: #002F66;
    text-align: center;
    flex: 1;
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
    position: absolute;
    right: 28px;
    top: 24px;
}

.close-btn:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: rotate(90deg);
}



.modal-body.column-layout {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 28px 0 0 0;
    max-height: calc(85vh - 120px);
    overflow-y: auto;
    gap: 18px;
}
.profile-section {
    width: 100%;
    max-width: 370px;
    margin-bottom: 0;
    background: none;
    border: none;
    border-radius: 0;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    padding: 0 0 0 0;
}
.profile-section h3 {
    font-size: 1.15rem;
    font-weight: 700;
    margin-bottom: 8px;
    color: #222;
}
.profile-photo {
    align-items: center;
    justify-content: flex-start;
    margin-bottom: 10px;
}
.avatar-actions {
    display: flex;
    flex-direction: column;
    gap: 6px;
    margin-top: 10px;
    align-items: flex-end;
    width: 100%;
}
.btn-alt, .btn-save {
    background: #93c5fd;
    color: #1e293b;
    border: none;
    border-radius: 6px;
    padding: 4px 14px;
    margin-bottom: 2px;
    cursor: pointer;
    width: 90px;
    text-align: center;
}
.btn-save {
    background: #60a5fa;
}
.remove-account-row {
    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: center;
    margin-top: 24px;
    gap: 12px;
    width: 100%;
}
.remove-account-row span {
    font-size: 1rem;
    font-weight: 500;
    color: #111;
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