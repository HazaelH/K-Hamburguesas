let cart = [];
let currentProduct = null;
let currentLocalePOS = window.MENU_LANG?.locale || 'es';
let isProcessingOrder = false;

const vibrate = () => { if (navigator.vibrate) navigator.vibrate(50); };

// =========================================================
// FUNCIÓN AUXILIAR DE MONEDA INTERNACIONAL
// =========================================================
const formatearMoneda = (cantidadEnPesos) => {
    const rate = window.MENU_LANG?.exchangeRate || 1;
    const symbol = window.MENU_LANG?.currencySymbol || '$';
    const code = window.MENU_LANG?.currencyCode || '';
    
    const cantidadConvertida = (cantidadEnPesos / rate).toFixed(2);
    return `${symbol}${cantidadConvertida}${code}`;
};

// =========================================================
// CONTROL DEL CAJÓN DESLIZABLE (MÓVIL)
// =========================================================
window.toggleCartMobile = function() {
    vibrate();
    const panel = document.getElementById('cart-panel');
    
    // Simplemente intercalamos la clase que lo esconde al 100% hacia abajo
    panel.classList.toggle('translate-y-full');
    panel.classList.toggle('translate-y-0');
};

window.cambiarIconoMesa = function(valor) {
    const icono = document.getElementById('icono-mesa');
    if (!icono) return;

    if (valor === "") {
        // Es un pedido "Para llevar"
        icono.className = 'fas fa-shopping-bag text-orange-500';
    } else {
        // Es un pedido "En Mesa"
        icono.className = 'fas fa-chair text-blue-500';
    }
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
    toast.className = `${style.bg} ${style.border} text-white px-5 py-4 rounded-xl shadow-2xl flex items-center gap-3 transform transition-all duration-300 translate-x-full opacity-0 border backdrop-blur-md mb-2 pointer-events-auto`;
    toast.innerHTML = `<span class="text-2xl">${style.icon}</span><span class="font-bold text-sm tracking-wide leading-tight">${message}</span>`;
    container.appendChild(toast);
    requestAnimationFrame(() => {
        toast.classList.remove('translate-x-full', 'opacity-0');
        toast.classList.add('translate-x-0', 'opacity-100');
    });
    setTimeout(() => {
        toast.classList.remove('translate-x-0', 'opacity-100');
        toast.classList.add('translate-x-full', 'opacity-0');
        setTimeout(() => toast.remove(), 300);
    }, 3000); 
};

window.filterCategory = function(categoria, btnElement) {
    vibrate();
    if (btnElement) {
        document.querySelectorAll('.cat-btn').forEach(b => {
            b.classList.remove('bg-orange-600', 'text-white', 'shadow-lg', 'shadow-orange-900/20');
            b.classList.add('bg-slate-800', 'text-slate-300');
        });
        btnElement.classList.remove('bg-slate-800', 'text-slate-300');
        btnElement.classList.add('bg-orange-600', 'text-white', 'shadow-lg', 'shadow-orange-900/20');
    }
    document.querySelectorAll('.product-item').forEach(prod => {
        prod.style.display = (categoria === 'all' || prod.dataset.categoria === categoria) ? 'flex' : 'none';
    });
};

document.getElementById('buscador-pos')?.addEventListener('input', (e) => {
    const texto = e.target.value.toLowerCase();
    document.querySelectorAll('.cat-btn')[0].click();
    document.querySelectorAll('.product-item').forEach(prod => {
        prod.style.display = prod.dataset.nombre.includes(texto) ? 'flex' : 'none';
    });
});

