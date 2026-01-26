// Variables globales
let lastOrderCount = 0;
let orderIdToDelete = null; // Guardamos aquí el ID temporalmente

document.addEventListener('DOMContentLoaded', () => {
    
    // --- 1. TEMPORIZADOR Y DETECCIÓN ---
    let timeLeft = 30;
    const timerEl = document.getElementById('timer');
    const currentOrders = document.querySelectorAll('[id^="order-card-"]').length;
    const savedCount = localStorage.getItem('lastOrderCount');
    
    if (savedCount && currentOrders > parseInt(savedCount)) {
        playNotificationSound();
    }
    localStorage.setItem('lastOrderCount', currentOrders);

    if(timerEl) {
        setInterval(() => {
            timeLeft--;
            timerEl.innerText = timeLeft;
            if (timeLeft <= 0) window.location.reload();
        }, 1000);
    }

    // Listener para el botón de confirmar borrado (dentro del modal)
    const confirmBtn = document.getElementById('btn-confirm-delete');
    if(confirmBtn) {
        confirmBtn.addEventListener('click', async () => {
            if(orderIdToDelete) {
                // Cerramos modal primero para que se sienta rápido
                closeModal();
                // Ejecutamos la acción real
                await actualizarEstado(orderIdToDelete, 'cancelado');
                orderIdToDelete = null; // Limpiamos variable
            }
        });
    }
});

// --- 2. GESTIÓN DEL MODAL (Nuevo) ---

window.cancelarOrden = function(orderId) {
    orderIdToDelete = orderId; // Guardamos el ID
    
    // Actualizamos el texto del modal
    document.getElementById('modal-order-id').innerText = `Orden #${orderId}`;
    
    // Mostramos el modal
    const modal = document.getElementById('confirmation-modal');
    const backdrop = document.getElementById('modal-backdrop');
    const panel = document.getElementById('modal-panel');

    modal.classList.remove('hidden');
    
    // Pequeño timeout para permitir que CSS detecte el cambio de display:none a block y anime la opacidad
    setTimeout(() => {
        backdrop.classList.remove('opacity-0');
        panel.classList.remove('opacity-0', 'scale-95');
        panel.classList.add('scale-100');
    }, 10);
};

window.closeModal = function() {
    const modal = document.getElementById('confirmation-modal');
    const backdrop = document.getElementById('modal-backdrop');
    const panel = document.getElementById('modal-panel');

    // Animación de salida
    backdrop.classList.add('opacity-0');
    panel.classList.remove('scale-100');
    panel.classList.add('opacity-0', 'scale-95');

    // Esperamos a que termine la animación (300ms) para ocultarlo
    setTimeout(() => {
        modal.classList.add('hidden');
        orderIdToDelete = null;
    }, 300);
};


// --- 3. SONIDO Y TOASTS ---
function playNotificationSound() {
    try {
        const audio = new Audio('https://actions.google.com/sounds/v1/cartoon/clown_horn.ogg'); 
        audio.volume = 0.5;
        audio.play().catch(e => console.log("Audio bloqueado"));
        showToast('¡Nueva orden recibida!', 'info');
    } catch (e) { console.error(e); }
}

window.showToast = function(message, type = 'success') {
    const container = document.getElementById('toast-container');
    if(!container) return; 

    const styles = {
        success: { bg: 'bg-green-600', icon: '<i class="fas fa-check-circle"></i>' },
        error:   { bg: 'bg-red-600',   icon: '<i class="fas fa-bomb"></i>' },
        info:    { bg: 'bg-blue-600',  icon: '<i class="fas fa-info-circle"></i>' }
    };

    const style = styles[type] || styles.success;
    const toast = document.createElement('div');
    toast.className = `${style.bg} text-white px-6 py-4 rounded-lg shadow-2xl flex items-center gap-3 transform translate-x-full transition-all duration-500 ease-out min-w-[300px] border border-white/10 backdrop-blur-sm pointer-events-auto`;
    toast.innerHTML = `<span class="text-2xl">${style.icon}</span><div><h4 class="font-bold text-sm uppercase tracking-wide opacity-90">${type === 'error' ? 'Error' : 'Aviso'}</h4><span class="font-medium text-sm">${message}</span></div>`;

    container.appendChild(toast);
    requestAnimationFrame(() => toast.classList.remove('translate-x-full'));
    setTimeout(() => {
        toast.classList.add('translate-x-full', 'opacity-0');
        setTimeout(() => toast.remove(), 500);
    }, 4000);
};

// --- 4. ACTUALIZAR ESTADO (Backend) ---
window.actualizarEstado = async function(orderId, nuevoEstado) {
    const card = document.getElementById(`order-card-${orderId}`);
    
    // Si no es cancelar (es decir, es cocinar o terminar), mostramos loading en el botón de la tarjeta
    // Si es cancelar, el modal ya se cerró, así que mostramos un toast de carga
    if (nuevoEstado !== 'cancelado') {
        const btn = card ? card.querySelector('button') : null;
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Procesando...';
            btn.classList.add('opacity-75', 'cursor-not-allowed');
        }
    } else {
        showToast('Cancelando orden...', 'info');
    }

    try {
        const tokenMeta = document.querySelector('meta[name="csrf-token"]');
        if (!tokenMeta) throw new Error("Falta token CSRF");
        const token = tokenMeta.getAttribute('content');

        const response = await fetch(`/empleado/orden/${orderId}/estado`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': token },
            body: JSON.stringify({ status: nuevoEstado })
        });

        const data = await response.json();

        if (response.ok && data.success) {
            if(nuevoEstado === 'cancelado') {
                showToast(`Orden #${orderId} eliminada`, 'success');
                // Efecto visual de eliminación
                if(card) {
                    card.style.transition = 'all 0.5s ease';
                    card.style.transform = 'scale(0.9) translateX(-100%)';
                    card.style.opacity = '0';
                    setTimeout(() => window.location.reload(), 600);
                } else {
                    setTimeout(() => window.location.reload(), 1000);
                }
            } else {
                showToast(`Orden #${orderId} actualizada`, 'success');
                setTimeout(() => window.location.reload(), 800);
            }
        } else {
            throw new Error(data.message || 'Error del servidor');
        }

    } catch (error) {
        console.error(error);
        showToast(error.message || 'Error de conexión', 'error');
        // Si falló, recargamos para asegurar estado consistente
        setTimeout(() => window.location.reload(), 2000);
    }
};