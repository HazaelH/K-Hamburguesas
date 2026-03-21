document.addEventListener('DOMContentLoaded', () => {
    
    const fileInput = document.getElementById('foto_custom');
    const previewImg = document.getElementById('preview-avatar'); // La imagen grande
    const iconAvatar = document.getElementById('icon-avatar');    // El icono de cámara
    const radioButtons = document.querySelectorAll('input[name="avatar_option"]');

    // Función para mostrar una imagen en el círculo grande
    function setMainPreview(src) {
        if(previewImg) {
            previewImg.src = src;
            previewImg.classList.remove('hidden');
        }
        if(iconAvatar) iconAvatar.classList.add('hidden');
    }

    // 1. Si suben foto personalizada
    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    setMainPreview(e.target.result);
                    
                    // Desmarcar visualmente los radio buttons
                    radioButtons.forEach(el => {
                        el.checked = false;
                        const img = el.parentElement.querySelector('img');
                        img.classList.remove('border-orange-500', 'scale-110', 'grayscale-0');
                        img.classList.add('grayscale');
                        // Ocultar check
                        const check = el.parentElement.querySelector('div.absolute');
                        if(check) check.classList.add('hidden');
                        if(check) check.classList.remove('flex');
                    });
                }
                reader.readAsDataURL(file);
            }
        });
    }

    // 2. Si seleccionan avatar predeterminado (LIVE PREVIEW)
    radioButtons.forEach(radio => {
        radio.addEventListener('change', (e) => {
             // Limpiar input file para evitar conflictos
             if(fileInput) fileInput.value = ''; 
             
             // Obtener la URL de la imagen pequeña que se clickeó
             const selectedImgSrc = e.target.parentElement.querySelector('img').src;
             
             // Poner esa imagen en el círculo grande
             setMainPreview(selectedImgSrc);
        });
    });
});