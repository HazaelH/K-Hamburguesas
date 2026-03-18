// --- VARIABLES GLOBALES ---
let alertasGlobales = [];
let currentAlertOrderId = null;
const LANG = window.K_ADMIN_CONFIG ? window.K_ADMIN_CONFIG.lang : {};

// --- FUNCIONES ACCESIBLES GLOBALMENTE ---
window.toggleCentroNotificaciones = function() {
    const dropdown = document.getElementById('notificaciones-dropdown');
    const badge = document.getElementById('admin-alert-badge');
    
    if (badge.classList.contains('hidden') || badge.innerText === '0') {
        window.mostrarNotificacionVacia();
        dropdown.classList.add('hidden');
        return;
    }

    if (dropdown.classList.contains('hidden')) {
        dropdown.classList.remove('hidden');
        setTimeout(() => dropdown.classList.remove('scale-95', 'opacity-0'), 10);
    } else {
        dropdown.classList.add('scale-95', 'opacity-0');
        setTimeout(() => dropdown.classList.add('hidden'), 200);
    }
};

window.mostrarNotificacionVacia = function() {
    const toast = document.createElement('div');
    toast.className = 'fixed bottom-5 right-5 bg-slate-800 border border-slate-700 text-white px-6 py-4 rounded-xl shadow-2xl z-[150] flex items-center gap-3 transform transition-all duration-300 translate-y-20 opacity-0';
    toast.innerHTML = `<i class="fas fa-info-circle text-blue-400 text-xl"></i> <div><h4 class="font-bold text-sm">${LANG.all_good_title}</h4><p class="text-xs text-slate-400">${LANG.all_good_desc}</p></div>`;
    
    document.body.appendChild(toast);
    setTimeout(() => toast.classList.remove('translate-y-20', 'opacity-0'), 10);
    setTimeout(() => {
        toast.classList.add('translate-y-20', 'opacity-0');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
};

window.cerrarModalAdmin = function() {
    const modal = document.getElementById('admin-auth-modal');
    const panel = document.getElementById('admin-auth-panel');
    panel.classList.add('opacity-0', 'scale-95');
    setTimeout(() => { 
        modal.classList.add('hidden');
        document.getElementById('admin-pin-input').value = '';
        const errorElement = document.getElementById('admin-pin-error');
        if(errorElement) errorElement.classList.add('hidden');
        
        // AGREGAR ESTA LÍNEA PARA LIMPIAR LA CAJA ROJA
        const motivoBox = document.getElementById('admin-auth-motivo-box');
        if(motivoBox) motivoBox.classList.add('hidden');
    }, 300);
};

// =========================================================================
// GESTIÓN DE MODALES 
// =========================================================================
window.abrirModalResolucion = function(index) {
    const alerta = alertasGlobales[index];
    window.toggleCentroNotificaciones(); 

    if(alerta.tipo === 'cancelacion') {
        currentAlertOrderId = alerta.id;
        
        // --- NUEVA LÓGICA: SEPARAR EL TÍTULO DEL MOTIVO ---
        let mensajePrincipal = alerta.mensaje;
        let motivoExtra = null;
        
        // Verificamos si el mensaje trae el texto ' - Motivo: "'
        if (alerta.mensaje.includes(' - Motivo: "')) {
            const partes = alerta.mensaje.split(' - Motivo: "');
            mensajePrincipal = partes[0]; // "El cajero solicita cancelar la orden #50"
            motivoExtra = partes[1].replace('"', ''); // "El cliente se fue"
        }

        // Llenar el modal
        document.getElementById('admin-auth-order').innerText = mensajePrincipal;
        document.getElementById('admin-pin-input').classList.remove('hidden');
        document.getElementById('admin-auth-panel').querySelector('h3').innerHTML = `<i class="fas fa-shield-alt text-red-500"></i> ${LANG.kitchen_request}`;
        
        // Manejar la caja roja del Motivo
        const motivoBox = document.getElementById('admin-auth-motivo-box');
        const motivoText = document.getElementById('admin-auth-mensaje');
        if (motivoBox && motivoText && motivoExtra) {
            motivoText.innerText = `"${motivoExtra}"`;
            motivoBox.classList.remove('hidden');
        } else if (motivoBox) {
            motivoBox.classList.add('hidden');
        }

        document.getElementById('admin-auth-panel').querySelector('.flex.gap-3').innerHTML = `
            <button onclick="resolverAlerta('rechazar')" class="flex-1 bg-slate-800 hover:bg-slate-700 text-white font-bold py-3.5 rounded-xl transition-all">${LANG.reject}</button>
            <button onclick="resolverAlerta('aprobar')" class="flex-1 bg-red-600 hover:bg-red-500 text-white font-bold py-3.5 rounded-xl shadow-lg transition-all">${LANG.approve}</button>
        `;
        
        const modalCocina = document.getElementById('admin-auth-modal');
        const panelCocina = document.getElementById('admin-auth-panel');
        modalCocina.classList.remove('hidden');
        setTimeout(() => { panelCocina.classList.remove('opacity-0', 'scale-95'); }, 10);
        
    } else if (alerta.tipo === 'stock') {
        // ... (El código de stock se queda exactamente igual) ...
        document.getElementById('admin-auth-order').innerText = alerta.mensaje;
        document.getElementById('admin-pin-input').classList.add('hidden');
        document.getElementById('admin-auth-panel').querySelector('h3').innerHTML = `<i class="fas fa-boxes text-amber-500"></i> ${LANG.stock_request}`;
        
        // Ocultar caja de motivo si estaba abierta de otra alerta
        const motivoBox = document.getElementById('admin-auth-motivo-box');
        if (motivoBox) motivoBox.classList.add('hidden');
        
        document.getElementById('admin-auth-panel').querySelector('.flex.gap-3').innerHTML = `
            <button onclick="resolverStock(${alerta.id}, 'rechazar')" class="flex-1 bg-slate-800 hover:bg-slate-700 text-white font-bold py-3.5 rounded-xl transition-all">${LANG.reject}</button>
            <button onclick="resolverStock(${alerta.id}, 'aprobar')" class="flex-1 bg-amber-500 hover:bg-amber-400 text-slate-900 font-bold py-3.5 rounded-xl shadow-lg transition-all">${LANG.approve_change}</button>
        `;
        
        const modalStock = document.getElementById('admin-auth-modal');
        const panelStock = document.getElementById('admin-auth-panel');
        modalStock.classList.remove('hidden');
        setTimeout(() => { panelStock.classList.remove('opacity-0', 'scale-95'); }, 10);
        
    } else if (alerta.tipo === 'sos_repartidor') {
        // ... (El código SOS se queda exactamente igual) ...
        currentAlertOrderId = alerta.id;
        document.getElementById('admin-sos-order').innerText = `#${alerta.id}`;
        
        const mensajeLimpio = alerta.mensaje.split('): ')[1] || alerta.mensaje;
        document.getElementById('admin-sos-mensaje').innerText = `"${mensajeLimpio}"`;
        document.getElementById('admin-sos-respuesta').value = '';
        
        const modalSos = document.getElementById('admin-sos-modal');
        const panelSos = document.getElementById('admin-sos-panel');
        
        if(modalSos && panelSos) {
            modalSos.classList.remove('hidden');
            setTimeout(() => { panelSos.classList.remove('opacity-0', 'scale-95'); }, 10);
        } else {
            console.error("Falta pegar el HTML del admin-sos-modal en tu archivo admin.blade.php");
        }
    }
};


window.cerrarModalSOSAdmin = function() {
    const modal = document.getElementById('admin-sos-modal');
    const panel = document.getElementById('admin-sos-panel');
    panel.classList.add('opacity-0', 'scale-95');
    setTimeout(() => modal.classList.add('hidden'), 300);
};

window.enviarResolucionSOS = async function() {
    const btn = document.getElementById('btn-resolver-sos');
    const respuesta = document.getElementById('admin-sos-respuesta').value;
    const originalText = btn.innerHTML;

    if(!respuesta.trim()) {
        alert(LANG.empty_sos_reply);
        return;
    }

    btn.innerHTML = `<i class="fas fa-spinner fa-spin"></i> ${LANG.sending}`;
    btn.disabled = true;

    try {
        const token = document.querySelector('meta[name="csrf-token"]').content;
        const response = await fetch(`/admin/orden/${currentAlertOrderId}/resolver-sos`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': token },
            body: JSON.stringify({ respuesta: respuesta })
        });
        
        if (response.ok) {
            window.cerrarModalSOSAdmin();
            // ¡CORRECCIÓN UX! Actualizamos la campanita sin recargar la página
            setTimeout(() => { if(window.actualizarAlertasAdmin) window.actualizarAlertasAdmin(); }, 300); 
        } else {
            alert(LANG.error_sending);
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    } catch(e) {
        console.error(e);
        btn.innerHTML = originalText;
        btn.disabled = false;
    }
};

window.resolverStock = async function(id, decision) {
    const btn = event.target;
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    btn.disabled = true;

    try {
        const token = document.querySelector('meta[name="csrf-token"]').content;
        const response = await fetch(`/admin/api/alertas/stock/${id}/resolver`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': token },
            body: JSON.stringify({ decision: decision })
        });
        
        if (response.ok) {
            window.cerrarModalAdmin();
            // ¡CORRECCIÓN UX! Actualizamos la campanita sin recargar la página
            setTimeout(() => { if(window.actualizarAlertasAdmin) window.actualizarAlertasAdmin(); }, 300); 
        }
    } catch(e) {
        btn.innerHTML = originalText;
        btn.disabled = false;
    }
};

window.resolverAlerta = async function(accion) {
    const pinInput = document.getElementById('admin-pin-input');
    const pin = pinInput ? pinInput.value : '';
    
    if(accion === 'aprobar' && pin === '') {
        if(pinInput) pinInput.focus();
        return;
    }

    const btn = event.target;
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    btn.disabled = true;

    try {
        const token = document.querySelector('meta[name="csrf-token"]').content;
        const response = await fetch(`/admin/orden/${currentAlertOrderId}/resolver-cancelacion`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': token },
            body: JSON.stringify({ accion: accion, pin: pin })
        });

        const data = await response.json();
        
        if (response.ok && data.success) {
            window.cerrarModalAdmin();
            // ¡CORRECCIÓN UX! Actualizamos la campanita sin recargar la página
            setTimeout(() => { if(window.actualizarAlertasAdmin) window.actualizarAlertasAdmin(); }, 300);
        } else {
            const errorElement = document.getElementById('admin-pin-error');
            if(errorElement) {
                errorElement.innerText = data.message || 'Error al validar';
                errorElement.classList.remove('hidden');
            }
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    } catch (e) {
        btn.innerHTML = originalText;
        btn.disabled = false;
    }
};

// =========================================================================
// MOTOR DE ALERTAS EN TIEMPO REAL (Extraído para poder llamarlo a voluntad)
// =========================================================================
window.actualizarAlertasAdmin = async function() {
    const checkAlertsUrl = window.K_ADMIN_CONFIG ? window.K_ADMIN_CONFIG.checkAlertsUrl : '/admin/api/alertas';
    try {
        const response = await fetch(checkAlertsUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        if(!response.ok) return;

        const data = await response.json();
        alertasGlobales = data.alertas || [];
        
        const badge = document.getElementById('admin-alert-badge');
        const countText = document.getElementById('notificaciones-count');
        const lista = document.getElementById('notificaciones-lista');

        if (alertasGlobales.length > 0) {
            badge.innerText = alertasGlobales.length;
            countText.innerText = alertasGlobales.length;
            badge.classList.remove('hidden');
            
            // Sonido solo si hay una notificación NUEVA que no estaba antes
            if (lista.children.length < alertasGlobales.length) {
                new Audio('https://actions.google.com/sounds/v1/alarms/digital_watch_alarm_long.ogg').play().catch(e=>{});
            }

            lista.innerHTML = '';
            alertasGlobales.forEach((alerta, index) => {
                let icon, title, bgHover;
                
                if (alerta.tipo === 'cancelacion') {
                    icon = '<i class="fas fa-shield-alt text-red-500"></i>';
                    title = LANG.alert_cancel;
                    bgHover = 'hover:bg-red-900/20';
                } else if (alerta.tipo === 'stock') {
                    icon = '<i class="fas fa-boxes text-amber-500"></i>';
                    title = LANG.alert_stock;
                    bgHover = 'hover:bg-amber-900/20';
                } else if (alerta.tipo === 'sos_repartidor') {
                    icon = '<i class="fas fa-motorcycle text-blue-500"></i>';
                    title = LANG.alert_sos;
                    bgHover = 'hover:bg-blue-900/20';
                }
                
                lista.innerHTML += `
                    <div onclick="abrirModalResolucion(${index})" class="p-4 border-b border-slate-700/50 cursor-pointer transition-colors flex gap-3 group ${bgHover}">
                        <div class="mt-1 bg-slate-900 p-2 rounded-lg h-8 w-8 flex items-center justify-center border border-slate-600 group-hover:border-slate-400">
                            ${icon}
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-white">${title}</h4>
                            <p class="text-xs text-slate-400 mt-1">${alerta.mensaje}</p>
                            ${alerta.tipo === 'sos_repartidor' ? `<p class="text-[10px] font-bold text-blue-400 mt-2 uppercase tracking-widest"><i class="fas fa-check"></i> ${LANG.click_to_resolve}</p>` : ''}
                        </div>
                    </div>
                `;
            });

        } else {
            badge.classList.add('hidden');
            lista.innerHTML = '';
            
            // Si procesaste la última notificación y ya no hay más, mostramos el globo de "Todo bien"
            const dropdown = document.getElementById('notificaciones-dropdown');
            if(dropdown && !dropdown.classList.contains('hidden')) {
                dropdown.classList.add('hidden');
                if(window.mostrarNotificacionVacia) window.mostrarNotificacionVacia();
            }
        }
    } catch (e) {}
};

document.addEventListener('DOMContentLoaded', () => {
    
    const toggleBtn = document.getElementById('sidebar-toggle');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    
    function toggleSidebar() { 
        sidebar.classList.toggle('-translate-x-full'); 
        overlay.classList.toggle('hidden'); 
    }
    
    if(toggleBtn) { 
        toggleBtn.addEventListener('click', toggleSidebar); 
        overlay.addEventListener('click', toggleSidebar); 
    }

    const checkAlertsUrl = window.K_ADMIN_CONFIG ? window.K_ADMIN_CONFIG.checkAlertsUrl : '/admin/api/alertas';

    setInterval(async () => {
        try {
            const response = await fetch(checkAlertsUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            if(!response.ok) return;

            const data = await response.json();
            alertasGlobales = data.alertas || [];
            
            const badge = document.getElementById('admin-alert-badge');
            const countText = document.getElementById('notificaciones-count');
            const lista = document.getElementById('notificaciones-lista');

            if (alertasGlobales.length > 0) {
                badge.innerText = alertasGlobales.length;
                countText.innerText = alertasGlobales.length;
                badge.classList.remove('hidden');
                
                if (lista.children.length < alertasGlobales.length) {
                    new Audio('https://actions.google.com/sounds/v1/alarms/digital_watch_alarm_long.ogg').play().catch(e=>{});
                }

                lista.innerHTML = '';
                alertasGlobales.forEach((alerta, index) => {
                    let icon, title, bgHover;
                    
                    if (alerta.tipo === 'cancelacion') {
                        icon = '<i class="fas fa-shield-alt text-red-500"></i>';
                        title = LANG.alert_cancel;
                        bgHover = 'hover:bg-red-900/20';
                    } else if (alerta.tipo === 'stock') {
                        icon = '<i class="fas fa-boxes text-amber-500"></i>';
                        title = LANG.alert_stock;
                        bgHover = 'hover:bg-amber-900/20';
                    } else if (alerta.tipo === 'sos_repartidor') {
                        icon = '<i class="fas fa-motorcycle text-blue-500"></i>';
                        title = LANG.alert_sos;
                        bgHover = 'hover:bg-blue-900/20';
                    }
                    
                    lista.innerHTML += `
                        <div onclick="abrirModalResolucion(${index})" class="p-4 border-b border-slate-700/50 cursor-pointer transition-colors flex gap-3 group ${bgHover}">
                            <div class="mt-1 bg-slate-900 p-2 rounded-lg h-8 w-8 flex items-center justify-center border border-slate-600 group-hover:border-slate-400">
                                ${icon}
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-white">${title}</h4>
                                <p class="text-xs text-slate-400 mt-1">${alerta.mensaje}</p>
                                ${alerta.tipo === 'sos_repartidor' ? `<p class="text-[10px] font-bold text-blue-400 mt-2 uppercase tracking-widest"><i class="fas fa-check"></i> ${LANG.click_to_resolve}</p>` : ''}
                            </div>
                        </div>
                    `;
                });

            } else {
                badge.classList.add('hidden');
                lista.innerHTML = '';
                document.getElementById('notificaciones-dropdown').classList.add('hidden');
            }
        } catch (e) {}
    }, 4000);

    window.actualizarAlertasAdmin();
    setInterval(window.actualizarAlertasAdmin, 4000);

    const relojContenedor = document.getElementById('reloj-en-vivo');
    
    if (relojContenedor) {
        // Detecta el idioma activo desde Laravel para el formato del reloj
        const currentLocale = (window.K_ADMIN_CONFIG && window.K_ADMIN_CONFIG.locale === 'en') ? 'en-US' : 'es-ES';
        
        actualizarReloj(currentLocale);
        setInterval(() => actualizarReloj(currentLocale), 1000);
        
        function actualizarReloj(locale) {
            const fecha = new Date();
            const opcionesFecha = { weekday: 'long', day: '2-digit', month: 'long', year: 'numeric' };
            const opcionesHora = { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: locale === 'en-US' };
            
            const fechaFormateada = fecha.toLocaleDateString(locale, opcionesFecha);
            const horaFormateada = fecha.toLocaleTimeString(locale, opcionesHora);
            
            // Capitalizamos la primera letra
            const textoFinal = fechaFormateada.charAt(0).toUpperCase() + fechaFormateada.slice(1);
            relojContenedor.innerHTML = `${textoFinal} <span class="text-orange-500 mx-2">|</span> <span class="text-white font-bold">${horaFormateada}</span>`;
        }
    }
});