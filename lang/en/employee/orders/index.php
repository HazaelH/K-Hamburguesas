<?php
return [
    'title' => 'Cash Register Control',
    'heading' => 'Cash Monitor',
    'subtitle' => 'Manage payments and scan deliveries.',
    'scan_placeholder' => 'Scan QR Code here...',
    
    'sales_today' => 'Today\'s Sales',
    'no_orders' => 'No active orders today',
    
    // Tabla
    'col_order' => 'Order #',
    'col_client' => 'Customer / Origin',
    'col_total' => 'Total',
    'col_method' => 'Method',
    'col_status' => 'Status',
    'col_actions' => 'Actions',
    
    // Detalles de Orden
    'table' => 'Table :number',
    'takeout' => 'Takeout',
    'casual_client' => 'Walk-in Customer',
    'web_delivery' => 'Web Delivery',
    'web_user' => 'Web User',
    'cash' => 'Cash',
    'card' => 'Card',
    
    // Estados
    'status_canceled' => 'CANCELED',
    'status_delivered' => 'DELIVERED',
    'status_paid' => 'PAID',
    'status_pending' => 'PENDING',
    
    // Acciones
    'action_closed' => 'Closed',
    'action_void' => 'Voided',
    'action_collected' => 'Collected',
    'btn_charge' => 'Charge',
    'btn_cancel' => 'Cancel Order',
    
    // Modal Universal
    'modal_cancel' => 'Cancel',
    'modal_confirm' => 'Confirm',

    // JS Traducciones
    'js_charge_title' => 'Charge Order #:id?',
    'js_charge_desc' => 'Confirm that <span class="text-emerald-400 font-bold text-lg">$:total</span> entered the register.',
    'js_charge_btn' => 'Yes, Charge',
    
    'js_cancel_title' => 'Cancel Order #:id?',
    'js_cancel_desc' => 'This action <span class="text-red-400 font-bold">cannot be undone</span>. The order will be voided.',
    'js_cancel_btn' => 'Yes, Cancel',
];