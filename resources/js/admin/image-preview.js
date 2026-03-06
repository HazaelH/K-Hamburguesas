/**
 * Lógica para previsualizar imagen antes de subirla
 * Ubicación: public/js/image-preview.js
 */

document.addEventListener('DOMContentLoaded', function() {
    // Obtenemos los elementos por su ID
    const input = document.getElementById('imagen-input');
    const previewImg = document.getElementById('preview-img');
    const placeholder = document.getElementById('placeholder-text');

    // Verificamos que existan para evitar errores en otras páginas
    if (input && previewImg && placeholder) {
        
        input.addEventListener('change', function(e) {
            const file = e.target.files[0];
            
            if (file) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    // Asignamos la imagen cargada al src de la etiqueta img
                    previewImg.src = e.target.result;
                    
                    // Mostramos la imagen y ocultamos el texto de "Subir"
                    previewImg.classList.remove('hidden');
                    placeholder.classList.add('hidden');
                }
                
                // Leemos el archivo como URL de datos
                reader.readAsDataURL(file);
            }
        });
    }
});