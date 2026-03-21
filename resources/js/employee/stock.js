window.solicitarCambio = async function(id) {
    const btn = event.currentTarget;
    const originalContent = btn.innerHTML;
    
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    btn.disabled = true;

    try {
        const token = document.querySelector('meta[name="csrf-token"]').content;
        const response = await fetch(`/empleado/stock/${id}/solicitar-cambio`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': token }
        });

        const data = await response.json();

        if (response.ok && data.success) {
            window.showToast(data.message, 'success');
            setTimeout(() => window.location.reload(), 1500); 
        } else {
            window.showToast(data.message || 'Error', 'error');
            btn.innerHTML = originalContent;
            btn.disabled = false;
        }
    } catch (error) {
        window.showToast(window.STOCK_LANG.conn_error, 'error');
        btn.innerHTML = originalContent;
        btn.disabled = false;
    }
};

window.showToast = function(message, type = 'success') {
    const container = document.getElementById('toast-container');
    if(!container) return;

    const colors = type === 'success' ? 'bg-emerald-600' : 'bg-red-600';
    const icon = type === 'success' ? '<i class="fas fa-check"></i>' : '<i class="fas fa-ban"></i>';

    const toast = document.createElement('div');
    toast.className = `${colors} text-white px-5 py-4 rounded-xl shadow-2xl flex items-center gap-3 transform translate-x-full transition-all duration-300 pointer-events-auto border border-white/10 backdrop-blur-md mb-2`;
    toast.innerHTML = `<span class="text-xl">${icon}</span><span class="font-bold text-sm tracking-wide">${message}</span>`;

    container.appendChild(toast);
    
    requestAnimationFrame(() => toast.classList.remove('translate-x-full'));
    setTimeout(() => {
        toast.classList.add('translate-x-full');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
};

document.addEventListener('DOMContentLoaded', () => {
    const buscador = document.getElementById('buscador');
    const noResults = document.getElementById('no-results');
    
    if (buscador) {
        buscador.addEventListener('input', (e) => {
            const texto = e.target.value.toLowerCase();
            const productos = document.querySelectorAll('.product-card');
            let visibles = 0;

            productos.forEach(prod => {
                const nombre = prod.dataset.nombre;
                if (nombre.includes(texto)) {
                    prod.style.display = 'block';
                    visibles++;
                } else {
                    prod.style.display = 'none';
                }
            });

            if (visibles === 0) {
                if(noResults) noResults.style.display = 'flex';
            } else {
                if(noResults) noResults.style.display = 'none';
            }
        });
    }
});