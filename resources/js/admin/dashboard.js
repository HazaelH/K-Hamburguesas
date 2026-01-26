document.addEventListener('DOMContentLoaded', () => {
    
    const dataDiv = document.getElementById('dashboard-data');
    if (!dataDiv) return;

    // --- 1. GRÁFICA LINEAL (Ingresos) ---
    const lineLabels = JSON.parse(dataDiv.dataset.lineLabels);
    const lineValues = JSON.parse(dataDiv.dataset.lineValues);
    const ctxLine = document.getElementById('myAreaChart');

    if(ctxLine) {
        new Chart(ctxLine, {
            type: 'line',
            data: {
                labels: lineLabels,
                datasets: [{
                    label: 'Ingresos',
                    data: lineValues,
                    backgroundColor: 'rgba(249, 115, 22, 0.1)',
                    borderColor: 'rgba(249, 115, 22, 1)',
                    borderWidth: 3,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: 'rgba(249, 115, 22, 1)',
                    pointRadius: 4,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: '#1e293b',
                        titleColor: '#fff',
                        bodyColor: '#cbd5e1',
                        borderColor: '#334155',
                        borderWidth: 1
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: '#94a3b8' }
                    },
                    y: {
                        grid: { color: '#334155', borderDash: [5, 5] },
                        ticks: { color: '#94a3b8' }
                    }
                }
            }
        });
    }

    // --- 2. GRÁFICA DE DONA (Top Productos) ---
    const pieLabels = JSON.parse(dataDiv.dataset.pieLabels);
    const pieValues = JSON.parse(dataDiv.dataset.pieValues);
    const ctxPie = document.getElementById('topProductsChart');

    if(ctxPie) {
        new Chart(ctxPie, {
            type: 'doughnut',
            data: {
                labels: pieLabels,
                datasets: [{
                    data: pieValues,
                    backgroundColor: [
                        '#ea580c', // Orange-600 (Más vendido)
                        '#f97316', // Orange-500
                        '#fb923c', // Orange-400
                        '#cbd5e1', // Slate-300
                        '#475569', // Slate-600
                    ],
                    borderColor: '#1e293b', // Color del fondo para separar segmentos
                    borderWidth: 2,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { color: '#cbd5e1', font: { size: 10 }, boxWidth: 10 }
                    },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        borderColor: '#334155',
                        borderWidth: 1
                    }
                },
                cutout: '70%', // Hace la dona más delgada y elegante
            }
        });
    }
});
window.openOrderModal = async function(id) {
    const modal = document.getElementById('order-modal');
    modal.classList.remove('hidden');

    // Limpiar datos previos
    document.getElementById('modal-items-container').innerHTML = '<p class="text-center text-slate-500">Cargando...</p>';

    try {
        const response = await fetch(`/admin/orden/${id}/detalles`);
        const data = await response.json();

        // Llenar datos
        document.getElementById('modal-order-id').innerText = data.id;
        document.getElementById('modal-cliente').innerText = data.cliente;
        document.getElementById('modal-fecha').innerText = data.fecha;
        document.getElementById('modal-total').innerText = data.total;
        document.getElementById('modal-status').innerText = data.status.toUpperCase();

        // Llenar lista de productos
        let htmlItems = '';
        data.items.forEach(item => {
            htmlItems += `
                <div class="flex justify-between items-center mb-2 last:mb-0">
                    <div class="flex gap-2">
                        <span class="text-orange-500 font-bold text-sm">x${item.cantidad}</span>
                        <span class="text-white text-sm">${item.producto}</span>
                    </div>
                    <span class="text-slate-400 text-sm">$${item.precio}</span>
                </div>
            `;
        });
        document.getElementById('modal-items-container').innerHTML = htmlItems;

    } catch (error) {
        console.error('Error al cargar orden:', error);
        document.getElementById('modal-items-container').innerHTML = '<p class="text-red-400 text-center">Error al cargar datos</p>';
    }
};

window.closeOrderModal = function() {
    document.getElementById('order-modal').classList.add('hidden');
};