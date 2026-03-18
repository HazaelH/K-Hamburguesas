let currentHomeProduct = null;
let currentHomeQuantity = 1;
// Detectamos el idioma actual desde la variable global (si no existe, por defecto 'es')
let currentLocale = window.MENU_LANG?.locale || 'es';
let isAddingToCartHome = false;

// ==========================================
// ABRIR MODAL
// ==========================================
window.abrirModalHome = (producto, locale = 'es') => {
    currentHomeProduct = producto;
    currentHomeQuantity = 1;
    currentLocale = locale; // Guardamos el idioma en el que se hizo clic
    
    const modal = document.getElementById('home-product-modal');
    const container = document.getElementById('home-modal-container');
    
    // 1. TEXTOS TRADUCIDOS
    const nombreLocal = (locale === 'en' && producto.nombre_en) ? producto.nombre_en : 
                        ((locale === 'pt' && producto.nombre_pt) ? producto.nombre_pt : producto.nombre);
    
    const descLocal = (locale === 'en' && producto.descripcion_en) ? producto.descripcion_en : 
                      ((locale === 'pt' && producto.descripcion_pt) ? producto.descripcion_pt : producto.descripcion);

    document.getElementById('home-modal-title').innerText = nombreLocal;
    document.getElementById('home-modal-desc').innerText = descLocal;
    
    const imgElement = document.getElementById('home-modal-img');
    imgElement.src = producto.imagen_url ? `/imagenes/${producto.imagen_url}` : 'https://via.placeholder.com/400x300?text=Sin+Imagen';

    // 2. PRECIO BASE FORMATEADO (Usamos el tipo de cambio inyectado)
    let precioBase = producto.precio_final ? producto.precio_final : producto.precio;
    precioBase = parseFloat(precioBase);
    
    const rate = window.MENU_LANG?.exchangeRate || 1;
    const symbol = window.MENU_LANG?.currencySymbol || '$';
    const code = window.MENU_LANG?.currencyCode || '';
    
    const precioConvertido = (precioBase / rate).toFixed(2);
    document.getElementById('home-modal-price').innerText = `${symbol}${precioConvertido}${code}`;
    
    // --- LÓGICA DE OPCIONES PREDEFINIDAS ---
    const opcionesContenedor = document.getElementById('home-opciones-contenedor');
    const opcionesDiv = document.getElementById('home-modal-opciones-dinamicas');
    
    if (opcionesContenedor && opcionesDiv) {
        opcionesDiv.innerHTML = ''; 

        let opciones = producto.opciones_personalizacion;
        if (typeof opciones === 'string') {
            try { opciones = JSON.parse(opciones); } catch(e) { opciones = []; }
        }

        if (opciones && opciones.length > 0) {
            opcionesContenedor.classList.remove('hidden');
            opciones.forEach((opcion) => {
                // Traducción del Extra
                let nombreOpt = typeof opcion === 'string' ? opcion : opcion.nombre;
                if (typeof opcion === 'object') {
                    if (locale === 'en' && opcion.nombre_en) nombreOpt = opcion.nombre_en;
                    if (locale === 'pt' && opcion.nombre_pt) nombreOpt = opcion.nombre_pt;
                }

                // El data-precio siempre debe guardar el valor en MXN (Pesos) para mandarlo al carrito
                const precioOptMxn = typeof opcion === 'string' ? 0 : (parseFloat(opcion.precio) || 0);
                
                // Pero el texto visual se calcula con la moneda del cliente
                const precioOptConvertido = (precioOptMxn / rate).toFixed(2);
                const textoPrecio = precioOptMxn > 0 ? `<span class="text-emerald-400 ml-auto font-black">+${symbol}${precioOptConvertido}${code}</span>` : '';

                // Agregamos data-nombre-original para guardar en el carrito siempre el nombre en Español (unifica la comanda de la cocina)
                const nombreOriginal = typeof opcion === 'string' ? opcion : opcion.nombre;

                opcionesDiv.innerHTML += `
                    <label class="flex items-center gap-3 bg-slate-800 p-3 rounded-xl border border-slate-600 cursor-pointer hover:border-orange-500 transition-colors shadow-inner group">
                        <input type="checkbox" value="${nombreOriginal}" data-precio="${precioOptMxn}" onchange="actualizarCantidadVisualHome()" class="home-opcion-checkbox w-5 h-5 text-orange-600 bg-slate-900 border-slate-500 rounded focus:ring-orange-500 focus:ring-2 cursor-pointer transition-all">
                        <span class="text-white text-sm font-bold group-hover:text-orange-400 transition-colors">${nombreOpt}</span>
                        ${textoPrecio}
                    </label>
                `;
            });
        } else {
            opcionesContenedor.classList.add('hidden');
        }
    }

    document.getElementById('home-modal-notas').value = '';
    actualizarCantidadVisualHome();

    modal.classList.remove('hidden');
    setTimeout(() => {
        container.classList.remove('translate-y-8', 'opacity-0', 'scale-95');
        container.classList.add('translate-y-0', 'opacity-100', 'scale-100');
    }, 10);
};

