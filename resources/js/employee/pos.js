// resources/js/employee/pos.js

// Estado global del carrito
let cart = [];

// --- 1. SISTEMA DE NOTIFICACIONES (TOASTS) ---
window.showToast = function(message, type = 'success') {
    const container = document.getElementById('toast-container');
    if(!container) return;

    const styles = {
        success: { bg: 'bg-green-600', icon: '<i class="fas fa-check-circle"></i>' },
        error:   { bg: 'bg-red-600',   icon: '<i class="fas fa-exclamation-circle"></i>' },
        info:    { bg: 'bg-blue-600',  icon: '<i class="fas fa-info-circle"></i>' }
    };

    const style = styles[type] || styles.success;

    const toast = document.createElement('div');
    // Usamos la clase animate-slideInRight definida en el CSS
    toast.className = `${style.bg} text-white px-6 py-3 md:py-4 rounded-lg shadow-2xl flex items-center gap-3 animate-slideInRight min-w-[300px] border border-white/10 backdrop-blur-sm pointer-events-auto mb-2`;
    toast.innerHTML = `
        <span class="text-xl">${style.icon}</span>
        <span class="font-bold text-sm">${message}</span>
    `;

    container.appendChild(toast);

    setTimeout(() => {
        toast.style.transition = 'all 0.5s ease-in';
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(100%)';
        setTimeout(() => toast.remove(), 500);
    }, 3000);
};


// --- 2. LÓGICA DEL CARRITO ---

window.addToCart = function(id, name, price, img) {
    const existingItem = cart.find(item => item.id === id);

    if (existingItem) {
        existingItem.qty++;
        showToast(`Agregaste otra ${name}`, 'info');
    } else {
        cart.push({ id, name, price, img, qty: 1 });
        showToast(`${name} agregado`, 'success');
    }
    updateCartUI();
};

window.changeQty = function(id, change) {
    const item = cart.find(item => item.id === id);
    if (item) {
        item.qty += change;
        if (item.qty <= 0) cart = cart.filter(i => i.id !== id);
        updateCartUI();
    }
};

window.updateCartUI = function() {
    const container = document.getElementById('cart-items');
    const totalEl = document.getElementById('cart-total');
    const btnPagar = document.getElementById('btn-pagar');

    // 1. Limpieza inicial
    container.innerHTML = '';
    let total = 0;

    // 2. Estado Vacío
    if (cart.length === 0) {
        container.innerHTML = `
            <div class="text-center text-slate-500 mt-20 flex flex-col items-center animate-fadeIn">
                <div class="w-20 h-20 bg-slate-800 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-basket-shopping text-3xl opacity-20 text-white"></i>
                </div>
                <p class="text-sm font-medium">Ticket vacío</p>
                <p class="text-xs text-slate-600">Agrega productos del menú</p>
            </div>
        `;
        
        btnPagar.disabled = true;
        totalEl.innerText = '$0.00';
        return;
    }

    // 3. Renderizar Productos
    btnPagar.disabled = false;

    cart.forEach(item => {
        total += item.price * item.qty;
        
        const itemDiv = document.createElement('div');
        itemDiv.className = 'flex gap-3 bg-slate-800 p-3 rounded-xl border border-white/5 items-center animate-fadeIn group hover:border-orange-500/30 transition-colors mb-2';
        
        itemDiv.innerHTML = `
            <div class="w-12 h-12 rounded-lg overflow-hidden shrink-0 border border-slate-700 relative">
                <img src="${item.img}" class="w-full h-full object-cover">
            </div>
            
            <div class="flex-1 min-w-0">
                <div class="text-white text-sm font-bold truncate leading-tight">${item.name}</div>
                <div class="text-slate-400 text-xs mt-1">$${item.price.toFixed(2)} c/u</div>
            </div>
            
            <div class="flex items-center gap-2 bg-slate-900 rounded-lg p-1 border border-slate-700">
                <button onclick="changeQty(${item.id}, -1)" class="w-6 h-6 rounded bg-slate-800 text-slate-300 hover:bg-red-500 hover:text-white flex items-center justify-center text-xs transition-colors">
                    <i class="fas fa-minus"></i>
                </button>
                <span class="text-white font-bold text-sm w-5 text-center select-none">${item.qty}</span>
                <button onclick="changeQty(${item.id}, 1)" class="w-6 h-6 rounded bg-slate-800 text-slate-300 hover:bg-blue-500 hover:text-white flex items-center justify-center text-xs transition-colors">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
        `;
        container.appendChild(itemDiv);
    });

    const formattedTotal = total.toFixed(2);
    totalEl.innerText = '$' + formattedTotal;
};


// --- 3. ENVÍO DE ORDEN ---

window.submitOrder = async function() {
    const btn = document.getElementById('btn-pagar');
    
    // Lectura de datos
    const mesaEl = document.getElementById('pos-mesa'); 
    const clienteEl = document.getElementById('pos-cliente');
    
    // Lectura de método de pago (Radio buttons)
    const pagoEl = document.querySelector('input[name="metodo_pago_pos"]:checked');
    const metodoPago = pagoEl ? pagoEl.value : 'efectivo';

    if (cart.length === 0) return;

    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Procesando...';

    try {
        const tokenMeta = document.querySelector('meta[name="csrf-token"]');
        if(!tokenMeta) throw new Error("Falta el token CSRF");
        const token = tokenMeta.getAttribute('content');

        const response = await fetch('/empleado/comandera/store', { 
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': token
            },
            body: JSON.stringify({
                items: cart,
                mesa: mesaEl ? mesaEl.value : null,
                cliente: clienteEl ? clienteEl.value : '', 
                total: cart.reduce((acc, item) => acc + (item.price * item.qty), 0),
                metodo_pago: metodoPago
            })
        });

        const result = await response.json();

        if (result.success) {
            showToast(`¡Orden #${result.order_id} enviada!`, 'success');
            
            // ABRIR TICKET
            const width = 400;
            const height = 600;
            const left = (screen.width - width) / 2;
            const top = (screen.height - height) / 2;
            
            window.open(
                `/empleado/ticket/${result.order_id}`, 
                'Ticket', 
                `width=${width},height=${height},top=${top},left=${left}`
            );

            // Limpiar todo
            cart = []; 
            if(mesaEl) mesaEl.value = ""; 
            if(clienteEl) clienteEl.value = ""; 
            updateCartUI();

        } else {
            showToast('Error: ' + result.message, 'error');
        }

    } catch (error) {
        console.error(error);
        showToast('Error de conexión', 'error');
    } finally {
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = originalText;
            if(cart.length === 0) btn.disabled = true;
        }
    }
};

// --- 4. BUSCADOR ---
document.addEventListener('DOMContentLoaded', () => {
    // Inicializar UI vacía
    updateCartUI();
    
    // Lógica del buscador
    const buscador = document.getElementById('buscador-pos');
    if (buscador) {
        buscador.addEventListener('input', (e) => {
            const texto = e.target.value.toLowerCase();
            const productos = document.querySelectorAll('.product-item');
            productos.forEach(prod => {
                const nombre = prod.dataset.nombre; 
                if (nombre.includes(texto)) prod.style.display = 'flex'; 
                else prod.style.display = 'none';
            });
        });
    }
    
    // Manejo del menú responsive (toggleCartMobile si lo usas)
    window.toggleCartMobile = function() {
        // Implementar si necesitas mostrar/ocultar carrito en móvil
        console.log("Toggle cart mobile");
    }
});