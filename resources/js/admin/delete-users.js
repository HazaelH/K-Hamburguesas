// Variable de control para el formulario actual
let formIdToSubmit = null;

// ==========================================
// 1. SISTEMA DEL MODAL DE BORRADO
// ==========================================
window.confirmarBorrado = function(formId, tipo) {
    formIdToSubmit = formId;
    const modal = document.getElementById('delete-modal');
    const panel = document.getElementById('delete-modal-panel');
    const iconContainer = document.getElementById('modal-icon-container');
    const icon = document.getElementById('modal-icon');
    const title = document.getElementById('modal-title');
    const message = document.getElementById('modal-message');
    const btnConfirm = document.getElementById('modal-confirm-btn');

    if (tipo === 'soft') {
        iconContainer.className = 'mx-auto flex h-16 w-16 items-center justify-center rounded-full mb-4 bg-orange-500/20 border border-orange-500/50';
        icon.className = 'fas fa-user-minus text-orange-500 text-2xl';
        title.innerText = 'Dar de baja al usuario';
        message.innerText = 'El usuario perderá acceso al sistema inmediatamente. Podrás recuperarlo más adelante desde la papelera de bajas.';
        btnConfirm.className = 'flex-1 bg-orange-600 hover:bg-orange-500 text-white font-bold py-3 rounded-xl shadow-lg shadow-orange-900/50 transition-all';
        btnConfirm.innerHTML = '<i class="fas fa-power-off"></i> Desactivar';
    } else {
        iconContainer.className = 'mx-auto flex h-16 w-16 items-center justify-center rounded-full mb-4 bg-red-500/20 border border-red-500/50';
        icon.className = 'fas fa-skull text-red-500 text-2xl';
        title.innerText = '¡Eliminación Definitiva!';
        message.innerText = 'Esta acción borrará al usuario para siempre. Podría afectar a reportes históricos. ¿Realmente deseas continuar?';
        btnConfirm.className = 'flex-1 bg-red-600 hover:bg-red-500 text-white font-bold py-3 rounded-xl shadow-lg shadow-red-900/50 transition-all';
        btnConfirm.innerHTML = '<i class="fas fa-trash"></i> Eliminar';
    }

    modal.classList.remove('hidden');
    setTimeout(() => {
        modal.classList.remove('opacity-0');
        panel.classList.remove('scale-95');
    }, 10);
};

window.cerrarModalBorrado = function() {
    const modal = document.getElementById('delete-modal');
    const panel = document.getElementById('delete-modal-panel');
    formIdToSubmit = null;
    
    modal.classList.add('opacity-0');
    panel.classList.add('scale-95');
    
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300); 
};

window.ejecutarBorrado = function() {
    if (formIdToSubmit) {
        // Ejecución corregida sin comillas
        document.getElementById(formIdToSubmit).submit();
    }
};

// ==========================================
// 2. SISTEMA DE NOTIFICACIONES TOAST
// ==========================================
window.lanzarToast = function(tipo, mensaje) {
    const container = document.getElementById('toast-container');
    if (!container) return;

    const toast = document.createElement('div');
    
    let bgColor = tipo === 'success' ? 'bg-emerald-900 border-emerald-600' : 'bg-red-900 border-red-600';
    let icon = tipo === 'success' ? '<i class="fas fa-check-circle text-emerald-400 text-2xl"></i>' : '<i class="fas fa-exclamation-circle text-red-400 text-2xl"></i>';
    let titulo = tipo === 'success' ? 'Operación Exitosa' : '¡Atención!';

    toast.className = `flex items-center gap-4 px-6 py-4 rounded-xl shadow-[0_10px_40px_-10px_rgba(0,0,0,0.5)] border text-white transform transition-all duration-500 translate-y-20 opacity-0 pointer-events-auto ${bgColor}`;
    
    toast.innerHTML = `
        ${icon} 
        <div>
            <h4 class="font-bold text-sm tracking-wide">${titulo}</h4>
            <p class="text-xs text-slate-300 mt-0.5">${mensaje}</p>
        </div>
    `;
    
    container.appendChild(toast);
    
    setTimeout(() => toast.classList.remove('translate-y-20', 'opacity-0'), 10);
    
    setTimeout(() => {
        toast.classList.add('translate-y-20', 'opacity-0');
        setTimeout(() => toast.remove(), 500); 
    }, 4000);
};

// ==========================================
// 3. INICIALIZADOR AUTOMÁTICO
// ==========================================
document.addEventListener('DOMContentLoaded', () => {
    // Verificamos si Blade nos inyectó mensajes desde el backend
    if (window.K_ADMIN_MESSAGES) {
        if (window.K_ADMIN_MESSAGES.success) {
            window.lanzarToast('success', window.K_ADMIN_MESSAGES.success);
        }
        if (window.K_ADMIN_MESSAGES.error) {
            window.lanzarToast('error', window.K_ADMIN_MESSAGES.error);
        }
    }
});