// ==========================================
// CERRAR MODAL
// ==========================================
window.cerrarModalHome = function() {
    const modal = document.getElementById('home-product-modal');
    const container = document.getElementById('home-modal-container');
    
    container.classList.remove('translate-y-0', 'opacity-100', 'scale-100');
    container.classList.add('translate-y-8', 'opacity-0', 'scale-95');

    setTimeout(() => {
        modal.classList.add('hidden');
        currentHomeProduct = null;
    }, 300);
};

// ==========================================
// CANTIDADES Y MATEMÁTICAS
// ==========================================
window.cambiarCantidadHome = function(change) {
    const nuevaCantidad = currentHomeQuantity + change;
    if (nuevaCantidad >= 1) {
        currentHomeQuantity = nuevaCantidad;
        actualizarCantidadVisualHome();
        
        const span = document.getElementById('home-cantidad-span');
        span.classList.add('scale-150', 'text-orange-500');
        setTimeout(() => span.classList.remove('scale-150', 'text-orange-500'), 150);
    }
};

window.actualizarCantidadVisualHome = function() {
    const spanCantidad = document.getElementById('home-cantidad-span');
    if (spanCantidad) spanCantidad.innerText = currentHomeQuantity;
    
    if (currentHomeProduct) {
        // Todo el cálculo matemático interno se hace en MXN (Pesos) para evitar decimales infinitos
        let precioBaseMxn = currentHomeProduct.precio_final ? currentHomeProduct.precio_final : currentHomeProduct.precio;
        precioBaseMxn = parseFloat(precioBaseMxn);

        let costoExtrasMxn = 0;
        const checkboxes = document.querySelectorAll('.home-opcion-checkbox:checked');
        checkboxes.forEach((chk) => {
            costoExtrasMxn += parseFloat(chk.dataset.precio || 0);
        });

        const precioUnitarioMxn = precioBaseMxn + costoExtrasMxn;
        const totalMxn = precioUnitarioMxn * currentHomeQuantity;
        
        // Solo al final lo convertimos para mostrarlo en pantalla
        const rate = window.MENU_LANG?.exchangeRate || 1;
        const symbol = window.MENU_LANG?.currencySymbol || '$';
        const code = window.MENU_LANG?.currencyCode || '';
        
        const totalConvertido = (totalMxn / rate).toFixed(2);
        
        document.getElementById('home-modal-total').innerText = `${symbol}${totalConvertido}${code}`;
    }
}

// ==========================================
// AGREGAR AL CARRITO (FETCH AJAX - BLINDADO)
// ==========================================
window.agregarAlCarritoHome = function() {
    if (!currentHomeProduct) return;

    if (isAddingToCartHome) {
        return;
    }

    isAddingToCartHome = true;

    const notas = document.getElementById('home-modal-notas').value.trim();
    const btnText = document.getElementById('home-btn-add-text');
    const originalText = btnText.innerHTML;

    const addBtn = btnText.closest('button'); 
    if (addBtn) {
        addBtn.disabled = true;
        addBtn.style.pointerEvents = 'none';
        addBtn.classList.add('opacity-70');
    }

    btnText.innerHTML = `<i class="fas fa-spinner fa-spin"></i> ${window.K_TRANSLATIONS.client.cart.saving}`;

    let modificaciones = [];
    const checkboxes = document.querySelectorAll('.home-opcion-checkbox:checked');
    checkboxes.forEach((chk) => {
        modificaciones.push({ grupo: 'Predefinido', valor: chk.value });
    });

    if (notas !== '') modificaciones.push({ grupo: 'Nota', valor: notas });

    fetch('/carrito/agregar', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            id_producto: currentHomeProduct.id_producto,
            cantidad: currentHomeQuantity,
            modificaciones: modificaciones
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'ok') {
            actualizarIconoCarritoGlobal(data.total_items);
            cerrarModalHome();
            
            setTimeout(() => {
                abrirUpsellModal();
            }, 300);
            
        } else {
            alert(window.K_TRANSLATIONS.client.cart.denied);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert(window.K_TRANSLATIONS.client.cart.connection_error);
    })
    .finally(() => {
        setTimeout(() => {
            isAddingToCartHome = false;
            
            btnText.innerHTML = originalText;
            if (addBtn) {
                addBtn.disabled = false;
                addBtn.style.pointerEvents = 'auto';
                addBtn.classList.remove('opacity-70');
            }
        }, 400);
    });
};

function actualizarIconoCarritoGlobal(cantidad) {
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
// MODAL DE UPSELLING
// ==========================================
window.abrirUpsellModal = function() {
    const modal = document.getElementById('upsell-modal');
    const panel = document.getElementById('upsell-modal-panel');
    modal.classList.remove('hidden');
    setTimeout(() => {
        panel.classList.remove('scale-95', 'opacity-0');
        panel.classList.add('scale-100', 'opacity-100');
    }, 10);
};

window.cerrarUpsellModal = function() {
    const modal = document.getElementById('upsell-modal');
    const panel = document.getElementById('upsell-modal-panel');
    panel.classList.remove('scale-100', 'opacity-100');
    panel.classList.add('scale-95', 'opacity-0');
    setTimeout(() => modal.classList.add('hidden'), 300);
};