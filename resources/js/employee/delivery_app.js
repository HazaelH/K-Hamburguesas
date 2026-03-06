document.addEventListener('DOMContentLoaded', function() {
    
    let ordenesConocidas = window.APP_CONFIG.ordenesConocidas || [];
    let respuestasMostradas = []; 
    let idRespuestaActual = null; 
    
    const sonidoNotificacion = new Audio('https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3');
    const sonidoMensaje = new Audio('https://assets.mixkit.co/active_storage/sfx/238/238-preview.mp3'); 

    // Función auxiliar para Toasts elegantes en móvil
    const showDeliveryToast = (iconHtml, message, isError = false) => {
        const bgClass = isError ? 'bg-red-600' : 'bg-emerald-600';
        const toast = document.createElement('div');
        toast.className = `fixed top-20 right-4 lg:right-8 ${bgClass} text-white px-6 py-4 rounded-xl font-bold shadow-2xl z-[300] flex items-center gap-3 transform transition-all duration-300 translate-x-full opacity-0 pointer-events-none`;
        toast.innerHTML = `${iconHtml} <span class="text-sm">${message}</span>`;
        
        document.body.appendChild(toast);
        requestAnimationFrame(() => {
            toast.classList.remove('translate-x-full', 'opacity-0');
            toast.classList.add('translate-x-0', 'opacity-100');
        });

        setTimeout(() => { 
            toast.classList.remove('translate-x-0', 'opacity-100');
            toast.classList.add('translate-x-full', 'opacity-0');
            setTimeout(() => toast.remove(), 300);
        }, 3500);
    };

    setInterval(() => {
        fetch(window.APP_CONFIG.rutas.radarNuevos)
            .then(res => res.json())
            .then(data => {
                if(!data) return;

                if(data.ordenes) {
                    const hayNuevas = data.ordenes.some(id => !ordenesConocidas.includes(id));
                    if (hayNuevas) {
                        sonidoNotificacion.play().catch(e => {}); 
                        const panelProcesando = document.getElementById('dialogo-procesando');
                        if(panelProcesando) {
                            panelProcesando.classList.remove('hidden');
                            panelProcesando.querySelector('h3').innerText = window.DELIVERY_LANG.new_order_title;
                            panelProcesando.querySelector('p').innerText = window.DELIVERY_LANG.new_order_desc;
                        }
                        setTimeout(() => window.location.reload(), 2500);
                    }
                }

                if(data.respuestas_sos && data.respuestas_sos.length > 0) {
                    data.respuestas_sos.forEach(resp => {
                        if (!respuestasMostradas.includes(resp.id)) {
                            respuestasMostradas.push(resp.id);
                            sonidoMensaje.play().catch(e => {});
                            
                            idRespuestaActual = resp.id; 
                            
                            document.getElementById('respuesta-order-id').innerText = resp.id;
                            document.getElementById('respuesta-mensaje').innerText = `"${resp.respuesta}"`;
                            
                            const modal = document.getElementById('modal-respuesta-admin');
                            const panel = document.getElementById('panel-respuesta-admin');
                            modal.classList.remove('hidden');
                            setTimeout(() => { modal.classList.remove('opacity-0'); panel.classList.remove('scale-95'); panel.classList.add('scale-100'); }, 10);
                        }
                    });
                }
            })
            .catch(err => console.log("Radar en espera..."));
    }, 8000); 

    window.tomarViaje = function(id) {
        const btn = document.getElementById(`btn-tomar-${id}`);
        const textoOriginal = btn.innerHTML;
        btn.innerHTML = `<i class="fas fa-spinner fa-spin"></i> ${window.DELIVERY_LANG.assigning}`;
        btn.disabled = true;

        fetch(window.APP_CONFIG.rutas.tomarPedido(id), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.APP_CONFIG.csrfToken
            }
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                window.location.reload(); 
            } else {
                showDeliveryToast('<i class="fas fa-exclamation-triangle text-xl"></i>', data.message, true);
                setTimeout(() => { window.location.reload(); }, 2000);
            }
        })
        .catch(err => {
            showDeliveryToast('<i class="fas fa-wifi text-xl"></i>', window.DELIVERY_LANG.conn_error, true);
            btn.innerHTML = textoOriginal;
            btn.disabled = false;
        });
    };

    let sosOrdenActual = null;

    window.abrirModalSOS = function(id) {
        sosOrdenActual = id;
        document.getElementById('sos-order-id').innerText = id;
        document.getElementById('sos-mensaje').value = '';
        
        const modal = document.getElementById('modal-sos');
        const panel = document.getElementById('panel-sos');
        modal.classList.remove('hidden');
        setTimeout(() => { modal.classList.remove('opacity-0'); panel.classList.remove('scale-95'); panel.classList.add('scale-100'); }, 10);
    };

    window.cerrarModalSOS = function() {
        const modal = document.getElementById('modal-sos');
        const panel = document.getElementById('panel-sos');
        modal.classList.add('opacity-0'); panel.classList.remove('scale-100'); panel.classList.add('scale-95');
        setTimeout(() => modal.classList.add('hidden'), 300);
    };
    
    window.cerrarModalRespuestaAdmin = function() {
        const modal = document.getElementById('modal-respuesta-admin');
        const panel = document.getElementById('panel-respuesta-admin');
        modal.classList.add('opacity-0'); panel.classList.remove('scale-100'); panel.classList.add('scale-95');
        setTimeout(() => modal.classList.add('hidden'), 300);

        if (idRespuestaActual) {
            fetch(window.APP_CONFIG.rutas.marcarLeido(idRespuestaActual), {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json', 
                    'X-CSRF-TOKEN': window.APP_CONFIG.csrfToken 
                }
            }).catch(err => console.log("Radar: No se pudo marcar como leído."));
            
            idRespuestaActual = null; 
        }
    };

    window.enviarSOS = function() {
        const mensaje = document.getElementById('sos-mensaje').value;
        if(!mensaje.trim()) { alert(window.DELIVERY_LANG.sos_empty); return; }

        const btn = document.getElementById('btn-enviar-sos');
        const textoOriginal = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        btn.disabled = true;

        fetch(window.APP_CONFIG.rutas.enviarSOS(sosOrdenActual), {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json', 
                'X-CSRF-TOKEN': window.APP_CONFIG.csrfToken 
            },
            body: JSON.stringify({ mensaje: mensaje })
        })
        .then(res => res.json())
        .then(data => {
            window.cerrarModalSOS();
            showDeliveryToast('<i class="fas fa-check-circle text-xl"></i>', window.DELIVERY_LANG.sos_sent, false);
            btn.innerHTML = textoOriginal; btn.disabled = false;
        })
        .catch(err => {
            alert(window.DELIVERY_LANG.conn_error);
            btn.innerHTML = textoOriginal; btn.disabled = false;
        });
    };

    window.avisarCliente = function(id, estado, btn) {
        const textoOriginal = btn.innerHTML;
        btn.innerHTML = `<i class="fas fa-spinner fa-spin"></i> ${window.DELIVERY_LANG.notifying}`;
        btn.disabled = true;

        fetch(`/empleado/repartidor/orden/${id}/notificar-cliente`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.APP_CONFIG.csrfToken
            },
            body: JSON.stringify({ estado: estado })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                btn.classList.remove('bg-indigo-600/20', 'bg-pink-600/20', 'text-indigo-400', 'text-pink-400');
                btn.classList.add('bg-emerald-600', 'text-white', 'border-emerald-500');
                btn.innerHTML = `<i class="fas fa-check"></i> ${window.DELIVERY_LANG.notified}`;
                
                showDeliveryToast('<i class="fas fa-paper-plane text-xl"></i>', window.DELIVERY_LANG.customer_notified, false);
            }
        })
        .catch(err => {
            btn.innerHTML = textoOriginal;
            btn.disabled = false;
        });
    };
});