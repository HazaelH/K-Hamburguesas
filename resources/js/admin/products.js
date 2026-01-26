$(document).ready(function() {
    
    // 1. DataTables
    const table = $('#dataTable');
    if (table.length) {
        table.DataTable({
            "language": { "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json" }
        });
    }

    // 2. Gráfico de Pastel (Categorías)
    const chartCanvas = document.getElementById("productosCategoriaChart");
    const dataContainer = document.getElementById("products-chart-data");

    if (chartCanvas && dataContainer) {
        const labels = JSON.parse(dataContainer.dataset.labels);
        const data = JSON.parse(dataContainer.dataset.values);

        new Chart(chartCanvas, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796'],
                    hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf', '#dda20a', '#be2617', '#60616f'],
                    hoverBorderColor: "rgba(234, 236, 244, 1)",
                }],
            },
            options: {
                maintainAspectRatio: false,
                tooltips: {
                    backgroundColor: "rgb(255,255,255)",
                    bodyFontColor: "#858796",
                    borderColor: '#dddfeb',
                    borderWidth: 1,
                    xPadding: 15,
                    yPadding: 15,
                    displayColors: false,
                    caretPadding: 10,
                },
                legend: { display: false },
                cutoutPercentage: 80,
            },
        });
    }

    // 3. Confirmación de Eliminación
    $('.form-delete').on('submit', function(e) {
        e.preventDefault();
        if (confirm('¿Estás seguro de que deseas eliminar este producto? Esta acción no se puede deshacer.')) {
            this.submit();
        }
    });
});