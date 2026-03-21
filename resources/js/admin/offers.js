// Objeto de traducciones (con fallback por seguridad)
const LANG = window.OFFERS_LANG || {
    csrf_error: "Error: Missing CSRF token.",
    server_error: "Server error: ",
    conn_error: "Connection error."
};

document.addEventListener('DOMContentLoaded', () => {

    // =========================================================
    // 1. LÓGICA DEL FORMULARIO DINÁMICO (Ocultar/Mostrar campos)
    // =========================================================
    const selectorAplicacion = document.getElementById('tipo_aplicacion');
    const divCategoria = document.getElementById('div_categoria');
    const divProducto = document.getElementById('div_producto');
    
    const inputCategoria = document.getElementById('referencia_categoria');
    const inputProducto = document.getElementById('referencia_producto');

    if (selectorAplicacion) {
        selectorAplicacion.addEventListener('change', function() {
            const valor = this.value;

            // Primero ocultamos ambos y limpiamos sus valores
            divCategoria.classList.add('hidden');
            divProducto.classList.add('hidden');
            inputCategoria.value = '';
            
            // Solo limpiamos el input del producto si estamos CREANDO. 
            // Si estamos EDITANDO, lo conservamos.
            if (!inputProducto.value) {
                inputProducto.value = '';
            }

            // Mostramos el que corresponda
            if (valor === 'categoria') {
                divCategoria.classList.remove('hidden');
                inputCategoria.required = true;
                inputProducto.required = false;
            } else if (valor === 'producto') {
                divProducto.classList.remove('hidden');
                inputProducto.required = true;
                inputCategoria.required = false;
            } else {
                // Es "todo" el menú
                inputCategoria.required = false;
                inputProducto.required = false;
            }
        });
    }

    // =========================================================
    // LÓGICA DEL BUSCADOR EN EL MODAL DE PRODUCTOS
    // =========================================================
    const buscador = document.getElementById('buscador_modal');
    const filtroCat = document.getElementById('filtro_categoria_modal');
    const items = document.querySelectorAll('.producto-item');
    const noResults = document.getElementById('no_results_modal');

    function filtrarProductos() {
        if(!buscador) return;
        const texto = buscador.value.toLowerCase();
        const cat = filtroCat.value;
        let visibles = 0;

        items.forEach(item => {
            const nombre = item.dataset.nombre.toLowerCase();
            const categoria = item.dataset.categoria;
            const matchTexto = nombre.includes(texto);
            const matchCat = cat === '' || categoria === cat;

            if (matchTexto && matchCat) {
                item.style.display = 'flex';
                visibles++;
            } else {
                item.style.display = 'none';
            }
        });

        if (visibles === 0) {
            noResults.classList.remove('hidden');
        } else {
            noResults.classList.add('hidden');
        }
    }

    if(buscador) buscador.addEventListener('input', filtrarProductos);
    if(filtroCat) filtroCat.addEventListener('change', filtrarProductos);

}); // <-- FIN DEL DOMContentLoaded


// =========================================================
// 2. CAMBIAR ESTADO DE LA OFERTA (FETCH AJAX)
// =========================================================
window.toggleOferta = async (ofertaId, botonElemento, rutaUrl) => {
    const spanCirculo = botonElemento.querySelector('span');
    botonElemento.style.opacity = '0.5';
    botonElemento.disabled = true;

    try {
        const tokenMeta = document.querySelector('meta[name="csrf-token"]');
        if(!tokenMeta) {
            alert(LANG.csrf_error);
            botonElemento.style.opacity = '1';
            botonElemento.disabled = false;
            return;
        }

        const respuesta = await fetch(rutaUrl, {
            method: 'POST', 
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': tokenMeta.content,
                'Accept': 'application/json'
            }
        });

        const data = await respuesta.json();

        if (respuesta.ok && data.success) {
            if (data.activa) {
                botonElemento.classList.remove('bg-slate-600');
                botonElemento.classList.add('bg-emerald-500');
                spanCirculo.classList.remove('-translate-x-2.5');
                spanCirculo.classList.add('translate-x-2.5');
            } else {
                botonElemento.classList.remove('bg-emerald-500');
                botonElemento.classList.add('bg-slate-600');
                spanCirculo.classList.remove('translate-x-2.5');
                spanCirculo.classList.add('-translate-x-2.5');
            }
        } else {
            alert(LANG.server_error + (data.message || 'Desconocido'));
        }
    } catch (error) {
        alert(LANG.conn_error);
        console.error(error);
    } finally {
        botonElemento.style.opacity = '1';
        botonElemento.disabled = false;
    }
};

// =========================================================
// 3. CONTROL DE MODALES (Productos y Borrado)
// =========================================================

// Modal de Productos
window.abrirModalProductos = function() {
    const modal = document.getElementById('modal-productos');
    const panel = document.getElementById('modal-productos-panel');
    modal.classList.remove('hidden');
    setTimeout(() => {
        modal.classList.remove('opacity-0');
        panel.classList.remove('scale-95');
        panel.classList.add('scale-100');
    }, 10);
};

window.cerrarModalProductos = function() {
    const modal = document.getElementById('modal-productos');
    const panel = document.getElementById('modal-productos-panel');
    modal.classList.add('opacity-0');
    panel.classList.remove('scale-100');
    panel.classList.add('scale-95');
    setTimeout(() => modal.classList.add('hidden'), 300);
};

// Seleccionar un producto y pasarlo al Input Oculto
window.seleccionarProducto = function(id, nombre) {
    document.getElementById('referencia_producto').value = id;
    
    const spanTexto = document.getElementById('texto_producto_seleccionado');
    spanTexto.innerHTML = `<span class="text-orange-400 font-bold"><i class="fas fa-check mr-1"></i> ${nombre}</span>`;
    spanTexto.classList.remove('text-slate-400');
    spanTexto.classList.add('text-white');
    
    window.cerrarModalProductos();
};

// Modal de Borrar Oferta
window.confirmarBorradoOferta = function(formId) {
    const modal = document.getElementById('delete-modal-offer');
    const panel = document.getElementById('delete-modal-offer-panel');
    const confirmBtn = document.getElementById('modal-confirm-offer-btn');
    
    modal.classList.remove('hidden');
    setTimeout(() => {
        modal.classList.remove('opacity-0');
        panel.classList.remove('scale-95');
        panel.classList.add('scale-100');
    }, 10);

    confirmBtn.onclick = function() {
        document.getElementById(formId).submit();
    };
};

window.cerrarModalBorradoOferta = function() {
    const modal = document.getElementById('delete-modal-offer');
    const panel = document.getElementById('delete-modal-offer-panel');
    modal.classList.add('opacity-0');
    panel.classList.remove('scale-100');
    panel.classList.add('scale-95');
    setTimeout(() => modal.classList.add('hidden'), 300);
};