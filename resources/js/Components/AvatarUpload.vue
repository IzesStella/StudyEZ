<script setup>
import { ref } from 'vue'
import { usePage, useForm, router } from '@inertiajs/vue3'
import { toast } from 'vue3-toastify'
import 'vue3-toastify/dist/index.css'

const { props } = usePage()
const user = props.auth.user

const selectedFile = ref(null)
const previewUrl = ref(user.profile_photo ? `/storage/${user.profile_photo}` : '/images/default-avatar.svg')
const fileInput = ref(null)
const isUploading = ref(false)

// Form para upload da foto
const form = useForm({
    photo: null,
})

function selectFile() {
    fileInput.value.click()
}

function handleFileSelect(event) {
    const file = event.target.files[0]
    if (file) {
        // Validar tamanho do arquivo (2MB)
        if (file.size > 2 * 1024 * 1024) {
            toast.error('A imagem deve ter no máximo 2MB')
            return
        }

        // Validar tipo do arquivo
        if (!file.type.startsWith('image/')) {
            toast.error('Por favor, selecione apenas arquivos de imagem')
            return
        }

        selectedFile.value = file
        form.photo = file
        
        // Criar preview da imagem
        const reader = new FileReader()
        reader.onload = (e) => {
            previewUrl.value = e.target.result
        }
        reader.readAsDataURL(file)
    }
}

function removePhoto() {
    selectedFile.value = null
    form.photo = null
    previewUrl.value = user.profile_photo ? `/storage/${user.profile_photo}` : '/images/default-avatar.svg'
    if (fileInput.value) {
        fileInput.value.value = ''
    }
}

function savePhoto() {
    if (!selectedFile.value) {
        toast.error('Selecione uma foto antes de salvar')
        return
    }

    isUploading.value = true

    form.post(route('profile.avatar'), {
        onSuccess: () => {
            toast.success('Foto de perfil atualizada com sucesso!')
            selectedFile.value = null
            // Navegar para a página de perfil para mostrar a nova foto
            setTimeout(() => {
                router.visit(route('profile.show'))
            }, 1000)
        },
        onError: (errors) => {
            toast.error('Erro ao atualizar foto de perfil')
            console.error(errors)
        },
        onFinish: () => {
            isUploading.value = false
        }
    })
}

defineExpose({
    selectedFile,
    removePhoto,
    savePhoto
})
</script>

<template>
    <div class="avatar-upload">
        <h3>Foto de Perfil</h3>
        
        <div class="avatar-section">
            <div class="avatar-preview">
                <img :src="previewUrl" alt="Avatar Preview" />
                <div class="avatar-overlay" @click="selectFile">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
            </div>
            
            <div class="avatar-actions">
                <button type="button" class="upload-btn" @click="selectFile">
                    Escolher Foto
                </button>
                <button 
                    type="button" 
                    class="save-btn" 
                    @click="savePhoto" 
                    v-if="selectedFile"
                    :disabled="isUploading"
                >
                    <span v-if="isUploading">Salvando...</span>
                    <span v-else>Salvar Foto</span>
                </button>
                <button type="button" class="remove-btn" @click="removePhoto" v-if="selectedFile">
                    Remover
                </button>
            </div>
        </div>

        <input 
            ref="fileInput"
            type="file" 
            accept="image/*" 
            @change="handleFileSelect"
            style="display: none"
        />
        
        <p class="help-text">
            Formatos aceitos: JPG, PNG, GIF. Tamanho máximo: 2MB
        </p>
    </div>
</template>

<style scoped>
.avatar-upload h3 {
    margin: 0 0 16px 0;
    color: #135572;
    font-family: 'Montserrat', sans-serif;
    font-weight: 600;
    font-size: 1.1rem;
}

.avatar-section {
    display: flex;
    align-items: center;
    gap: 20px;
    margin-bottom: 12px;
}

.avatar-preview {
    position: relative;
    width: 80px;
    height: 80px;
    border-radius: 50%;
    overflow: hidden;
    cursor: pointer;
    border: 3px solid #B0D5FF;
    transition: all 0.3s ease;
}

.avatar-preview:hover {
    border-color: #25608A;
    transform: scale(1.05);
}

.avatar-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.avatar-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(19, 85, 114, 0.8);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.avatar-preview:hover .avatar-overlay {
    opacity: 1;
}

.avatar-overlay svg {
    width: 24px;
    height: 24px;
    color: white;
}

.avatar-actions {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.upload-btn, .save-btn, .remove-btn {
    padding: 8px 16px;
    border: none;
    border-radius: 8px;
    font-family: 'Montserrat', sans-serif;
    font-weight: 600;
    font-size: 0.9rem;
    cursor: pointer;
    transition: all 0.3s ease;
}

.upload-btn {
    background: #FF9500;
    color: white;
}

.upload-btn:hover {
    background: #e68900;
    transform: translateY(-1px);
}

.save-btn {
    background: #135572;
    color: white;
}

.save-btn:hover:not(:disabled) {
    background: #25608A;
    transform: translateY(-1px);
}

.save-btn:disabled {
    background: #94a3b8;
    cursor: not-allowed;
    transform: none;
}

.remove-btn {
    background: #ef4444;
    color: white;
}

.remove-btn:hover {
    background: #dc2626;
    transform: translateY(-1px);
}

.help-text {
    font-size: 0.8rem;
    color: #6b7280;
    margin: 0;
    font-family: 'Montserrat', sans-serif;
}
</style>