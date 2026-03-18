<?php
return [
    'title' => 'Controle de Caixa',
    'heading' => 'Monitor de Caixa',
    'subtitle' => 'Gerencie cobranças e escaneie entregas.',
    'scan_placeholder' => 'Escanear Código QR aqui...',
    
    'sales_today' => 'Vendas do Dia',
    'no_orders' => 'Não há pedidos ativos hoje',
    
    // Tabela
    'col_order' => 'Pedido #',
    'col_client' => 'Cliente / Origem',
    'col_total' => 'Total',
    'col_method' => 'Método',
    'col_status' => 'Status',
    'col_actions' => 'Ações',
    
    // Detalhes do Pedido
    'table' => 'Mesa :number',
    'takeout' => 'Para Levar',
    'casual_client' => 'Cliente Casual',
    'web_delivery' => 'Entrega Web',
    'web_user' => 'Usuário Web',
    'cash' => 'Dinheiro',
    'card' => 'Cartão',
    
    // Estados
    'status_canceled' => 'CANCELADO',
    'status_delivered' => 'ENTREGUE',
    'status_paid' => 'PAGO',
    'status_pending' => 'POR COBRAR',
    
    // Ações
    'action_closed' => 'Fechado',
    'action_void' => 'Anulado',
    'action_collected' => 'Cobrado',
    'btn_charge' => 'Cobrar',
    'btn_cancel' => 'Cancelar Pedido',
    
    // Modal Universal
    'modal_cancel' => 'Cancelar',
    'modal_confirm' => 'Confirmar',

    // Motivo Cancelamento
    'reason_label' => 'Motivo do cancelamento:',
    'reason_ph' => 'Ex: O cliente saiu antes de pagar...',

    // JS Traduções
    'js_charge_title' => 'Cobrar Pedido #:id?',
    'js_charge_desc' => 'Confirma que <span class="text-emerald-400 font-bold text-lg">$:total</span> entraram no caixa.',
    'js_charge_btn' => 'Sim, Cobrar',
    
    'js_cancel_title' => 'Solicitar Cancelamento #:id?',
    'js_cancel_desc' => 'Este pedido será pausado até que um gerente o autorize.',
    'js_cancel_btn' => 'Enviar Solicitação',
    'cancel_request_sent' => 'Solicitação de cancelamento enviada ao administrador.'
];