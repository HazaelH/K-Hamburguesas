// resources/js/employee/caja.js

// 1. SISTEMA DE TOASTS (Notificaciones)
window.showToast = function(message, type = 'success') {
    const container = document.getElementById('toast-container');
    if(!container) return;

    const styles = {
        success: { bg: 'bg-emerald-600', icon: '<i class="fas fa-check-circle"></i>' },
        error:   { bg: 'bg-red-600',   icon: '<i class="fas fa-times-circle"></i>' },
    };
    const style = styles[type] || styles.success;

    const toast = document.createElement('div');
    toast.className = `${style.bg} text-white px-4 py-3 rounded-xl shadow-2xl flex items-center gap-3 toast-entry pointer-events-auto min-w-[300px] border border-white/10 backdrop-blur-md mb-2`;
    toast.innerHTML = `<span class="text-lg">${style.icon}</span><span class="font-bold text-sm">${message}</span>`;

    container.appendChild(toast);
    setTimeout(() => {
        toast.style.transition = 'all 0.5s ease-in';
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(100%)';
        setTimeout(() => toast.remove(), 500);
    }, 3000);
};

// 2. SISTEMA DE MODAL DE CONFIRMACIÓN
// Elementos del DOM
const modal = document.getElementById('custom-modal');
const modalTitle = document.getElementById('modal-title');
const modalDesc = document.getElementById('modal-desc');
const modalIconBox = document.getElementById('modal-icon-box');
const modalIcon = document.getElementById('modal-icon');
const btnConfirm = document.getElementById('btn-confirm-action');
const formAction = document.getElementById('form-modal-action');

window.openConfirmModal = function(type, orderId, total = 0) {
    // Configuración Base
    const baseUrl = '/empleado/caja'; // Ajusta si tu prefijo cambia
    
    if (type === 'pay') {
        // DISEÑO VERDE (COBRAR)
        modalTitle.innerText = `¿Cobrar Orden #${orderId}?`;
        modalDesc.innerHTML = `Confirmas que ingresaron <span class="text-emerald-400 font-bold text-lg">$${parseFloat(total).toFixed(2)}</span> a la caja.`;
        
        // Estilos Icono
        modalIconBox.className = "mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-emerald-900/30 sm:mx-0 sm:h-10 sm:w-10";
        modalIcon.className = "fas fa-hand-holding-dollar text-emerald-500 text-lg";
        
        // Estilos Botón
        btnConfirm.className = "inline-flex w-full justify-center rounded-lg bg-emerald-600 px-3 py-2 text-sm font-bold text-white shadow-sm hover:bg-emerald-500 sm:w-auto transition-colors flex items-center gap-2";
        btnConfirm.innerHTML = '<i class="fas fa-check"></i> Sí, Cobrar';
        
        // Ruta del Formulario
        formAction.action = `${baseUrl}/${orderId}/pagar`;

    } else {
        // DISEÑO ROJO (CANCELAR)
        modalTitle.innerText = `¿Cancelar Orden #${orderId}?`;
        modalDesc.innerHTML = `Esta acción <span class="text-red-400 font-bold">no se puede deshacer</span>. La orden quedará anulada.`;
        
        // Estilos Icono
        modalIconBox.className = "mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-900/30 sm:mx-0 sm:h-10 sm:w-10";
        modalIcon.className = "fas fa-exclamation-triangle text-red-500 text-lg";
        
        // Estilos Botón
        btnConfirm.className = "inline-flex w-full justify-center rounded-lg bg-red-600 px-3 py-2 text-sm font-bold text-white shadow-sm hover:bg-red-500 sm:w-auto transition-colors flex items-center gap-2";
        btnConfirm.innerHTML = '<i class="fas fa-trash-alt"></i> Sí, Cancelar';
        
        // Ruta del Formulario
        formAction.action = `${baseUrl}/${orderId}/cancelar`;
    }

    // Mostrar Modal
    modal.classList.remove('hidden');
    // Pequeño delay para permitir que la clase 'modal-visible' active la transición CSS
    setTimeout(() => {
        modal.classList.remove('modal-hidden');
        modal.classList.add('modal-visible');
    }, 10);
};

window.closeModal = function() {
    modal.classList.remove('modal-visible');
    modal.classList.add('modal-hidden');
    
    // Esperar a que termine la animación para ocultarlo del DOM
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
};