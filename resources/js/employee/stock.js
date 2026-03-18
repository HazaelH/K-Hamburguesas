window.solicitarCambio = async function(id) {
    const btn = event.currentTarget;
    const originalContent = btn.innerHTML;
    
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    btn.disabled = true;

    try {
        const token = document.querySelector('meta[name="csrf-token"]').content;
        const baseUrl = window.STOCK_LANG.baseUrl || '/empleado/stock';
        const urlPeticion = `${baseUrl}/${id}/solicitar-cambio`;

        const response = await fetch(urlPeticion, {
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

// --- SISTEMA DE BÚSQUEDA Y FILTROS AVANZADOS ---
let currentFilter = 'all';

// Función para cuando el usuario hace clic en los "Chips"
window.setFilter = function(filter, btnElement) {
    currentFilter = filter;

    // 1. Reiniciar los estilos de todos los botones
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.classList.remove('bg-emerald-600', 'text-white', 'shadow-lg', 'shadow-emerald-500/30', 'border-emerald-500');
        btn.classList.add('bg-slate-800', 'text-slate-300', 'border-slate-600');
    });
    
    // 2. Encender el botón seleccionado
    btnElement.classList.remove('bg-slate-800', 'text-slate-300', 'border-slate-600');
    btnElement.classList.add('bg-emerald-600', 'text-white', 'shadow-lg', 'shadow-emerald-500/30', 'border-emerald-500');

    // 3. Ejecutar el motor de búsqueda
    aplicarFiltros();
};

// El motor de búsqueda unificado
function aplicarFiltros() {
    const texto = document.getElementById('buscador') ? document.getElementById('buscador').value.toLowerCase() : '';
    const productos = document.querySelectorAll('.product-card');
    const noResults = document.getElementById('no-results');
    let visibles = 0;

    // A. Filtrar las tarjetas
    productos.forEach(prod => {
        const nombre = prod.dataset.nombre;
        const status = prod.dataset.status;
        const pending = prod.dataset.pending;

        let coincideTexto = nombre.includes(texto);
        let coincideChip = true;

        if (currentFilter === 'active' && status !== 'active') coincideChip = false;
        if (currentFilter === 'inactive' && status !== 'inactive') coincideChip = false;
        if (currentFilter === 'pending' && pending !== 'true') coincideChip = false;

        if (coincideTexto && coincideChip) {
            prod.style.display = 'block';
            visibles++;
        } else {
            prod.style.display = 'none';
        }
    });

    // B. Mostrar/Ocultar "No hay resultados"
    if (noResults) {
        noResults.style.display = visibles === 0 ? 'flex' : 'none';
    }

    // C. Ocultar el título de la categoría si todos sus productos están ocultos
    document.querySelectorAll('.category-header').forEach(header => {
        let actual = header.nextElementSibling;
        let tieneProductosVisibles = false;
        
        // Revisamos los elementos que siguen al título hasta encontrar el siguiente título
        while(actual && actual.classList.contains('product-card')) {
            if(actual.style.display === 'block') {
                tieneProductosVisibles = true;
                break;
            }
            actual = actual.nextElementSibling;
        }
        
        header.style.display = tieneProductosVisibles ? 'flex' : 'none';
    });
}

// Enganchar el buscador de texto al motor unificado
document.addEventListener('DOMContentLoaded', () => {
    const buscador = document.getElementById('buscador');
    if (buscador) {
        buscador.addEventListener('input', aplicarFiltros);
    }
});