window.openCustomizationModal = function(id, name, basePrice, img, rawOptions, locale = 'es') {
    vibrate();
    currentLocalePOS = locale;
    currentProduct = { id, name, basePrice, img, extras: 0, selections: [] };

    let options = [];
    if (typeof rawOptions === 'string') {
        try { options = JSON.parse(rawOptions) || []; } catch(e) { options = []; }
    } else if (Array.isArray(rawOptions)) {
        options = rawOptions;
    }

    if (options.length === 0) {
        return processAddToCart();
    }

    document.getElementById('modal-prod-img').src = img;
    document.getElementById('modal-prod-name').innerText = name;
    
    document.getElementById('modal-prod-base-price').innerText = formatearMoneda(basePrice);
    document.getElementById('modal-final-price').innerText = formatearMoneda(basePrice);

    const container = document.getElementById('modal-options-container');
    container.innerHTML = `<p class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-3">${window.POS_LANG.extras}</p>`;

    options.forEach((opt, index) => {
        let optNameTranslated = typeof opt === 'string' ? opt : opt.nombre;
        if (typeof opt === 'object') {
            if (locale === 'en' && opt.nombre_en) optNameTranslated = opt.nombre_en;
            if (locale === 'pt' && opt.nombre_pt) optNameTranslated = opt.nombre_pt;
        }

        const originalOptName = typeof opt === 'string' ? opt : opt.nombre;
        const optPriceMxn = parseFloat(opt.precio || 0);
        
        const priceBadge = optPriceMxn > 0 ? `<span class="bg-emerald-500/20 text-emerald-400 px-2 py-0.5 rounded text-xs font-bold font-mono">+${formatearMoneda(optPriceMxn)}</span>` : '';

        container.innerHTML += `
            <label class="flex items-center justify-between p-4 bg-slate-800/80 border border-slate-700 rounded-xl mb-2 cursor-pointer hover:border-orange-500 transition-colors group">
                <div class="flex items-center gap-3">
                    <div class="relative flex items-center justify-center">
                        <input type="checkbox" class="custom-extra-checkbox peer appearance-none w-6 h-6 border-2 border-slate-600 rounded bg-slate-900 checked:bg-orange-500 checked:border-orange-500 transition-all cursor-pointer" 
                               data-name="${originalOptName}" data-price="${optPriceMxn}" data-translated="${optNameTranslated}">
                        <i class="fas fa-check absolute text-white text-sm opacity-0 peer-checked:opacity-100 pointer-events-none transition-opacity"></i>
                    </div>
                    <span class="font-bold text-slate-200 group-hover:text-white transition-colors">${optNameTranslated}</span>
                </div>
                ${priceBadge}
            </label>
        `;
    });

    document.querySelectorAll('.custom-extra-checkbox').forEach(box => {
        box.addEventListener('change', calculateModalTotal);
    });

    document.getElementById('modal-add-btn').onclick = processAddToCart;

    const modal = document.getElementById('customization-modal');
    const panel = document.getElementById('customization-panel');
    modal.classList.remove('hidden');
    setTimeout(() => {
        modal.classList.remove('opacity-0');
        panel.classList.remove('scale-95');
        panel.classList.add('scale-100');
    }, 10);
};

function calculateModalTotal() {
    let extraCostMxn = 0;
    currentProduct.selections = [];

    document.querySelectorAll('.custom-extra-checkbox:checked').forEach(box => {
        const priceMxn = parseFloat(box.dataset.price);
        extraCostMxn += priceMxn;
        currentProduct.selections.push({ 
            name: box.dataset.name, 
            translatedName: box.dataset.translated,
            price: priceMxn 
        });
    });

    currentProduct.extras = extraCostMxn;
    const finalPriceMxn = currentProduct.basePrice + extraCostMxn;
    
    document.getElementById('modal-final-price').innerText = formatearMoneda(finalPriceMxn);
}

window.closeCustomizationModal = function() {
    const modal = document.getElementById('customization-modal');
    const panel = document.getElementById('customization-panel');
    modal.classList.add('opacity-0');
    panel.classList.remove('scale-100');
    panel.classList.add('scale-95');
    setTimeout(() => modal.classList.add('hidden'), 300);
    currentProduct = null;
};

