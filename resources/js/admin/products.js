document.addEventListener('DOMContentLoaded', () => {
    
    // =========================================================
    // 1. CÓDIGO HEREDADO (DataTables y Gráficos)
    // =========================================================
    if (window.jQuery && $('#dataTable').length) {
        $('#dataTable').DataTable({
            "language": { "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json" }
        });
    }

    const chartCanvas = document.getElementById("productosCategoriaChart");
    const dataContainer = document.getElementById("products-chart-data");

    if (chartCanvas && dataContainer && typeof Chart !== 'undefined') {
        const labels = JSON.parse(dataContainer.dataset.labels);
        const data = JSON.parse(dataContainer.dataset.values);

        new Chart(chartCanvas, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796'],
                    hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf', '#dda20a', '#be2617', '#60616f'],
                    hoverBorderColor: "rgba(234, 236, 244, 1)",
                }],
            },
            options: { maintainAspectRatio: false, legend: { display: false }, cutoutPercentage: 80 },
        });
    }

    // =========================================================
    // 2. FILTROS AUTOMÁTICOS
    // =========================================================
    const formFiltros = document.getElementById('filtro-productos');
    const selectores = document.querySelectorAll('.filter-dropdown');
    
    selectores.forEach(select => {
        select.addEventListener('change', () => {
            document.body.style.cursor = 'wait';
            formFiltros.submit();
        });
    });

    // =========================================================
    // 3. LÓGICA DEL MODAL DE BORRADO
    // =========================================================
    const modal = document.getElementById('delete-modal-product');
    const panel = modal?.querySelector('#delete-modal-panel');
    const btnConfirmar = document.getElementById('modal-confirm-btn');
    let formularioActivo = null;

    window.confirmarBorradoProducto = (formId) => {
        formularioActivo = document.getElementById(formId);
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            panel.classList.remove('scale-95');
        }, 10);
    };

    window.cerrarModalBorradoProducto = () => {
        modal.classList.add('opacity-0');
        panel.classList.add('scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
            formularioActivo = null;
        }, 300);
    };

    if (btnConfirmar) {
        btnConfirmar.addEventListener('click', () => {
            if (formularioActivo) {
                btnConfirmar.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Eliminando...';
                btnConfirmar.disabled = true;
                formularioActivo.submit();
            }
        });
    }

    if (modal) {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) window.cerrarModalBorradoProducto();
        });
    }
}); // <-- FIN DEL DOMContentLoaded

// =========================================================
// 4. CAMBIAR ESTADO EN TIEMPO REAL (FETCH AJAX)
// =========================================================
window.cambiarEstadoProducto = async (rutaUrl, botonElemento) => {
    const htmlOriginal = botonElemento.innerHTML;
    
    botonElemento.innerHTML = '<i class="fas fa-spinner fa-spin text-[10px]"></i>';
    botonElemento.disabled = true;

    try {
        const tokenMeta = document.querySelector('meta[name="csrf-token"]');
        if(!tokenMeta) {
            alert("Error: Falta la etiqueta meta CSRF en tu layout admin.blade.php");
            botonElemento.innerHTML = htmlOriginal;
            botonElemento.disabled = false;
            return;
        }

        const respuesta = await fetch(rutaUrl, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': tokenMeta.content,
                'Accept': 'application/json'
            }
        });

        const data = await respuesta.json();

        // Si la respuesta fue exitosa y Laravel nos confirma el success
        if (respuesta.ok && data.success) {
            if (data.is_active) {
                botonElemento.className = 'border text-[10px] font-bold px-2 py-1 rounded uppercase tracking-wider transition-all duration-300 hover:scale-105 bg-emerald-500/10 text-emerald-400 border-emerald-500/20 hover:bg-emerald-500/30';
                botonElemento.innerHTML = '<i class="fas fa-check-circle text-[10px] mr-1"></i> <span>Stock</span>';
            } else {
                botonElemento.className = 'border text-[10px] font-bold px-2 py-1 rounded uppercase tracking-wider transition-all duration-300 hover:scale-105 bg-red-500/10 text-red-400 border-red-500/20 hover:bg-red-500/30';
                botonElemento.innerHTML = '<i class="fas fa-times-circle text-[10px] mr-1"></i> <span>Agotado</span>';
            }
        } else {
            // AQUÍ ESTÁ LA MAGIA DETECTIVE: Mostramos el error real
            alert('Fallo en Laravel: ' + (data.message || 'Error desconocido del servidor'));
            console.error('Detalles del error:', data);
            botonElemento.innerHTML = htmlOriginal;
        }
    } catch (error) {
        alert('Error de conexión o código roto: ' + error.message);
        console.error('Error Crítico:', error);
        botonElemento.innerHTML = htmlOriginal;
    } finally {
        botonElemento.disabled = false;
    }
};