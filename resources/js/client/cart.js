document.addEventListener('DOMContentLoaded', () => {
    
    // 1. Escuchar clics en botones de "Agregar al Carrito"
    // Asumiremos que tus botones tendrán la clase '.add-to-cart-btn'
    const addButtons = document.querySelectorAll('.add-to-cart-btn');

    addButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault(); // Evitar que el formulario recargue la página

            const form = this.closest('form');
            const formData = new FormData(form);
            const productId = formData.get('product_id'); // Asegúrate que tu form tenga este input
            
            // Efecto visual de "Cargando" en el botón
            const originalIcon = this.innerHTML;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            this.disabled = true;

            // Petición AJAX a Laravel
            fetch('/carrito/agregar', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json', // Importante para que Laravel sepa que queremos JSON
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // ACTUALIZAR EL CONTADOR DEL NAVBAR
                    updateCartCounter(data.cartCount);
                    
                    // Feedback visual (opcional: Toast o alerta suave)
                    // alert('Producto agregado'); 
                } else {
                    alert('Hubo un error al agregar el producto');
                }
            })
            .catch(error => console.error('Error:', error))
            .finally(() => {
                // Restaurar botón
                this.innerHTML = originalIcon;
                this.disabled = false;
            });
        });
    });

    // 2. Función para animar y actualizar el contador
    function updateCartCounter(count) {
        const badge = document.getElementById('cart-count');
        if(badge) {
            badge.innerText = count;
            
            // Animación de rebote
            badge.classList.remove('animate-pulse'); // Quitamos la animación base
            badge.classList.add('scale-150', 'bg-green-500'); // Efecto visual
            
            setTimeout(() => {
                badge.classList.remove('scale-150', 'bg-green-500');
                badge.classList.add('animate-pulse'); // Volvemos a la base
            }, 300);
        }
    }
});