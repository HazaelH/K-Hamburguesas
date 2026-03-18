document.addEventListener('DOMContentLoaded', () => {
    
    const dataDiv = document.getElementById('dashboard-data');
    if (!dataDiv) return;

    // Variables globales para guardar las gráficas y poder actualizarlas
    let chartArea, chartPie, chartPeak;

    // ==========================================
    // 1. INICIALIZAR GRÁFICA DE LÍNEAS
    // ==========================================
    const lineLabels = JSON.parse(dataDiv.dataset.lineLabels);
    const lineValues = JSON.parse(dataDiv.dataset.lineValues);
    const ctxLine = document.getElementById('myAreaChart');

    if(ctxLine) {
        let gradientLine = ctxLine.getContext('2d').createLinearGradient(0, 0, 0, 400);
        gradientLine.addColorStop(0, 'rgba(16, 185, 129, 0.4)'); 
        gradientLine.addColorStop(1, 'rgba(16, 185, 129, 0.0)'); 

        chartArea = new Chart(ctxLine, {
            type: 'line',
            data: {
                labels: lineLabels,
                datasets: [{
                    label: window.ADMIN_LANG.revenue_mxn,
                    data: lineValues,
                    backgroundColor: gradientLine,
                    borderColor: '#10b981', 
                    borderWidth: 3,
                    pointBackgroundColor: '#1e293b', 
                    pointBorderColor: '#10b981',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.4 
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { color: '#64748b', font: {size: 11} } },
                    y: { grid: { color: '#334155', borderDash: [5, 5] }, ticks: { color: '#64748b', font: {size: 11} }, beginAtZero: true }
                }
            }
        });
    }

    // ==========================================
    // 2. INICIALIZAR GRÁFICA DE BARRAS
    // ==========================================
    const peakCtx = document.getElementById("peakHoursChart");
    if (peakCtx) {
        let barLabels = JSON.parse(dataDiv.dataset.barLabels || '[]');
        let barValues = JSON.parse(dataDiv.dataset.barValues || '[]');

        const gradientBlue = peakCtx.getContext('2d').createLinearGradient(0, 0, 0, 400);
        gradientBlue.addColorStop(0, 'rgba(59, 130, 246, 0.8)');
        gradientBlue.addColorStop(1, 'rgba(59, 130, 246, 0.2)');

        chartPeak = new Chart(peakCtx, {
            type: "bar",
            data: {
                labels: barLabels,
                datasets: [{
                    label: window.ADMIN_LANG.orders,
                    backgroundColor: gradientBlue,
                    hoverBackgroundColor: "rgba(59, 130, 246, 1)",
                    borderColor: "rgba(59, 130, 246, 1)",
                    borderWidth: 1,
                    borderRadius: 4,
                    data: barValues,
                }]
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { color: "#9ca3af", font: { size: 10 } } },
                    y: { beginAtZero: true, grid: { color: "rgba(255, 255, 255, 0.05)" }, ticks: { color: "#9ca3af", stepSize: 1 } }
                }
            }
        });
    }

    // ==========================================
    // 3. INICIALIZAR GRÁFICA DE DONA
    // ==========================================
    const pieLabels = JSON.parse(dataDiv.dataset.pieLabels);
    const pieValues = JSON.parse(dataDiv.dataset.pieValues);
    const ctxPie = document.getElementById('topProductsChart');

    if(ctxPie) {
        chartPie = new Chart(ctxPie, {
            type: 'doughnut',
            data: {
                labels: pieLabels,
                datasets: [{
                    data: pieValues,
                    backgroundColor: ['#f97316', '#eab308', '#3b82f6', '#8b5cf6', '#10b981'],
                    borderColor: '#1e293b', 
                    borderWidth: 4,
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'right', labels: { color: '#94a3b8', font: { size: 11 }, usePointStyle: true, boxWidth: 8 } } },
                cutout: '75%', 
            }
        });
    }

    // =========================================================
    // ✨ MAGIA AJAX: ACTUALIZAR FILTROS SIN RECARGAR
    // =========================================================
    const filterButtons = document.querySelectorAll('.filter-btn');

    filterButtons.forEach(btn => {
        btn.addEventListener('click', async function() {
            const filterValue = this.dataset.filter;

            // 1. Cambiar color de los botones (Activo/Inactivo)
            filterButtons.forEach(b => {
                b.classList.remove('text-white', 'bg-orange-700', 'shadow', 'active');
                b.classList.add('text-slate-300', 'hover:text-white', 'hover:bg-slate-800');
            });
            this.classList.remove('text-slate-300', 'hover:text-white', 'hover:bg-slate-800');
            this.classList.add('text-white', 'bg-orange-700', 'shadow', 'active');

            // 2. Efecto de "Cargando" en los números
            document.querySelectorAll('#metric-revenue, #metric-pending, #metric-products, #metric-clients').forEach(el => {
                el.classList.add('animate-pulse', 'text-slate-500');
            });

            try {
                // 3. Petición AJAX al controlador que armamos
                const response = await fetch(`?filter=${filterValue}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest', 
                        'Accept': 'application/json'          
                    }
                });

                if (!response.ok) throw new Error('Error de red');
                const data = await response.json();

                // 4. Actualizar los números en el HTML
                document.getElementById('metric-revenue').innerText = `$${data.ingresosMensuales}`;
                document.getElementById('metric-pending').innerText = data.pedidosPendientes;
                document.getElementById('metric-products').innerText = data.totalProductos;
                document.getElementById('metric-clients').innerText = data.totalClientes;

                // Quitar efecto de carga
                document.querySelectorAll('#metric-revenue, #metric-pending, #metric-products, #metric-clients').forEach(el => {
                    el.classList.remove('animate-pulse', 'text-slate-500');
                });

                // 5. Actualizar Gráficas (Chart.js)
                if (chartArea) {
                    chartArea.data.labels = data.chartLabels;
                    chartArea.data.datasets[0].data = data.chartData;
                    chartArea.update();
                }
                if (chartPie) {
                    chartPie.data.labels = data.topProductsLabels;
                    chartPie.data.datasets[0].data = data.topProductsValues;
                    chartPie.update();
                }
                if (chartPeak) {
                    chartPeak.data.labels = data.peakHoursLabels;
                    chartPeak.data.datasets[0].data = data.peakHoursValues;
                    chartPeak.update();
                }

                // 6. Cambiar la URL silenciosamente por si el usuario recarga
                window.history.pushState({}, '', `?filter=${filterValue}`);

            } catch (error) {
                console.error("Error cargando dashboard AJAX:", error);
                document.querySelectorAll('#metric-revenue, #metric-pending, #metric-products, #metric-clients').forEach(el => {
                    el.classList.remove('animate-pulse', 'text-slate-500');
                });
            }
        });
    });
});

// LÓGICA DEL MODAL DE TICKETS (Se queda igual)
window.openOrderModal = async function(id) {
    const modal = document.getElementById('order-modal');
    const panel = document.getElementById('order-modal-panel');
    
    document.getElementById('modal-link-completo').href = `/admin/pedidos/${id}`;

    modal.classList.remove('hidden');
    setTimeout(() => {
        modal.classList.remove('opacity-0');
        panel.classList.remove('scale-95');
        panel.classList.add('scale-100');
    }, 10);

    document.getElementById('modal-order-id').innerText = id;
    document.getElementById('modal-items-container').innerHTML = `
        <div class="flex justify-center items-center py-6 text-slate-400">
            <i class="fas fa-circle-notch fa-spin text-2xl mr-2"></i> ${window.ADMIN_LANG.loading_ticket}
        </div>
    `;

    try {
        const response = await fetch(`/admin/orden/${id}/detalles`);
        const data = await response.json();

        document.getElementById('modal-cliente').innerText = data.cliente;
        document.getElementById('modal-fecha').innerText = data.fecha;
        document.getElementById('modal-total').innerText = parseFloat(data.total).toFixed(2);
        
        const statusBadge = document.getElementById('modal-status');
        
        let translatedStatus = data.status.toUpperCase();
        if(data.status === 'pendiente') translatedStatus = window.ADMIN_LANG.status_pending;
        else if(data.status === 'listo' || data.status === 'en_camino') translatedStatus = window.ADMIN_LANG.status_ready;
        else if(data.status === 'entregado' || data.status === 'pagado') translatedStatus = window.ADMIN_LANG.status_delivered;
        
        statusBadge.innerText = translatedStatus;
        
        statusBadge.className = 'inline-block mt-3 border px-3 py-1 rounded-full text-xs font-bold ';
        if(data.status === 'pendiente') statusBadge.className += 'bg-orange-500/10 text-orange-400 border-orange-500/30';
        else if(data.status === 'listo') statusBadge.className += 'bg-blue-500/10 text-blue-400 border-blue-500/30';
        else if(data.status === 'entregado') statusBadge.className += 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30';
        else statusBadge.className += 'bg-yellow-500/10 text-yellow-400 border-yellow-500/30';

        let htmlItems = '';
        data.items.forEach(item => {
            htmlItems += `
                <div class="flex justify-between items-start bg-slate-800 p-3 rounded-xl border border-slate-700/50">
                    <div class="flex gap-3">
                        <span class="bg-slate-900 text-orange-400 font-black h-6 w-6 rounded flex items-center justify-center text-xs shrink-0">${item.cantidad}</span>
                        <div>
                            <p class="text-white font-bold text-sm leading-tight">${item.producto}</p>
                        </div>
                    </div>
                    <span class="text-emerald-400 font-mono font-bold text-sm shrink-0">$${parseFloat(item.precio).toFixed(2)}</span>
                </div>
            `;
        });
        document.getElementById('modal-items-container').innerHTML = htmlItems;

    } catch (error) {
        document.getElementById('modal-items-container').innerHTML = `
            <div class="bg-red-500/10 border border-red-500/20 text-red-400 p-4 rounded-xl text-center text-sm">
                <i class="fas fa-exclamation-triangle mb-2 text-xl"></i><br>${window.ADMIN_LANG.server_error}
            </div>`;
    }
};

window.closeOrderModal = function() {
    const modal = document.getElementById('order-modal');
    const panel = document.getElementById('order-modal-panel');
    
    modal.classList.add('opacity-0');
    panel.classList.remove('scale-100');
    panel.classList.add('scale-95');

    setTimeout(() => { modal.classList.add('hidden'); }, 300); 
};