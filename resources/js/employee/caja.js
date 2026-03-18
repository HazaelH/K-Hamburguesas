// resources/js/employee/caja.js

// --- CANDADO MAESTRO ANTI-SPAM ---
let isProcessingAction = false;

// 1. SISTEMA DE TOASTS
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
window.openConfirmModal = function(type, orderId, total = 0) {
    // 2.1 QUITAR CANDADO AL ABRIR
    isProcessingAction = false;
    
    const modal = document.getElementById('custom-modal');
    const modalTitle = document.getElementById('modal-title');
    const modalDesc = document.getElementById('modal-desc');
    const modalIconBox = document.getElementById('modal-icon-box');
    const modalIcon = document.getElementById('modal-icon');
    const btnConfirm = document.getElementById('btn-confirm-action');
    const formAction = document.getElementById('form-modal-action');
    
    // Elementos del motivo de cancelación
    const reasonContainer = document.getElementById('modal-reason-container');
    const reasonInput = document.getElementById('motivo_cancelacion');
    
    if(!modal) return; 

    // Restaurar botón a su estado normal (por si se cerró antes a la mitad)
    btnConfirm.disabled = false;
    btnConfirm.style.pointerEvents = 'auto';
    btnConfirm.classList.remove('opacity-75', 'cursor-not-allowed');

    // 2.2 REINICIAR CAJA DE MOTIVO (Oculta por defecto)
    if (reasonContainer && reasonInput) {
        reasonContainer.classList.add('hidden');
        reasonInput.required = false;
        reasonInput.value = '';
    }

    const baseUrl = window.CAJA_LANG.baseUrl || '/empleado/caja';
    
    if (type === 'pay') {
        let title = window.CAJA_LANG.charge_title.replace(':id', orderId);
        let desc = window.CAJA_LANG.charge_desc.replace(':total', parseFloat(total).toFixed(2));
        
        modalTitle.innerText = title;
        modalDesc.innerHTML = desc;
        
        modalIconBox.className = "mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-emerald-900/30 sm:mx-0 sm:h-10 sm:w-10";
        modalIcon.className = "fas fa-hand-holding-dollar text-emerald-500 text-lg";
        
        btnConfirm.className = "inline-flex w-full justify-center rounded-lg bg-emerald-600 px-3 py-2 text-sm font-bold text-white shadow-sm hover:bg-emerald-500 sm:w-auto transition-colors flex items-center gap-2";
        btnConfirm.innerHTML = `<i class="fas fa-check"></i> ${window.CAJA_LANG.charge_btn}`;
        
        formAction.action = `${baseUrl}/${orderId}/pagar`;

    } else {
        // En lugar de Cancelar, es Solicitar Cancelación
        let title = window.CAJA_LANG.cancel_title ? window.CAJA_LANG.cancel_title.replace(':id', orderId) : `Solicitar Cancelación #${orderId}`;
        let desc = window.CAJA_LANG.cancel_desc || 'Esta orden se pondrá en pausa hasta que un gerente la autorice.'; 
        
        modalTitle.innerText = title;
        modalDesc.innerHTML = desc;
        
        modalIconBox.className = "mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-900/30 sm:mx-0 sm:h-10 sm:w-10";
        modalIcon.className = "fas fa-hand-paper text-red-500 text-lg";
        
        btnConfirm.className = "inline-flex w-full justify-center rounded-lg bg-red-600 px-3 py-2 text-sm font-bold text-white shadow-sm hover:bg-red-500 sm:w-auto transition-colors flex items-center gap-2";
        btnConfirm.innerHTML = `<i class="fas fa-paper-plane"></i> ${window.CAJA_LANG.cancel_btn || 'Enviar Solicitud'}`;
        
        formAction.action = `${baseUrl}/${orderId}/cancelar`;

        // 2.3 MOSTRAR CAJA DE MOTIVO AL CANCELAR
        if (reasonContainer && reasonInput) {
            reasonContainer.classList.remove('hidden');
            reasonInput.required = true; // Hacemos que sea obligatorio escribir el porqué
        }
    }

    modal.classList.remove('hidden');
    setTimeout(() => {
        modal.classList.remove('modal-hidden');
        modal.classList.add('modal-visible');
    }, 10);
};

window.closeModal = function() {
    const modal = document.getElementById('custom-modal');
    if(!modal) return;

    modal.classList.remove('modal-visible');
    modal.classList.add('modal-hidden');
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
};

// =========================================================
// 3. BLINDAJE DEL FORMULARIO (ANTI-DOBLE CLIC)
// =========================================================
document.addEventListener('DOMContentLoaded', () => {
    const formAction = document.getElementById('form-modal-action');
    
    if (formAction) {
        formAction.addEventListener('submit', function(e) {
            // Si el candado está activo, bloqueamos cualquier intento
            if (isProcessingAction) {
                e.preventDefault();
                return;
            }

            // Validamos que el empleado haya escrito el motivo si es cancelación
            if (this.checkValidity()) {
                isProcessingAction = true; // CERRAMOS EL CANDADO
                
                const btn = document.getElementById('btn-confirm-action');
                if (btn) {
                    btn.disabled = true;
                    btn.style.pointerEvents = 'none';
                    btn.classList.add('opacity-75', 'cursor-not-allowed');
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';
                }
            }
        });
    }
});