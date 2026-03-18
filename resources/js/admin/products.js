document.addEventListener('DOMContentLoaded', () => {

    function blindarFormulario(formId, btnId, loadingText) {
        const form = document.getElementById(formId);
        const btn = document.getElementById(btnId);
        let isSubmitting = false;

        if (form && btn) {
            form.addEventListener('submit', function(e) {
                if (isSubmitting) {
                    e.preventDefault();
                    return;
                }

                if (form.checkValidity()) {
                    isSubmitting = true;
                    btn.disabled = true;
                    btn.style.pointerEvents = 'none';
                    btn.classList.add('opacity-75', 'cursor-not-allowed');
                    btn.classList.remove('hover:scale-[1.01]', 'hover:bg-orange-500');
                    btn.innerHTML = `<i class="fas fa-spinner fa-spin mr-2"></i> ${loadingText}`;
                }
            });
        }
    }

    // Activar blindaje si los elementos existen
    blindarFormulario('form-create-product', 'btn-submit-product', 'Guardando producto...');
    blindarFormulario('form-edit-product', 'btn-update-product', 'Actualizando producto...');
    
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
    // 3. LÓGICA DEL MODAL DE BORRADO Y RESTAURACIÓN (BLINDADOS)
    // =========================================================
    const modal = document.getElementById('delete-modal-product');
    const panel = document.getElementById('delete-modal-panel');
    const btnConfirmar = document.getElementById('modal-confirm-btn');
    
    let formularioActivo = null;
    let isProcessingAction = false; // Candado maestro para toda la vista
    
    // Guardamos el texto original del botón para restaurarlo si cierran el modal
    let originalConfirmText = btnConfirmar ? btnConfirmar.innerHTML : 'Eliminar';

    // ABRIR MODAL
    window.confirmarBorradoProducto = (formId) => {
        isProcessingAction = false; // Quitamos el candado
        
        // Restauramos el botón a su estado normal
        if (btnConfirmar) {
            btnConfirmar.disabled = false;
            btnConfirmar.style.pointerEvents = 'auto';
            btnConfirmar.classList.remove('opacity-75', 'cursor-not-allowed');
            btnConfirmar.innerHTML = originalConfirmText; 
        }

        formularioActivo = document.getElementById(formId);
        if (modal && panel) {
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                panel.classList.remove('scale-95');
                panel.classList.add('scale-100');
            }, 10);
        }
    };

    // CERRAR MODAL
    window.cerrarModalBorradoProducto = () => {
        if (modal && panel) {
            modal.classList.add('opacity-0');
            panel.classList.remove('scale-100');
            panel.classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
                formularioActivo = null;
            }, 300);
        }
    };

    // CONFIRMAR ELIMINACIÓN (BLINDADO)
    if (btnConfirmar) {
        btnConfirmar.addEventListener('click', () => {
            if (isProcessingAction) return; // Si el candado está puesto, no hace nada

            if (formularioActivo) {
                isProcessingAction = true; // Ponemos el candado
                
                // Cambiamos el botón visualmente
                btnConfirmar.disabled = true;
                btnConfirmar.style.pointerEvents = 'none';
                btnConfirmar.classList.add('opacity-75', 'cursor-not-allowed');
                btnConfirmar.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Procesando...';
                
                formularioActivo.submit();
            }
        });
    }

    // Cerrar modal al hacer clic afuera
    if (modal) {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) window.cerrarModalBorradoProducto();
        });
    }

    // =========================================================
    // 3.1 BLINDAJE DE BOTONES DE "RESTAURAR" (Sin modal)
    // =========================================================
    const restoreForms = document.querySelectorAll('form[action*="restaurar"]');
    
    restoreForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            // Si ya estamos procesando otra acción, bloqueamos
            if (isProcessingAction) {
                e.preventDefault();
                return;
            }
            
            isProcessingAction = true; // Ponemos el candado
            
            const btn = this.querySelector('button[type="submit"]');
            if (btn) {
                btn.disabled = true;
                btn.style.pointerEvents = 'none';
                btn.classList.add('opacity-75', 'cursor-not-allowed');
                // Cambiamos el icono por un spinner
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            }
        });
    });

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
});