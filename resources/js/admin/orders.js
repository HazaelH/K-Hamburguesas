$(document).ready(function() {
    
    // 1. Lógica para la Tabla (Index)
    const table = $('#dataTable');
    if (table.length) {
        table.DataTable({
            "language": { "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json" },
            "order": [[ 6, "desc" ]] 
        });
    }

    // 2. Lógica para completar pedido (Show)
    // Buscamos cualquier formulario con la clase 'form-confirm-complete'
    $('.form-confirm-complete').on('submit', function(e) {
        // Detenemos el envío automático
        e.preventDefault();
        
        // Mostramos confirmación nativa (o podrías usar SweetAlert aquí si quisieras)
        if (confirm('¿Estás seguro de que este pedido ya fue entregado al cliente?')) {
            // Si dice sí, enviamos el formulario manualmente
            this.submit();
        }
    });

});