// resources/js/profile.js

document.addEventListener('DOMContentLoaded', () => {
    
    // Elementos del DOM
    const avatarPreview = document.getElementById('avatar-preview');
    const uploadInput = document.getElementById('avatar-upload-input');
    const radioInputs = document.querySelectorAll('input[name="avatar_preset"]');

    // Función global para cuando seleccionan un "monito" (preset)
    window.previewPreset = function(url) {
        if(avatarPreview) {
            avatarPreview.src = url;
        }
        // Limpiamos el input de archivo para evitar conflictos en el backend
        if(uploadInput) {
            uploadInput.value = ''; 
        }
    };

    // Función global para cuando suben una foto propia
    window.previewUpload = function(event) {
        const file = event.target.files[0];
        if (file) {
            // 1. Desmarcamos visualmente los radio buttons de presets
            radioInputs.forEach(input => input.checked = false);
            
            // 2. Creamos una URL temporal para mostrar la imagen
            if(avatarPreview) {
                avatarPreview.src = URL.createObjectURL(file);
            }
        }
    };
});