window.toggleStock = async function(id) {
    try {
        const tokenMeta = document.querySelector('meta[name="csrf-token"]');
        const token = tokenMeta ? tokenMeta.getAttribute('content') : '';

        // Hacemos la petición
        const response = await fetch(`/empleado/stock/${id}/toggle`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token
            }
        });

        const data = await response.json();

        if (data.success) {
            // AQUÍ LLAMAMOS A LA FUNCIÓN QUE ACTUALIZA EL COLOR
            updateProductUI(id, data.new_status); // Asegúrate de que el controlador devuelva 'new_status' o 'is_active'
            showToast(data.message, data.new_status ? 'success' : 'error');
        } else {
            showToast('Error al actualizar', 'error');
        }

    } catch (error) {
        console.error(error);
        showToast('Error de conexión', 'error');
    }
};

function updateProductUI(id, isActive) {
    // 1. Seleccionamos los elementos
    const statusText = document.getElementById(`status-text-${id}`);
    const dot = document.getElementById(`dot-${id}`);
    
    // 2. Actualizamos el TEXTO y el COLOR de fondo/texto
    if (statusText) {
        if(isActive) {
            statusText.innerText = 'DISPONIBLE';
            // Sobrescribimos className completo para borrar lo rojo
            statusText.className = 'text-[10px] font-bold px-2 py-0.5 rounded-md transition-colors duration-300 bg-emerald-500/10 text-emerald-400';
        } else {
            statusText.innerText = 'AGOTADO';
            // Sobrescribimos className completo para poner lo rojo
            statusText.className = 'text-[10px] font-bold px-2 py-0.5 rounded-md transition-colors duration-300 bg-red-500/10 text-red-400';
        }
    }

    // 3. Actualizamos el PUNTO (Dot)
    if(dot) {
        if(isActive) {
            dot.classList.remove('bg-red-500');
            dot.classList.add('bg-emerald-500');
        } else {
            dot.classList.remove('bg-emerald-500');
            dot.classList.add('bg-red-500');
        }
    }
}

function showToast(message, type = 'success') {
    const container = document.getElementById('toast-container');
    if(!container) return;

    // Usamos Emerald para éxito
    const colors = type === 'success' ? 'bg-emerald-600' : 'bg-red-600';
    const icon = type === 'success' ? '<i class="fas fa-check"></i>' : '<i class="fas fa-ban"></i>';

    const toast = document.createElement('div');
    toast.className = `${colors} text-white px-4 py-3 rounded-lg shadow-xl flex items-center gap-3 animate-fadeIn backdrop-blur-md border border-white/10`;
    toast.innerHTML = `${icon} <span class="font-bold text-sm">${message}</span>`;

    container.appendChild(toast);

    setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 500);
    }, 3000);
}

// Buscador (Sin cambios, solo para asegurar que cargue)
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

            if (visibles === 0) noResults.style.display = 'flex';
            else noResults.style.display = 'none';
        });
    }
});