function processAddToCart() {
    vibrate();
    
    const selectionNames = currentProduct.selections ? currentProduct.selections.map(s => s.name).sort().join(',') : '';
    const uniqueId = `${currentProduct.id}-${selectionNames}`;
    const finalPriceMxn = currentProduct.basePrice + (currentProduct.extras || 0);
    
    const existingItem = cart.find(item => item.uniqueId === uniqueId);

    if (existingItem) {
        existingItem.qty++;
        showToast(`+1 ${currentProduct.name}`, 'info');
    } else {
        cart.push({ 
            uniqueId: uniqueId, 
            originalId: currentProduct.id, 
            name: currentProduct.name, 
            price: finalPriceMxn, 
            img: currentProduct.img, 
            qty: 1,
            selections: currentProduct.selections || []
        });
        showToast(`${currentProduct.name} ${window.POS_LANG.added}`, 'success');
    }
    
    updateCartUI();
    if (document.getElementById('customization-modal').classList.contains('hidden') === false) {
        closeCustomizationModal();
    }
}

window.changeQty = function(uniqueId, change) {
    vibrate();
    const item = cart.find(item => item.uniqueId === uniqueId);
    if (item) {
        item.qty += change;
        if (item.qty <= 0) cart = cart.filter(i => i.uniqueId !== uniqueId);
        updateCartUI();
    }
};

window.clearCart = function() {
    if(cart.length === 0 || isProcessingOrder) return;
    vibrate();

    const modal = document.getElementById('clear-cart-modal');
    const panel = document.getElementById('clear-cart-panel');
    
    modal.classList.remove('hidden');
    requestAnimationFrame(() => {
        modal.classList.remove('opacity-0');
        panel.classList.remove('scale-95');
        panel.classList.add('scale-100');
    });
};

window.closeClearModal = function() {
    const modal = document.getElementById('clear-cart-modal');
    const panel = document.getElementById('clear-cart-panel');
    
    modal.classList.add('opacity-0');
    panel.classList.remove('scale-100');
    panel.classList.add('scale-95');
    setTimeout(() => modal.classList.add('hidden'), 300);
};

window.confirmClearCart = function() {
    cart = []; 
    updateCartUI(); 
    showToast(window.POS_LANG.account_cleared, 'info'); 
    
    if(document.getElementById('icono-mesa')) {
        document.getElementById('icono-mesa').className = 'fas fa-shopping-bag text-orange-500';
    }
    
    closeClearModal();
};

window.updateCartUI = function() {
    const container = document.getElementById('cart-items');
    const totalEl = document.getElementById('cart-total');
    const btnPagar = document.getElementById('btn-pagar');
    container.innerHTML = '';
    let totalMxn = 0;

    if (cart.length === 0) {
        container.innerHTML = `<div class="h-full flex flex-col items-center justify-center text-slate-500 opacity-50 select-none"><i class="fas fa-receipt text-6xl mb-4"></i><p class="font-bold text-lg">${window.POS_LANG.empty_acc}</p><p class="text-sm text-center mt-2">${window.POS_LANG.empty_desc}</p></div>`;
        btnPagar.disabled = true;
        totalEl.innerText = formatearMoneda(0);
        
        // Actualizar badge móvil
        const mobileTotalBadge = document.getElementById('mobile-cart-total-badge');
        if (mobileTotalBadge) mobileTotalBadge.innerText = formatearMoneda(0);
        return;
    }

    btnPagar.disabled = false;

    cart.forEach(item => {
        totalMxn += item.price * item.qty;
        
        let extrasHtml = '';
        if (item.selections && item.selections.length > 0) {
            const modsText = item.selections.map(s => s.price > 0 ? `${s.translatedName} (+${formatearMoneda(s.price)})` : s.translatedName).join(', ');
            extrasHtml = `<p class="text-[10px] text-orange-400 font-mono mt-0.5 leading-tight line-clamp-1">+ ${modsText}</p>`;
        }

        const itemDiv = document.createElement('div');
        itemDiv.className = 'flex gap-2 bg-slate-800/80 p-2 rounded-xl border border-slate-700 items-center mb-2 shadow-sm w-full';
        
        itemDiv.innerHTML = `
            <div class="w-12 h-12 rounded-lg overflow-hidden shrink-0 border border-slate-600 bg-slate-900">
                <img src="${item.img}" alt="${item.name}" class="w-full h-full object-cover">
            </div>
            <div class="flex-1 min-w-0 pr-1">
                <div class="text-white text-sm font-bold truncate leading-tight" title="${item.name}">${item.name}</div>
                ${extrasHtml}
                <div class="text-emerald-400 font-bold text-xs mt-0.5">${formatearMoneda(item.price)}</div>
            </div>
            <div class="flex items-center bg-slate-900 rounded-lg border border-slate-700 shrink-0 overflow-hidden">
                <button aria-label="Disminuir cantidad" onclick="changeQty('${item.uniqueId}', -1)" class="w-8 h-8 flex items-center justify-center text-slate-300 hover:bg-red-500 hover:text-white transition-colors"><i class="fas fa-minus text-xs"></i></button>
                <span class="text-white font-bold text-xs w-5 text-center select-none">${item.qty}</span>
                <button aria-label="Aumentar cantidad" onclick="changeQty('${item.uniqueId}', 1)" class="w-8 h-8 flex items-center justify-center text-slate-300 hover:bg-blue-500 hover:text-white transition-colors"><i class="fas fa-plus text-xs"></i></button>
            </div>
        `;
        container.appendChild(itemDiv);
    });

    totalEl.innerText = formatearMoneda(totalMxn);
    
    // Actualizar badge móvil
    const mobileFabTotal = document.getElementById('mobile-fab-total');
    if (mobileFabTotal) mobileFabTotal.innerText = formatearMoneda(totalMxn);
};

