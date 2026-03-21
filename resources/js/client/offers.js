document.addEventListener('DOMContentLoaded', () => {
    const tarjetas = document.querySelectorAll('.oferta-card');
    const modal = document.getElementById('modal-oferta');
    const panel = document.getElementById('modal-oferta-panel');
    const btnCerrar = document.getElementById('btn-cerrar-modal');
    const overlay = document.getElementById('modal-overlay');
    
    let countdownInterval = null; 

    // ==========================================
    // FUNCIÓN AUXILIAR DE MONEDA INTERNACIONAL
    // ==========================================
    const formatearMoneda = (cantidadEnPesos) => {
        const rate = window.MENU_LANG?.exchangeRate || 1;
        const symbol = window.MENU_LANG?.currencySymbol || '$';
        const code = window.MENU_LANG?.currencyCode || '';
        
        const cantidadConvertida = (cantidadEnPesos / rate).toFixed(2);
        return `${symbol}${cantidadConvertida}${code}`;
    };

    const abrirModal = (tarjeta) => {
        const titulo = tarjeta.dataset.titulo;
        const desc = tarjeta.dataset.desc;
        const imgUrl = tarjeta.dataset.img;
        const fechaFinIso = tarjeta.dataset.finIso;
        const porcentaje = parseFloat(tarjeta.dataset.porcentaje);
        const precio = parseFloat(tarjeta.dataset.precio);
        const tipo = tarjeta.dataset.tipo;
        const ref = tarjeta.dataset.ref;
        const refTraducida = tarjeta.dataset.refTraducida || ref;

        document.getElementById('modal-titulo').innerText = titulo;
        document.getElementById('modal-desc').innerText = desc || '...';

        const imgEl = document.getElementById('modal-img');
        const noImgEl = document.getElementById('modal-no-img');
        if (imgUrl) {
            imgEl.src = imgUrl;
            imgEl.classList.remove('hidden');
            noImgEl.classList.add('hidden');
        } else {
            imgEl.classList.add('hidden');
            noImgEl.classList.remove('hidden');
        }

        const btnPedir = document.getElementById('modal-btn-pedir');
        let etiquetaHtml = '';
        
        // Las categorías no se pueden traducir tan fácil porque vienen de la base de datos, 
        // pero los prefijos sí. Usaremos iconos para que sea universal.
        if (tipo === 'categoria') {
            etiquetaHtml = `<span class="bg-purple-600 text-white text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-wider shadow-lg border border-purple-400"><i class="fas fa-layer-group mr-1"></i> ${refTraducida}</span>`;
            btnPedir.href = `/menu?categoria=${encodeURIComponent(ref)}`; // Mantiene el ref original para la URL
        } else if (tipo === 'producto') {
            etiquetaHtml = `<span class="bg-blue-600 text-white text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-wider shadow-lg border border-blue-400"><i class="fas fa-hamburger mr-1"></i> Product</span>`;
            btnPedir.href = `/menu`;
        } else {
            etiquetaHtml = `<span class="bg-emerald-600 text-white text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-wider shadow-lg border border-emerald-400"><i class="fas fa-globe mr-1"></i> Global</span>`;
            btnPedir.href = `/menu`;
        }
        document.getElementById('modal-etiqueta-aplicacion').innerHTML = etiquetaHtml;

        let badgeHtml = '';
        if (porcentaje > 0) {
            badgeHtml = `<div class="bg-red-600 text-white font-black px-5 py-2 rounded-2xl shadow-2xl rotate-[-3deg] border-2 border-red-400/50 text-2xl drop-shadow-lg">-${porcentaje}%</div>`;
        } else if (precio > 0) {
            // AQUÍ APLICAMOS LA MAGIA MULTIMONEDA PARA EL PRECIO FIJO
            badgeHtml = `<div class="bg-green-600 text-white font-black px-5 py-2 rounded-2xl shadow-2xl rotate-[-3deg] border-2 border-green-400/50 text-2xl drop-shadow-lg">${formatearMoneda(precio)}</div>`;
        }
        document.getElementById('modal-badge-container').innerHTML = badgeHtml;

        iniciarContador(fechaFinIso);

        modal.classList.remove('hidden');
        setTimeout(() => {
            panel.classList.remove('scale-95', 'opacity-0');
            panel.classList.add('scale-100', 'opacity-100');
        }, 10);
        document.body.style.overflow = 'hidden';
    };

    const iniciarContador = (fechaLimiteIso) => {
        if (countdownInterval) clearInterval(countdownInterval);

        const fechaLimite = new Date(fechaLimiteIso).getTime();

        const actualizarReloj = () => {
            const ahora = new Date().getTime();
            const diferencia = fechaLimite - ahora;

            if (diferencia <= 0) {
                clearInterval(countdownInterval);
                document.getElementById('count-days').innerText = "00";
                document.getElementById('count-hours').innerText = "00";
                document.getElementById('count-mins').innerText = "00";
                document.getElementById('count-secs').innerText = "00";
                return;
            }

            const dias = Math.floor(diferencia / (1000 * 60 * 60 * 24));
            const horas = Math.floor((diferencia % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutos = Math.floor((diferencia % (1000 * 60 * 60)) / (1000 * 60));
            const segundos = Math.floor((diferencia % (1000 * 60)) / 1000);

            document.getElementById('count-days').innerText = dias.toString().padStart(2, '0');
            document.getElementById('count-hours').innerText = horas.toString().padStart(2, '0');
            document.getElementById('count-mins').innerText = minutos.toString().padStart(2, '0');
            document.getElementById('count-secs').innerText = segundos.toString().padStart(2, '0');
        };

        actualizarReloj();
        countdownInterval = setInterval(actualizarReloj, 1000);
    };

    const cerrarModal = () => {
        panel.classList.remove('scale-100', 'opacity-100');
        panel.classList.add('scale-95', 'opacity-0');
        
        if (countdownInterval) clearInterval(countdownInterval);

        setTimeout(() => {
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }, 300);
    };

    tarjetas.forEach(tarjeta => {
        tarjeta.addEventListener('click', () => abrirModal(tarjeta));
    });

    if (btnCerrar) btnCerrar.addEventListener('click', cerrarModal);
    if (overlay) overlay.addEventListener('click', cerrarModal);
});