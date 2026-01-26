import '../bootstrap';

document.addEventListener('DOMContentLoaded', () => {
    console.log('Módulo del Menú cargado');
});

window.cargarCategoria = function(categoria) {
    const grid = document.getElementById('contenedor-productos');
    if (!grid) return;

    grid.style.opacity = '0.5';

    fetch(`/api/categoria/${categoria}`)
        .then(res => res.json())
        .then(data => {
            grid.innerHTML = '';
            data.forEach(prod => {
                const img = prod.imagen_url || 'https://placehold.co/400x300';
                const precio = parseFloat(prod.precio).toFixed(2);
                
                const html = `
                    <div class="bg-black/40 border border-white/10 rounded-xl overflow-hidden hover:border-orange-500/50 transition duration-300 group">
                        <div class="relative h-48 overflow-hidden">
                            <img src="${img}" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                            <div class="absolute top-2 right-2 bg-orange-500 text-white text-xs font-bold px-2 py-1 rounded">$${precio}</div>
                        </div>
                        <div class="p-4">
                            <h3 class="font-bold text-lg mb-1 text-white">${prod.nombre}</h3>
                            <button onclick="abrirModal(${prod.id_producto})" class="w-full bg-white/10 hover:bg-orange-500 text-white py-2 rounded-lg font-bold mt-2 transition">
                                Personalizar
                            </button>
                        </div>
                    </div>`;
                grid.innerHTML += html;
            });
            grid.style.opacity = '1';
        })
        .catch(err => console.error('Error cargando categoría:', err));
};

window.abrirModal = function(id) {
    fetch(`/api/producto/${id}`)
        .then(res => res.json())
        .then(prod => {
            window.productoActualId = prod.id_producto;

            document.getElementById('modalNombre').innerText = prod.nombre;
            document.getElementById('modalDesc').innerText = prod.descripcion || 'Sin descripción';
            document.getElementById('modalPrecio').innerText = '$' + parseFloat(prod.precio).toFixed(2);
            document.getElementById('modalImg').src = prod.imagen_url || 'https://placehold.co/400x300';
            
            const container = document.getElementById('modalOpciones');
            container.innerHTML = '';

            if (prod.opciones && Object.keys(prod.opciones).length > 0) {
                Object.keys(prod.opciones).forEach(grupo => {
                    const titulo = grupo.charAt(0).toUpperCase() + grupo.slice(1);
                    let htmlGrupo = `
                        <div class="mb-4">
                            <h4 class="text-orange-400 font-bold mb-2 text-sm uppercase tracking-wide border-b border-gray-700 pb-1">${titulo}</h4>
                            <div class="grid grid-cols-2 gap-2">`;
                    
                    prod.opciones[grupo].forEach(opcion => {
                        htmlGrupo += `
                            <label class="flex items-center space-x-2 cursor-pointer bg-gray-800 p-2 rounded hover:bg-gray-700 transition select-none">
                                <input type="checkbox" class="form-checkbox text-orange-500 rounded focus:ring-0 bg-gray-900 border-gray-600" 
                                       value="${opcion}" data-grupo="${grupo}">
                                <span class="text-sm text-gray-200">${opcion}</span>
                            </label>`;
                    });
                    htmlGrupo += `</div></div>`;
                    container.innerHTML += htmlGrupo;
                });
            } else {
                container.innerHTML = '<p class="text-gray-500 italic text-center py-4">Este producto no requiere personalización.</p>';
            }

            const modal = document.getElementById('modalProducto');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        });
};

window.cerrarModal = function() {
    const modal = document.getElementById('modalProducto');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
};

window.confirmarAgregar = function() {
    const tokenMeta = document.querySelector('meta[name="csrf-token"]');
    const token = tokenMeta ? tokenMeta.getAttribute('content') : '';

    const opciones = [];
    document.querySelectorAll('#modalOpciones input:checked').forEach(chk => {
        opciones.push({
            grupo: chk.dataset.grupo,
            valor: chk.value
        });
    });

    fetch('/carrito/agregar', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token
        },
        body: JSON.stringify({
            id_producto: window.productoActualId,
            modificaciones: opciones
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'ok') {
            alert('¡' + data.mensaje + '!');
            cerrarModal();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Hubo un error al agregar el producto.');
    });
};