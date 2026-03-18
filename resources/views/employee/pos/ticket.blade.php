<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <title>Ticket #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</title>
    <style>
        @media print {
            .no-print { display: none !important; }
            @page { margin: 0; padding: 0; }
            body { padding-bottom: 20px; } 
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 12px;
            color: #000;
            width: 72mm; 
            margin: 0 auto;
            padding: 4mm;
            background: #fff;
            line-height: 1.2;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }
        .mono { font-family: 'Courier New', Courier, monospace; letter-spacing: -0.5px; }
        
        h1 { font-size: 18px; margin: 0 0 5px 0; letter-spacing: 1px; }
        h2 { font-size: 14px; margin: 5px 0; }
        
        .divider { border-top: 1px dashed #000; margin: 8px 0; width: 100%; }
        .double-divider { border-top: 2px solid #000; margin: 8px 0; }

        table { width: 100%; border-collapse: collapse; }
        td { vertical-align: top; padding: 2px 0; }
        
        .col-cant { width: 10%; text-align: center; font-weight: bold; }
        .col-desc { width: 65%; padding-left: 5px; }
        .col-importe { width: 25%; text-align: right; white-space: nowrap; }

        .btn-print {
            display: block; width: 100%; background: #2d3748; color: #fff; text-align: center;
            padding: 12px; margin-bottom: 20px; border-radius: 6px; text-decoration: none;
            font-weight: bold; box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        .btn-print:hover { background: #1a202c; }
    </style>
</head>
<body onload="window.print()">

    <a href="#" onclick="window.print(); return false;" class="btn-print no-print">
        {{ __('employee/pos/ticket.print_ticket') }}
    </a>

    <div class="text-center">
        <h1 class="font-bold">K-HAMBURGUESAS</h1>
        <div>Av. Miguel Hidalgo 14, centro, 50900 </div>
        <div>Villa de Almoloya de Juárez, Méx., Mexico</div>
        <div class="mono">Tel: +52 725 136 1324</div>
    </div>

    <div class="divider"></div>

    <div>
        <div style="display: flex; justify-content: space-between;">
            <span>{{ __('employee/pos/ticket.date') }} {{ $order->created_at->format(app()->getLocale() == 'en' ? 'm/d/Y' : 'd/m/Y') }}</span>
            <span>{{ __('employee/pos/ticket.time') }} {{ $order->created_at->format(app()->getLocale() == 'en' ? 'h:i A' : 'H:i') }}</span>
        </div>
        <div style="margin-top: 4px;">
            {{ __('employee/pos/ticket.order') }} <span class="font-bold text-lg mono">#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</span>
        </div>
        <div>
            {{ __('employee/pos/ticket.cashier') }} <span class="uppercase">{{ $order->user->name ?? __('employee/pos/ticket.default_cashier') }}</span>
        </div>
        <div>
            {{ __('employee/pos/ticket.client') }} <span class="uppercase font-bold">{{ $order->cliente_nombre ?? __('employee/pos/ticket.general_public') }}</span>
        </div>
        <div style="margin-top: 4px;">
            {{ __('employee/pos/ticket.type') }} 
            <span class="font-bold uppercase border-black">
                @if($order->mesa)
                    {{ __('employee/pos/ticket.table', ['number' => $order->mesa]) }}
                @elseif($order->tipo_servicio == 'mesa')
                    {{ __('employee/pos/ticket.dine_in') }}
                @elseif($order->tipo_servicio == 'llevar')
                    {{ __('employee/pos/ticket.takeout') }}
                @endif
            </span>
        </div>
    </div>

    <div class="double-divider"></div>

    <table>
        <thead>
            <tr class="uppercase" style="font-size: 10px;">
                <td class="col-cant">{{ __('employee/pos/ticket.qty') }}</td>
                <td class="col-desc">{{ __('employee/pos/ticket.desc') }}</td>
                <td class="col-importe">{{ __('employee/pos/ticket.amount') }}</td>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td class="col-cant mono">{{ $item->cantidad }}</td>
                <td class="col-desc">
                    <span class="font-bold">{{ $item->product ? $item->product->nombre_traducido : __('employee/pos/ticket.product_deleted') }}</span>

                    @if(!empty($item->opciones) && $item->opciones !== 'null')
                        @php $opciones = is_string($item->opciones) ? json_decode($item->opciones, true) : $item->opciones; @endphp
                        
                        @if(is_array($opciones) && count($opciones) > 0)
                            <div class="mt-1.5 flex flex-wrap gap-1">
                                @foreach($opciones as $opcion)
                                    @php
                                        // 1. Tomamos el valor original en español de la orden
                                        $valorOriginal = is_array($opcion) ? ($opcion['valor'] ?? $opcion['name'] ?? $opcion['nombre'] ?? '') : $opcion;
                                        $textoOpcion = $valorOriginal;

                                        // 2. Buscamos en el catálogo de este producto si hay traducción
                                        if ($item->product && !empty($item->product->opciones_personalizacion)) {
                                            $catOpciones = is_string($item->product->opciones_personalizacion) 
                                                ? json_decode($item->product->opciones_personalizacion, true) 
                                                : $item->product->opciones_personalizacion;
                                            
                                            $locale = app()->getLocale();
                                            if (is_array($catOpciones)) {
                                                foreach ($catOpciones as $catOp) {
                                                    // Si coincide con nuestro extra en español
                                                    if (isset($catOp['nombre']) && $catOp['nombre'] === $valorOriginal) {
                                                        // Le ponemos el idioma correspondiente
                                                        if ($locale === 'en' && !empty($catOp['nombre_en'])) {
                                                            $textoOpcion = $catOp['nombre_en'];
                                                        } elseif ($locale === 'pt' && !empty($catOp['nombre_pt'])) {
                                                            $textoOpcion = $catOp['nombre_pt'];
                                                        }
                                                        break;
                                                    }
                                                }
                                            }
                                        }
                                    @endphp
                                    <span class="bg-red-500/10 text-red-400 border border-red-500/20 text-[10px] px-1.5 py-0.5 rounded font-bold uppercase tracking-wider truncate max-w-full flex items-center gap-1 shadow-sm">
                                        <i class="fas fa-plus text-[8px]"></i> {{ $textoOpcion }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    @endif
                </td>
                <td class="col-importe mono">{{ formatCurrency($item->precio_unitario * $item->cantidad) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="divider"></div>

    <table style="width: 100%;">
        @php
            $datos = is_string($order->datos_entrega) ? json_decode($order->datos_entrega, true) : ($order->datos_entrega ?? []);
            $envio = $datos['costo_envio_cobrado'] ?? 0;
            $subtotal = $order->total - $envio;
        @endphp

        @if($envio > 0)
        <tr>
            <td class="text-right" style="padding-right: 10px;">{{ __('employee/pos/ticket.subtotal') }}</td>
            <td class="text-right mono">{{ formatCurrency($subtotal) }}</td>
        </tr>
        <tr>
            <td class="text-right" style="padding-right: 10px;">{{ __('employee/pos/ticket.shipping') }}</td>
            <td class="text-right mono">{{ formatCurrency($envio) }}</td>
        </tr>
        @endif

        <tr style="font-size: 16px;">
            <td class="text-right font-bold" style="padding-right: 10px; padding-top: 5px;">{{ __('employee/pos/ticket.total') }}</td>
            <td class="text-right font-bold mono" style="padding-top: 5px;">{{ formatCurrency($order->total) }}</td>
        </tr>
    </table>

    <br>

    <div style="border: 2px solid #000; padding: 5px; text-align: center; border-radius: 4px;">
        <span style="font-size: 10px;">{{ __('employee/pos/ticket.paid_with') }}</span><br>
        <span class="font-bold uppercase" style="font-size: 14px;">
            @if($order->metodo_pago == 'tarjeta')
                {{ __('employee/pos/ticket.card') }}
            @else
                {{ __('employee/pos/ticket.cash') }}
            @endif
        </span>
    </div>

    <div class="text-center" style="margin-top: 15px;">
        @if($order->codigo_entrega)
            <div style="margin-bottom: 5px;">{{ __('employee/pos/ticket.web_ref') }} <span class="mono font-bold">{{ $order->codigo_entrega }}</span></div>
        @endif
        
        <div>{{ __('employee/pos/ticket.thanks') }}</div>
        <div style="font-size: 10px; margin-top: 5px;">www.k-hamburguesas.com</div>
        
        <div style="margin-top: 15px;">. . . . . . . . . . . .</div>
    </div>

</body>
</html>