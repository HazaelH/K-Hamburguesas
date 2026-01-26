// Variable global para saber qué producto estamos editando
let currentProduct = null;
let currentQuantity = 1;

document.addEventListener('DOMContentLoaded', () => {
    // Inicializar listeners si es necesario
});

// ==========================================
// 1. LÓGICA DEL MODAL
// ==========================================

window.abrirModal = function(producto) {
    currentProduct = producto;
    currentQuantity = 1;

    // Llenar datos en el modal
    document.getElementById('modal-title').innerText = producto.nombre;
    document.getElementById('modal-desc').innerText = producto.descripcion;
    document.getElementById('modal-price').innerText = `$${parseFloat(producto.precio).toFixed(2)}`;
    document.getElementById('modal-img').src = `/imagenes/${producto.imagen_url}`; // Ajusta la ruta si usas 'storage/'
    
    // Resetear inputs
    document.getElementById('modal-notas').value = '';
    actualizarCantidadVisual();

    // Mostrar modal
    const modal = document.getElementById('product-modal');
    modal.classList.remove('hidden');
    
    // Animación de entrada
    const container = modal.querySelector('div.relative');
    container.classList.remove('scale-95', 'opacity-0');
    container.classList.add('scale-100', 'opacity-100');
};

window.cerrarModal = function() {
    const modal = document.getElementById('product-modal');
    const container = modal.querySelector('div.relative');
    
    // Animación de salida
    container.classList.remove('scale-100', 'opacity-100');
    container.classList.add('scale-95', 'opacity-0');

    setTimeout(() => {
        modal.classList.add('hidden');
        currentProduct = null;
    }, 200); // Esperar a que termine la transición css
};

// ==========================================
// 2. LÓGICA DE CANTIDAD
// ==========================================

window.cambiarCantidad = function(change) {
    const nuevaCantidad = currentQuantity + change;
    if (nuevaCantidad >= 1) {
        currentQuantity = nuevaCantidad;
        actualizarCantidadVisual();
    }
};

function actualizarCantidadVisual() {
    document.getElementById('cantidad-span').innerText = currentQuantity;
    
    // Calcular subtotal en el botón
    if (currentProduct) {
        const total = (parseFloat(currentProduct.precio) * currentQuantity).toFixed(2);
        document.getElementById('modal-total').innerText = `$${total}`;
    }
}

// ==========================================
// 3. AGREGAR AL CARRITO (AJAX)
// ==========================================

window.agregarAlCarrito = function() {
    if (!currentProduct) return;

    const notas = document.getElementById('modal-notas').value;
    const btnText = document.getElementById('btn-add-text');
    const originalText = btnText.innerText;

    // Feedback de carga
    btnText.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';

    // Preparamos los datos
    // Nota: Tu controlador espera 'modificaciones' como array.
    // Convertimos la nota de texto en un formato que el controlador acepte.
    const modificaciones = notas ? [{ grupo: 'Nota', valor: notas }] : [];

    fetch('/carrito/agregar', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            id_producto: currentProduct.id_producto, // Asegúrate que tu modelo JS tenga id_producto
            cantidad: currentQuantity,
            modificaciones: modificaciones
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'ok') {
            
            // A. ACTUALIZAR CONTADOR DEL NAVBAR (Aquí ocurre la magia)
            actualizarIconoCarrito(data.total_items);

            // B. Mostrar Notificación Toast
            mostrarToast(data.mensaje);

            // C. Cerrar Modal
            cerrarModal();

        } else {
            // Error controlado
            mostrarError('No se pudo agregar al carrito.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarError('Ocurrió un error inesperado.');
    })
    .finally(() => {
        btnText.innerText = originalText;
    });
};

// Función auxiliar para actualizar el numerito rojo
function actualizarIconoCarrito(cantidad) {
    const badge = document.getElementById('cart-count'); // Asegúrate que tu layout tenga este ID
    if (badge) {
        badge.innerText = cantidad;
        
        // Animación de rebote para llamar la atención
        badge.classList.remove('animate-pulse');
        badge.classList.add('scale-150', 'bg-green-500'); // Crece y se pone verde momentáneamente
        
        setTimeout(() => {
            badge.classList.remove('scale-150', 'bg-green-500');
            badge.classList.add('animate-pulse');
        }, 500);
    }
}

// ==========================================
// 4. NOTIFICACIONES (TOAST)
// ==========================================

function mostrarToast(mensaje) {
    const toast = document.getElementById('toast-notification');
    const msgElement = document.getElementById('toast-message');
    
    if (toast && msgElement) {
        msgElement.innerText = mensaje;
        toast.classList.remove('translate-y-24', 'opacity-0'); // Subir
        
        setTimeout(() => {
            toast.classList.add('translate-y-24', 'opacity-0'); // Bajar y ocultar
        }, 3000);
    }
}

function mostrarError(mensaje) {
    const toast = document.getElementById('toast-error'); // Asegúrate de tener este HTML o reutiliza el otro
    if (toast) {
        document.getElementById('toast-error-message').innerText = mensaje;
        toast.classList.remove('translate-y-24', 'opacity-0');
        setTimeout(() => {
            toast.classList.add('translate-y-24', 'opacity-0');
        }, 3000);
    } else {
        alert(mensaje);
    }
}

// ==========================================
// 5. FILTRADO (Categorías)
// ==========================================
window.filtrarCategoria = function(categoria) {
    // Cambiar estilos de botones
    document.querySelectorAll('.filter-btn').forEach(btn => {
        if(btn.dataset.category === categoria) {
            btn.classList.remove('bg-gray-800', 'text-gray-400');
            btn.classList.add('bg-orange-600', 'text-white', 'shadow-lg', 'ring-2', 'ring-orange-500');
        } else {
            btn.classList.add('bg-gray-800', 'text-gray-400');
            btn.classList.remove('bg-orange-600', 'text-white', 'shadow-lg', 'ring-2', 'ring-orange-500');
        }
    });

    // Filtrar tarjetas
    const cards = document.querySelectorAll('.product-card');
    cards.forEach(card => {
        if (categoria === 'Todas' || card.dataset.categoria === categoria) {
            card.style.display = 'flex'; // Usamos flex porque las tarjetas son flex containers
        } else {
            card.style.display = 'none';
        }
    });
};