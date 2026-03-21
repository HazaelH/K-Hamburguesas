// Variables de control global
let currentProduct = null;
let currentQuantity = 1;
let currentLocaleMenu = window.MENU_LANG?.locale || 'es';

let currentCategory = 'Todas';
let currentSearchText = '';
let currentSortMode = 'default';

document.addEventListener('DOMContentLoaded', () => {
    const params = new URLSearchParams(window.location.search);
    const catDesdeUrl = params.get('categoria');
    
    if (catDesdeUrl) {
        setTimeout(() => {
            window.cambiarCategoria(catDesdeUrl, null);
        }, 100);
    }

    const searchInput = document.getElementById('search-input');
    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            currentSearchText = e.target.value.toLowerCase().trim();
            aplicarFiltrosAvanzados();
        });
    }

    const sortSelect = document.getElementById('sort-select');
    if (sortSelect) {
        sortSelect.addEventListener('change', (e) => {
            currentSortMode = e.target.value;
            aplicarFiltrosAvanzados();
        });
    }
});

// ==========================================
// CONTROL DE DISEÑO MÓVIL Y PC
// ==========================================
window.setGlobalView = function(viewType) {
    const gridContainer = document.getElementById('products-grid');
    const btnGrid = document.getElementById('btn-view-grid');
    const btnList = document.getElementById('btn-view-list');

    if (viewType === 'grid') {
        gridContainer.className = 'grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 sm:gap-6 view-grid';
        btnGrid.classList.replace('text-gray-500', 'text-orange-500');
        btnGrid.classList.replace('hover:text-white', 'bg-gray-900');
        btnList.classList.replace('text-orange-500', 'text-gray-500');
        btnList.classList.replace('bg-gray-900', 'hover:text-white');
    } else {
        gridContainer.className = 'grid grid-cols-1 gap-4 sm:gap-6 view-list';
        btnList.classList.replace('text-gray-500', 'text-orange-500');
        btnList.classList.replace('hover:text-white', 'bg-gray-900');
        btnGrid.classList.replace('text-orange-500', 'text-gray-500');
        btnGrid.classList.replace('bg-gray-900', 'hover:text-white');
    }
};

// ==========================================
// MOTOR DE BÚSQUEDA Y FILTRADO
// ==========================================
window.cambiarCategoria = function(categoria, btnElement) {
    currentCategory = categoria;

    const mobileSelect = document.getElementById('mobile-category-select');
    if(mobileSelect && btnElement) {
        mobileSelect.value = categoria;
    }

    document.querySelectorAll('.filter-btn').forEach(btn => {
        if(btn.dataset.category === categoria) {
            btn.classList.add('bg-orange-600', 'text-white', 'shadow-lg');
            btn.classList.remove('bg-gray-800', 'text-gray-400');
        } else {
            btn.classList.remove('bg-orange-600', 'text-white', 'shadow-lg');
            btn.classList.add('bg-gray-800', 'text-gray-400');
        }
    });

    aplicarFiltrosAvanzados();
};

function aplicarFiltrosAvanzados() {
    const grid = document.getElementById('products-grid');
    const cards = Array.from(grid.querySelectorAll('.product-card'));
    const noResultsMsg = document.getElementById('no-results-msg');
    
    let itemsVisibles = 0;

    cards.forEach(card => {
        const cat = card.dataset.categoria;
        const nombre = card.dataset.nombre;
        const desc = card.dataset.desc;

        const matchCategory = (currentCategory === 'Todas' || cat === currentCategory);
        const matchSearch = (nombre.includes(currentSearchText) || desc.includes(currentSearchText));

        if (matchCategory && matchSearch) {
            card.style.display = 'flex';
            setTimeout(() => {
                card.style.opacity = '1';
                card.style.transform = 'scale(1)';
            }, 10);
            itemsVisibles++;
        } else {
            card.style.opacity = '0';
            card.style.transform = 'scale(0.95)';
            setTimeout(() => {
                card.style.display = 'none';
            }, 300);
        }
    });

    if (itemsVisibles === 0) {
        setTimeout(() => noResultsMsg.classList.remove('hidden'), 300);
    } else {
        noResultsMsg.classList.add('hidden');
    }

    if (currentSortMode !== 'default') {
        cards.sort((a, b) => {
            const precioA = parseFloat(a.dataset.precio);
            const precioB = parseFloat(b.dataset.precio);
            const nombreA = a.dataset.nombre;
            const nombreB = b.dataset.nombre;

            if (currentSortMode === 'price-asc') return precioA - precioB;
            if (currentSortMode === 'price-desc') return precioB - precioA;
            if (currentSortMode === 'name-asc') return nombreA.localeCompare(nombreB);
            return 0;
        });
        cards.forEach(card => grid.appendChild(card));
    }
}