window.submitOrder = async function() {
    // Si está vacío o ya se está procesando una orden, bloqueamos.
    if (cart.length === 0 || isProcessingOrder) return;
    
    vibrate();
    isProcessingOrder = true; // ACTIVAMOS EL CANDADO

    const btn = document.getElementById('btn-pagar');
    const mesaEl = document.getElementById('pos-mesa'); 
    const clienteEl = document.getElementById('pos-cliente');
    const pagoEl = document.querySelector('input[name="metodo_pago_pos"]:checked');
    
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin text-2xl"></i>';

    const formattedCart = cart.map(item => ({
        id: item.originalId,           
        id_producto: item.originalId,  
        name: item.name,
        // Mandamos las cosas, pero el backend las re-calculará
        qty: item.qty,                 
        cantidad: item.qty,
        modificaciones: item.selections.map(s => ({ grupo: 'Extra POS', valor: s.name, precio: s.price }))
    }));

    try {
        const response = await fetch(API_STORE_ORDER, { 
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
            body: JSON.stringify({
                items: formattedCart,
                mesa: mesaEl ? mesaEl.value : null,
                cliente: clienteEl ? clienteEl.value : '', 
                metodo_pago: pagoEl ? pagoEl.value : 'efectivo'
                // NOTA: Quité la variable "total" de aquí para evitar que el profe la manipule.
            })
        });

        const result = await response.json();

        if (result.success) {
            showToast(window.POS_LANG.order_sent, 'success');
            const width = 400, height = 600, left = (screen.width - width) / 2, top = (screen.height - height) / 2;
            
            let ticketUrl = `/empleado/ticket/${result.order_id}`;
            if (currentLocalePOS !== 'es') {
                ticketUrl = `/${currentLocalePOS}${ticketUrl}`;
            }

            window.open(ticketUrl, 'Ticket', `width=${width},height=${height},top=${top},left=${left}`);
            
            cart = []; 
            if(mesaEl) mesaEl.value = ""; 
            if(clienteEl) clienteEl.value = ""; 
            if(document.getElementById('icono-mesa')) document.getElementById('icono-mesa').className = 'fas fa-shopping-bag text-orange-500';
            
            const panel = document.getElementById('cart-panel');
            if (panel && !panel.classList.contains('translate-y-full')) {
                window.toggleCartMobile(); 
            }

            updateCartUI();
        } else {
            showToast('Error: ' + result.message, 'error');
        }
    } catch (error) {
        showToast(window.POS_LANG.conn_error, 'error');
    } finally {
        btn.innerHTML = originalText;
        btn.disabled = cart.length === 0;
        isProcessingOrder = false; // DESACTIVAMOS EL CANDADO
    }
};

document.addEventListener('DOMContentLoaded', () => updateCartUI());