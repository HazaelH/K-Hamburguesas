let orderIdToDelete = null;
let isSyncing = false;
// Aumentamos a 5 segundos (5000 ms) para reducir la carga del servidor
const SYNC_INTERVAL_MS = 5000; 

document.addEventListener('DOMContentLoaded', () => {
    
    setInterval(() => {
        if(!isSyncing) window.sincronizarComandas();
    }, SYNC_INTERVAL_MS);

    const confirmBtn = document.getElementById('btn-confirm-delete');
    if(confirmBtn) {
        confirmBtn.addEventListener('click', async () => {
            if(!orderIdToDelete) return;

            const originalText = confirmBtn.innerHTML;
            confirmBtn.innerHTML = `<i class="fas fa-spinner fa-spin"></i> ${window.KITCHEN_LANG.requesting}`;
            confirmBtn.disabled = true;

            try {
                const token = document.querySelector('meta[name="csrf-token"]').content;
                const response = await fetch(`/empleado/orden/${orderIdToDelete}/solicitar-cancelacion`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': token }
                });

                if (response.ok) {
                    window.closeModal();
                    window.showToast(window.KITCHEN_LANG.request_sent, 'info');
                    
                    const card = document.getElementById(`order-card-${orderIdToDelete}`);
                    if(card) {
                        card.classList.add('opacity-50', 'animate-pulse');
                        const btn = card.querySelector('button.bg-red-600');
                        if(btn) {
                            btn.innerHTML = `<i class="fas fa-clock"></i> ${window.KITCHEN_LANG.waiting_admin}`;
                            btn.classList.replace('bg-red-600', 'bg-slate-600');
                            btn.disabled = true;
                        }
                    }
                } else {
                    const data = await response.json();
                    window.showToast(data.message || 'Error', 'error');
                }
            } catch (e) {
                window.showToast(window.KITCHEN_LANG.conn_error, 'error');
            } finally {
                confirmBtn.innerHTML = originalText;
                confirmBtn.disabled = false;
            }
        });
    }   
});

window.sincronizarComandas = async function() {
    if (isSyncing) return;
    isSyncing = true;
    try {
        const response = await fetch(window.location.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        const html = await response.text();
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        
        const newGrid = doc.getElementById('orders-grid-container');
        const currentGrid = document.getElementById('orders-grid-container');
        
        if (newGrid && currentGrid) {
            // SOLUCIÓN DE PARPADEO: Solo reemplaza el HTML si algo realmente cambió
            if (currentGrid.innerHTML !== newGrid.innerHTML) {
                const newCount = newGrid.querySelectorAll('[id^="order-card-"]').length;
                const currentCount = currentGrid.querySelectorAll('[id^="order-card-"]').length;
                
                // Si hay MÁS órdenes que antes, hace ruido
                if (newCount > currentCount) playNotificationSound();
                
                // Actualiza visualmente de forma silenciosa
                currentGrid.innerHTML = newGrid.innerHTML;
            }
        }
    } catch (error) {
        console.error("Sync error:", error);
    } finally {
        isSyncing = false;
    }
};

window.actualizarEstado = async function(orderId, nuevoEstado) {
    const card = document.getElementById(`order-card-${orderId}`);
    if (nuevoEstado !== 'cancelado') {
        const btn = card ? card.querySelector('button') : null;
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        }
    }
    try {
        const token = document.querySelector('meta[name="csrf-token"]').content;
        const response = await fetch(`/empleado/orden/${orderId}/estado`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': token },
            body: JSON.stringify({ status: nuevoEstado })
        });
        const data = await response.json();
        if (response.ok && data.success) {
            window.showToast(window.KITCHEN_LANG.status_updated, 'success');
            window.sincronizarComandas();
        } else throw new Error(data.message);
    } catch (error) {
        window.showToast(window.KITCHEN_LANG.conn_error, 'error');
    }
};

window.cancelarOrden = function(orderId) {
    orderIdToDelete = orderId;
    const descContainer = document.getElementById('modal-desc-container');
    if(descContainer && window.KITCHEN_LANG) {
        descContainer.innerHTML = window.KITCHEN_LANG.cancel_desc.replace(':id', orderId);
    }

    const modal = document.getElementById('confirmation-modal');
    const backdrop = document.getElementById('modal-backdrop');
    const panel = document.getElementById('modal-panel');
    modal.classList.remove('hidden');
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
    backdrop.classList.add('opacity-0');
    panel.classList.remove('scale-100');
    panel.classList.add('opacity-0', 'scale-95');
    setTimeout(() => { modal.classList.add('hidden'); orderIdToDelete = null; }, 300);
};

window.showToast = function(message, type = 'success') {
    const container = document.getElementById('toast-container');
    if(!container) return; 
    const styles = {
        success: { bg: 'bg-emerald-500/95', border: 'border-emerald-400', icon: '<i class="fas fa-check-circle"></i>' },
        error:   { bg: 'bg-red-500/95',     border: 'border-red-400',     icon: '<i class="fas fa-exclamation-circle"></i>' },
        info:    { bg: 'bg-blue-500/95',    border: 'border-blue-400',    icon: '<i class="fas fa-info-circle"></i>' }
    };
    const style = styles[type] || styles.success;
    const toast = document.createElement('div');
    toast.className = `${style.bg} ${style.border} text-white px-5 py-4 rounded-xl shadow-2xl flex items-center gap-3 transform translate-x-full transition-all duration-300 pointer-events-auto border backdrop-blur-md mb-2`;
    toast.innerHTML = `<span class="text-2xl">${style.icon}</span><span class="font-bold text-sm tracking-wide">${message}</span>`;
    container.appendChild(toast);
    requestAnimationFrame(() => toast.classList.remove('translate-x-full'));
    setTimeout(() => { toast.classList.add('translate-x-full'); setTimeout(() => toast.remove(), 300); }, 3000);
};

function playNotificationSound() {
    try {
        const audio = new Audio('https://actions.google.com/sounds/v1/cartoon/clown_horn.ogg'); 
        audio.volume = 0.5; audio.play().catch(e => {});
    } catch (e) {}
}