<script setup>
import { ref } from 'vue'
import { usePage } from '@inertiajs/vue3'

const { props } = usePage()
const user = props.auth.user

const selectedFile = ref(null)
const previewUrl = ref(user.avatar || '/images/default-avatar.png')
const fileInput = ref(null)

function selectFile() {
    fileInput.value.click()
}

function handleFileSelect(event) {
    const file = event.target.files[0]
    if (file) {
        selectedFile.value = file
        
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
    previewUrl.value = '/images/default-avatar.png'
    if (fileInput.value) {
        fileInput.value.value = ''
    }
}

defineExpose({
    selectedFile,
    removePhoto
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

.upload-btn, .remove-btn {
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