// ==========================================
// INTERACCIONES DEL MODAL
// ==========================================
window.abrirModal = (producto, locale = 'es') => {
    currentProduct = producto;
    currentQuantity = 1; 
    currentLocaleMenu = locale;
    
    const modal = document.getElementById('product-modal');
    const container = document.getElementById('modal-container');
    
    // TEXTOS TRADUCIDOS
    const nombreLocal = (locale === 'en' && producto.nombre_en) ? producto.nombre_en : 
                        ((locale === 'pt' && producto.nombre_pt) ? producto.nombre_pt : producto.nombre);
    
    const descLocal = (locale === 'en' && producto.descripcion_en) ? producto.descripcion_en : 
                      ((locale === 'pt' && producto.descripcion_pt) ? producto.descripcion_pt : producto.descripcion);

    document.getElementById('modal-title').innerText = nombreLocal;
    document.getElementById('modal-desc').innerText = descLocal;
    
    const imgElement = document.getElementById('modal-img');
    imgElement.src = producto.imagen_url ? `/imagenes/${producto.imagen_url}` : 'https://via.placeholder.com/400x300?text=Sin+Imagen';

    // PRECIO
    let precioBase = producto.precio_final ? producto.precio_final : producto.precio;
    precioBase = parseFloat(precioBase);
    
    const rate = window.MENU_LANG?.exchangeRate || 1;
    const symbol = window.MENU_LANG?.currencySymbol || '$';
    const code = window.MENU_LANG?.currencyCode || '';
    
    const precioConvertido = (precioBase / rate).toFixed(2);
    document.getElementById('modal-price').innerText = `${symbol}${precioConvertido}${code}`;
    
    const opcionesContenedor = document.getElementById('opciones-contenedor');
    const opcionesDiv = document.getElementById('modal-opciones-dinamicas');
    
    if (opcionesContenedor && opcionesDiv) {
        opcionesDiv.innerHTML = ''; 

        let opciones = producto.opciones_personalizacion;
        if (typeof opciones === 'string') {
            try { opciones = JSON.parse(opciones); } catch(e) { opciones = []; }
        }

        if (opciones && opciones.length > 0) {
            opcionesContenedor.classList.remove('hidden');
            opciones.forEach((opcion) => {
                
                let nombreOpt = typeof opcion === 'string' ? opcion : opcion.nombre;
                if (typeof opcion === 'object') {
                    if (locale === 'en' && opcion.nombre_en) nombreOpt = opcion.nombre_en;
                    if (locale === 'pt' && opcion.nombre_pt) nombreOpt = opcion.nombre_pt;
                }

                const precioOptMxn = typeof opcion === 'string' ? 0 : (parseFloat(opcion.precio) || 0);
                const precioOptConvertido = (precioOptMxn / rate).toFixed(2);
                
                const textoPrecio = precioOptMxn > 0 ? `<span class="text-emerald-400 ml-auto font-black">+${symbol}${precioOptConvertido}${code}</span>` : '';
                const nombreOriginal = typeof opcion === 'string' ? opcion : opcion.nombre;

                opcionesDiv.innerHTML += `
                    <label class="flex items-center gap-3 bg-slate-800 p-3 rounded-xl border border-slate-600 cursor-pointer hover:border-orange-500 transition-colors shadow-inner group">
                        <input type="checkbox" value="${nombreOriginal}" data-precio="${precioOptMxn}" onchange="actualizarCantidadVisual()" class="opcion-dinamica-checkbox w-5 h-5 text-orange-600 bg-slate-900 border-slate-500 rounded focus:ring-orange-500 focus:ring-2 cursor-pointer transition-all">
                        <span class="text-white text-sm font-bold group-hover:text-orange-400 transition-colors">${nombreOpt}</span>
                        ${textoPrecio}
                    </label>
                `;
            });
        } else {
            opcionesContenedor.classList.add('hidden');
        }
    }

    document.getElementById('modal-notas').value = '';
    actualizarCantidadVisual();

    modal.classList.remove('hidden');
    setTimeout(() => {
        container.classList.remove('translate-y-8', 'opacity-0', 'scale-95');
        container.classList.add('translate-y-0', 'opacity-100', 'scale-100');
    }, 10);
};

window.cerrarModal = function() {
    const modal = document.getElementById('product-modal');
    const container = document.getElementById('modal-container');
    
    container.classList.remove('translate-y-0', 'opacity-100', 'scale-100');
    container.classList.add('translate-y-8', 'opacity-0', 'scale-95');

    setTimeout(() => {
        modal.classList.add('hidden');
        currentProduct = null;
    }, 300);
};

window.cambiarCantidad = function(change) {
    const nuevaCantidad = currentQuantity + change;
    if (nuevaCantidad >= 1) {
        currentQuantity = nuevaCantidad;
        actualizarCantidadVisual();
        
        const span = document.getElementById('cantidad-span');
        span.classList.add('scale-150', 'text-orange-500');
        setTimeout(() => span.classList.remove('scale-150', 'text-orange-500'), 150);
    }
};

