let isProcessing = false;

document.addEventListener('DOMContentLoaded', () => {
    
    // =========================================================
    // 1. AGREGAR AL CARRITO (Desde el Menú Rápido)
    // =========================================================
    const addButtons = document.querySelectorAll('.add-to-cart-btn');

    addButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault(); 

            const form = this.closest('form');
            const formData = new FormData(form);
            
            const originalIcon = this.innerHTML;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            this.disabled = true;

            fetch('/carrito/agregar', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'ok') { 
                    updateNavbarCounter(data.total_items);
                    showToast(window.K_TRANSLATIONS?.client?.cart?.success_title || 'Éxito', data.mensaje, 'success');
                } else {
                    showToast('Error', window.K_TRANSLATIONS?.client?.cart?.denied || 'Acción denegada', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Error', window.K_TRANSLATIONS?.client?.cart?.connection_error || 'Error de conexión', 'error');
            })
            .finally(() => {
                this.innerHTML = originalIcon;
                this.disabled = false;
            });
        });
    });
});

// =========================================================
// FUNCIONES AUXILIARES DE MONEDA (NUEVO)
// =========================================================
function formatearMoneda(cantidadEnPesos) {
    const rate = window.MENU_LANG?.exchangeRate || 1;
    const symbol = window.MENU_LANG?.currencySymbol || '$';
    const code = window.MENU_LANG?.currencyCode || '';
    
    const cantidadConvertida = (cantidadEnPesos / rate).toFixed(2);
    return `${symbol}${cantidadConvertida}${code}`;
}

// =========================================================
// 2. ACTUALIZAR CANTIDAD (+ / -)
// =========================================================
window.changeQty = function(rowId, action, btn) {
    if (isProcessing) return;
    isProcessing = true;

    const originalIcon = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin text-xs"></i>';
    btn.disabled = true;

    fetch(`/carrito/actualizar/${rowId}`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ action: action })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (data.action === 'removed') {
                animateRemoveRow(rowId, data);
            } else {
                document.getElementById(`qty-${rowId}`).innerText = data.new_qty;
                
                // Formateamos el subtotal del ítem
                const itemTotalEl = document.getElementById(`item-total-${rowId}`);
                if(itemTotalEl) {
                    itemTotalEl.innerText = formatearMoneda(data.new_item_total);
                }

                updateGlobalTotals(data);
            }
        }
    })
    .catch(error => console.error('Error:', error))
    .finally(() => {
        btn.innerHTML = originalIcon;
        btn.disabled = false;
        isProcessing = false;
    });
};

// =========================================================
// 3. ELIMINAR DEL CARRITO
// =========================================================
window.removeCartItem = function(btn, rowId) {
    if (isProcessing) return;
    
    isProcessing = true;

    const row = document.getElementById(`row-${rowId}`);
    if (row) row.style.opacity = '0.5';

    fetch(`/carrito/eliminar/${rowId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            animateRemoveRow(rowId, data);
        }
    })
    .catch(error => console.error('Error:', error))
    .finally(() => { isProcessing = false; });
};

// =========================================================
// 4. FUNCIONES AUXILIARES GLOBALES
// =========================================================
function animateRemoveRow(rowId, data) {
    const row = document.getElementById(`row-${rowId}`);
    
    if (data.cart_count === 0 || data.cart_empty === true) {
        window.location.reload(); 
        return; 
    }

    if (row) {
        row.style.transition = 'all 0.3s ease';
        row.style.transform = 'translateX(50px)';
        row.style.opacity = '0';
        
        setTimeout(() => {
            row.remove(); 
            updateGlobalTotals(data); 
        }, 300);
    }
}

function updateGlobalTotals(data) {
    const sub = document.getElementById('cart-subtotal');
    const iva = document.getElementById('cart-iva');
    const tot = document.getElementById('cart-total');
    const itemsB = document.getElementById('items-count-badge');

    // Usamos nuestra nueva función formatearMoneda para que se vea en el idioma/divisa correctos
    if (sub) sub.innerText = formatearMoneda(data.new_subtotal);
    if (iva) iva.innerText = formatearMoneda(data.new_iva);
    if (tot) tot.innerText = formatearMoneda(data.new_total);
    
    if (itemsB) {
        // Extraemos la palabra "ítems" / "items" del badge para que no se pierda la traducción
        const words = itemsB.innerText.split(' ');
        const lastWord = words.length > 1 ? words[words.length - 1] : 'items';
        itemsB.innerText = `${data.cart_count} ${lastWord}`; 
    }

    updateNavbarCounter(data.cart_count);
}

function updateNavbarCounter(count) {
    const badge = document.getElementById('cart-count'); 
    if(badge) {
        badge.innerText = count;
        badge.classList.remove('animate-pulse');
        badge.classList.add('scale-150', 'bg-green-500'); 
        
        setTimeout(() => {
            badge.classList.remove('scale-150', 'bg-green-500');
            badge.classList.add('animate-pulse');
        }, 300);
    }
}

// =========================================================
// 5. SISTEMA DE TOASTS
// =========================================================
window.showToast = function(title, msg, type = 'success') {
    const container = document.getElementById('toast-container');
    if (!container) return; 

    const toast = document.createElement('div');
    
    let borderColor = 'border-green-500';
    let iconClass = 'fa-check-circle text-green-500';
    
    if (type === 'error') {
        borderColor = 'border-red-500';
        iconClass = 'fa-trash-alt text-red-500';
    } else if (type === 'info') {
        borderColor = 'border-orange-500';
        iconClass = 'fa-info-circle text-orange-500';
    }

    toast.className = `bg-gray-900 text-white p-4 rounded-lg shadow-2xl border-l-4 ${borderColor} transform transition-all duration-300 translate-y-10 opacity-0 min-w-[250px] flex items-start gap-3 pointer-events-auto`;
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