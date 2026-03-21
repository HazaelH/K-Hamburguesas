<?php
return [
    'title' => 'Control de Caja',
    'heading' => 'Monitor de Caja',
    'subtitle' => 'Gestiona cobros y escanea entregas.',
    'scan_placeholder' => 'Escanear Código QR aquí...',
    
    'sales_today' => 'Ventas del Día',
    'no_orders' => 'No hay órdenes activas hoy',
    
    // Tabla
    'col_order' => 'Orden #',
    'col_client' => 'Cliente / Origen',
    'col_total' => 'Total',
    'col_method' => 'Método',
    'col_status' => 'Estado',
    'col_actions' => 'Acciones',
    
    // Detalles de Orden
    'table' => 'Mesa :number',
    'takeout' => 'Para Llevar',
    'casual_client' => 'Cliente Casual',
    'web_delivery' => 'Envío Web',
    'web_user' => 'Usuario Web',
    'cash' => 'Efectivo',
    'card' => 'Tarjeta',
    
    // Estados
    'status_canceled' => 'CANCELADO',
    'status_delivered' => 'ENTREGADO',
    'status_paid' => 'PAGADO',
    'status_pending' => 'POR COBRAR',
    
    // Acciones
    'action_closed' => 'Cerrado',
    'action_void' => 'Anulado',
    'action_collected' => 'Cobrado',
    'btn_charge' => 'Cobrar',
    'btn_cancel' => 'Cancelar Orden',
    
    // Modal Universal
    'modal_cancel' => 'Cancelar',
    'modal_confirm' => 'Confirmar',

    // JS Traducciones
    'js_charge_title' => '¿Cobrar Orden #:id?',
    'js_charge_desc' => 'Confirmas que ingresaron <span class="text-emerald-400 font-bold text-lg">$:total</span> a la caja.',
    'js_charge_btn' => 'Sí, Cobrar',
    
    'js_cancel_title' => '¿Cancelar Orden #:id?',
    'js_cancel_desc' => 'Esta acción <span class="text-red-400 font-bold">no se puede deshacer</span>. La orden quedará anulada.',
    'js_cancel_btn' => 'Sí, Cancelar',
];