window.actualizarCantidadVisual = function() {
    const spanCantidad = document.getElementById('cantidad-span');
    if (spanCantidad) spanCantidad.innerText = currentQuantity;
    
    if (currentProduct) {
        let precioBaseMxn = currentProduct.precio_final ? currentProduct.precio_final : currentProduct.precio;
        precioBaseMxn = parseFloat(precioBaseMxn);

        let costoExtrasMxn = 0;
        const checkboxes = document.querySelectorAll('.opcion-dinamica-checkbox:checked');
        checkboxes.forEach((chk) => {
            costoExtrasMxn += parseFloat(chk.dataset.precio || 0);
        });

        const precioUnitarioMxn = precioBaseMxn + costoExtrasMxn;
        const totalMxn = precioUnitarioMxn * currentQuantity;
        
        const rate = window.MENU_LANG?.exchangeRate || 1;
        const symbol = window.MENU_LANG?.currencySymbol || '$';
        const code = window.MENU_LANG?.currencyCode || '';
        
        const totalConvertido = (totalMxn / rate).toFixed(2);
        
        const modalTotal = document.getElementById('modal-total');
        if(modalTotal) modalTotal.innerText = `${symbol}${totalConvertido}${code}`;
    }
}

// ==========================================
// ENVÍO AL CARRITO
// ==========================================
window.agregarAlCarrito = function() {
    if (!currentProduct) return;

    const modalElement = document.getElementById('product-modal');
    
    // Leemos el mensaje de preparando y error del DOM (con fallbacks)
    const msgPreparing = modalElement.getAttribute('data-msg-preparing') || 'Guardando...';
    const msgDenied = modalElement.getAttribute('data-msg-denied') || 'Acción denegada';
    const msgError = modalElement.getAttribute('data-msg-error') || 'Error de conexión';
    const msgSuccessTitle = modalElement.getAttribute('data-msg-success-title') || 'Añadido';

    const notas = document.getElementById('modal-notas').value.trim();
    const btnText = document.getElementById('btn-add-text');
    const originalText = btnText.innerHTML;

    btnText.innerHTML = `<i class="fas fa-spinner fa-spin"></i> ${msgPreparing}`;

    let modificaciones = [];
    const checkboxes = document.querySelectorAll('.opcion-dinamica-checkbox:checked');
    checkboxes.forEach((chk) => {
        modificaciones.push({ grupo: 'Predefinido', valor: chk.value });
    });

    if (notas !== '') {
        modificaciones.push({ grupo: 'Nota', valor: notas });
    }

    fetch('/carrito/agregar', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            id_producto: currentProduct.id_producto,
            cantidad: currentQuantity,
            modificaciones: modificaciones
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'ok') {
            actualizarIconoCarrito(data.total_items);
            
            // LA MAGIA: Usamos data.mensaje que viene traducido desde ClientController
            const descMensaje = data.mensaje ? `${currentQuantity}x ${data.mensaje}` : `${currentQuantity}x producto agregado.`;

            showToast(
                msgSuccessTitle, 
                descMensaje, 
                'success'
            );
            cerrarModal();
        } else {
            showToast('Aviso', msgDenied, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Fallo', msgError, 'error');
    })
    .finally(() => {
        btnText.innerHTML = originalText;
    });
};

function actualizarIconoCarrito(cantidad) {
    const badge = document.getElementById('cart-count');
    if (badge) {
        badge.innerText = cantidad;
        badge.classList.remove('animate-pulse');
        badge.classList.add('scale-150', 'bg-green-500');
        
        setTimeout(() => {
            badge.classList.remove('scale-150', 'bg-green-500');
            badge.classList.add('animate-pulse');
        }, 500);
    }
}

// ==========================================
// SISTEMA DE TOASTS UNIFICADO
// ==========================================
window.showToast = function(title, msg, type = 'success') {
    const container = document.getElementById('toast-container');
    if (!container) return; 

    const toast = document.createElement('div');
    let borderColor = type === 'error' ? 'border-red-500' : (type === 'info' ? 'border-orange-500' : 'border-green-500');
    let iconClass = type === 'error' ? 'fa-trash-alt text-red-500' : (type === 'info' ? 'fa-info-circle text-orange-500' : 'fa-check-circle text-green-500');

    toast.className = `bg-gray-900 text-white p-4 rounded-lg shadow-2xl border-l-4 ${borderColor} transform transition-all duration-300 translate-y-10 opacity-0 min-w-[250px] flex items-start gap-3 pointer-events-auto z-50`;
    toast.innerHTML = `
        <div class="mt-0.5 text-lg"><i class="fas ${iconClass}"></i></div>
        <div>
            <div class="font-bold text-sm leading-tight">${title}</div>
            <div class="text-[11px] text-gray-400 mt-0.5">${msg}</div>
        </div>
    `;

    container.appendChild(toast);
    requestAnimationFrame(() => toast.classList.remove('translate-y-10', 'opacity-0'));

    setTimeout(() => {
        toast.classList.add('opacity-0', 'translate-x-full');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
};