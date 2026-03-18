<?php
return [
    // Index - Títulos y Encabezados
    'title_index' => 'Historial de Pedidos',
    'header_title' => 'Todos los Pedidos',
    'header_subtitle' => 'Historial completo de ventas y órdenes',
    
    // Index - Tarjetas de Estadísticas
    'stats_process' => 'En Proceso',
    'stats_delivered' => 'Entregados',
    'stats_income' => 'Ingresos de Hoy',

    // Index - Tabla
    'col_order' => 'Orden',
    'col_date' => 'Fecha',
    'col_client' => 'Cliente',
    'col_type' => 'Tipo',
    'col_total' => 'Total',
    'col_status' => 'Estado',
    'col_details' => 'Detalles',
    'guest' => 'Invitado',
    'type_delivery' => 'Domicilio',
    'type_takeaway' => 'Para Llevar',
    'type_dine_in' => 'En Mesa',
    'btn_ticket' => 'Ticket',
    'empty_table' => 'No hay pedidos registrados.',

    // Show - Títulos y Botones
    'title_show' => 'Detalle de Orden #',
    'btn_back' => 'Volver a Pedidos',
    'watermark_cancelled' => 'CANCELADO',
    'order_number' => 'ORDEN #',
    'btn_mark_delivered' => 'Marcar Entregada',
    'confirm_delivery' => '¿Seguro que deseas marcar esta orden como Entregada/Completada?',

    // Show - Detalles del Cliente
    'client_data' => 'Datos del Cliente',
    'client_name' => 'Nombre',
    'client_phone' => 'Teléfono de Contacto',
    'unregistered_phone' => 'No registrado',

    // Show - Detalles de Entrega
    'order_details' => 'Detalles de la Orden',
    'desc_takeaway' => 'El cliente pasa a recoger su pedido.',
    'desc_dine_in' => 'Consumo Local (Mesa)',
    'desc_dine_in_sub' => 'El cliente come en el establecimiento.',
    'desc_delivery' => 'Envío a Domicilio',
    'address_title' => 'Dirección Completa y Referencias',
    'address_missing' => 'La dirección no se guardó en la base de datos.',

    // Show - Resumen de Compra
    'purchase_summary' => 'Resumen de Compra',
    'col_qty' => 'Cant.',
    'col_product' => 'Producto',
    'col_unit_price' => 'Precio Unit.',
    'col_subtotal' => 'Subtotal',
    'unknown_product' => 'Producto Desconocido',
    'badge_obsolete' => 'Obsoleto',
    'total_paid' => 'Total Pagado:',

    // Controlador - Mensajes Flash
    'success_delivered' => 'Pedido #:id marcado como entregado.',
    'error_complete' => 'Error al completar pedido: ',

    // Estados del Pedido
    'status_pendiente' => 'Pendiente',
    'status_pagado' => 'Pagado',
    'status_preparando' => 'Preparando',
    'status_cocinando' => 'Cocinando',
    'status_listo' => 'Listo',
    'status_en_camino' => 'En Camino',
    'status_entregado' => 'Entregado',
    'status_cancelado' => 'Cancelado',

    'payment_method' => 'Método de Pago',
    'pay_terminal' => 'Terminal Bancaria',
    'verify_digits' => 'Verificar 4 dígitos:',
    'no_reference' => 'Sin referencia capturada',
    'pay_online' => 'Pago en Línea',
    'payment_authorized' => 'Cobro autorizado',
    'pay_cash' => 'Pago en Efectivo',
    'collect_cash' => 'Cobrar al momento de la entrega.',

    'confirm_delivery_title' => '¿Marcar como Entregado?',
    'confirm_delivery_desc' => 'El pedido cambiará de estado a finalizado y se sumará a los ingresos del día.',
    'btn_cancel_modal' => 'Cancelar',
    'btn_confirm_delivery' => 'Sí, Entregar',

    'filter_search_placeholder' => 'Buscar por ID, Cliente o Teléfono...',
    'filter_any_status' => 'Cualquier Estado',
    'filter_any_type' => 'Cualquier Tipo',
    'filter_any_payment' => 'Cualquier Pago',
    'filter_btn' => 'Filtrar',
    'filter_clear' => 'Limpiar Filtros',
    'col_payment' => 'Pago',
    'filter_date_start' => 'Inicio',
    'filter_date_end' => 'Fin',
];