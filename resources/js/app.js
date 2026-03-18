import './bootstrap';

// =========================================================
// PROTECCIÓN GLOBAL ANTI-DOBLE CLIC PARA TODOS LOS FORMULARIOS
// =========================================================
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            
            if (submitBtn) {
                // Si ya se envió, detenemos el segundo envío
                if (submitBtn.classList.contains('is-submitting')) {
                    e.preventDefault();
                    return;
                }
                
                // Marcamos como enviando y cambiamos el estado
                submitBtn.classList.add('is-submitting');
                
                // Opcional: Prevenir clics repetidos visualmente
                submitBtn.style.pointerEvents = 'none';
                submitBtn.style.opacity = '0.7';

                // Si quieres que el texto cambie a "Procesando...", puedes agregar algo como:
                // submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';
            }
        });
    });
});