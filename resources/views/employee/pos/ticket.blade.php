<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ticket #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</title>
    <style>
        /* --- CONFIGURACIÓN DE IMPRESIÓN --- */
        @media print {
            .no-print { display: none !important; }
            @page { margin: 0; padding: 0; }
            body { padding-bottom: 20px; } /* Espacio para el corte */
        }

        /* --- ESTILOS GENERALES --- */
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 12px;
            color: #000;
            width: 72mm; /* Ajuste seguro para impresoras de 80mm */
            margin: 0 auto;
            padding: 4mm;
            background: #fff;
            line-height: 1.2;
        }

        /* --- UTILIDADES --- */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }
        .mono { font-family: 'Courier New', Courier, monospace; letter-spacing: -0.5px; }
        
        /* --- ELEMENTOS --- */
        h1 { font-size: 18px; margin: 0 0 5px 0; letter-spacing: 1px; }
        h2 { font-size: 14px; margin: 5px 0; }
        
        .divider {
            border-top: 1px dashed #000;
            margin: 8px 0;
            width: 100%;
        }

        .double-divider {
            border-top: 2px solid #000;
            margin: 8px 0;
        }

        /* --- TABLA DE PRODUCTOS --- */
        table { width: 100%; border-collapse: collapse; }
        td { vertical-align: top; padding: 2px 0; }
        
        .col-cant { width: 10%; text-align: center; font-weight: bold; }
        .col-desc { width: 65%; padding-left: 5px; }
        .col-importe { width: 25%; text-align: right; white-space: nowrap; }

        /* --- BOTÓN WEB --- */
        .btn-print {
            display: block;
            width: 100%;
            background: #2d3748;
            color: #fff;
            text-align: center;
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        .btn-print:hover { background: #1a202c; }
    </style>
</head>
<body onload="window.print()">

    <a href="#" onclick="window.print(); return false;" class="btn-print no-print">
        🖨️ IMPRIMIR TICKET
    </a>

    <div class="text-center">
        <h1 class="font-bold">K-HAMBURGUESAS</h1>
        <div>Av. Tecnológico S/N</div>
        <div>Metepec, Edo. Méx.</div>
        <div class="mono">Tel: 55-1234-5678</div>
    </div>

    <div class="divider"></div>

    <div>
        <div style="display: flex; justify-content: space-between;">
            <span>FECHA: {{ $order->created_at->format('d/m/Y') }}</span>
            <span>HORA: {{ $order->created_at->format('H:i') }}</span>
        </div>
        <div style="margin-top: 4px;">
            ORDEN: <span class="font-bold text-lg mono">#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</span>
        </div>
        <div>
            ATENDIÓ: <span class="uppercase">{{ $order->user->name ?? 'Cajero' }}</span>
        </div>
        <div>
            CLIENTE: <span class="uppercase font-bold">{{ $order->cliente_nombre ?? 'Público General' }}</span>
        </div>
        <div style="margin-top: 4px;">
            TIPO: 
            <span class="font-bold uppercase border-black">
                @if($order->mesa)
                    MESA {{ $order->mesa }}
                @elseif($order->tipo_servicio == 'para_llevar')
                    PARA LLEVAR
                @else
                    DOMICILIO / WEB
                @endif
            </span>
        </div>
    </div>

    <div class="double-divider"></div>

    <table>
        <thead>
            <tr class="uppercase" style="font-size: 10px;">
                <td class="col-cant">Cant.</td>
                <td class="col-desc">Descripción</td>
                <td class="col-importe">Importe</td>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td class="col-cant mono">{{ $item->cantidad }}</td>
                <td class="col-desc">
                    <span class="uppercase font-bold">{{ $item->product->nombre }}</span>
                    
                    {{-- Mostrar opciones si existen (ej. Sin Cebolla) --}}
                    @php 
                        $opciones = is_string($item->opciones) ? json_decode($item->opciones, true) : ($item->opciones ?? []);
                    @endphp
                    @if(!empty($opciones))
                        <div style="font-size: 10px; color: #444; margin-top: 1px;">
                            @foreach($opciones as $op)
                                » {{ is_array($op) ? $op['valor'] : $op->valor }}<br>
                            @endforeach
                        </div>
                    @endif
                </td>
                <td class="col-importe mono">${{ number_format($item->precio_unitario * $item->cantidad, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="divider"></div>

    <table style="width: 100%;">
        {{-- Si quieres mostrar el envío desglosado --}}
        @php
            $datos = is_string($order->datos_entrega) ? json_decode($order->datos_entrega, true) : ($order->datos_entrega ?? []);
            $envio = $datos['costo_envio_cobrado'] ?? 0;
            $subtotal = $order->total - $envio;
        @endphp

        @if($envio > 0)
        <tr>
            <td class="text-right" style="padding-right: 10px;">Subtotal:</td>
            <td class="text-right mono">${{ number_format($subtotal, 2) }}</td>
        </tr>
        <tr>
            <td class="text-right" style="padding-right: 10px;">Envío:</td>
            <td class="text-right mono">${{ number_format($envio, 2) }}</td>
        </tr>
        @endif

        <tr style="font-size: 16px;">
            <td class="text-right font-bold" style="padding-right: 10px; padding-top: 5px;">TOTAL:</td>
            <td class="text-right font-bold mono" style="padding-top: 5px;">${{ number_format($order->total, 2) }}</td>
        </tr>
    </table>

    <br>

    <div style="border: 2px solid #000; padding: 5px; text-align: center; border-radius: 4px;">
        <span style="font-size: 10px;">PAGADO CON:</span><br>
        <span class="font-bold uppercase" style="font-size: 14px;">
            @if($order->metodo_pago == 'tarjeta')
                💳 TARJETA / ELECTRÓNICO
            @else
                💵 EFECTIVO
            @endif
        </span>
    </div>

    <div class="text-center" style="margin-top: 15px;">
        @if($order->codigo_entrega)
            <div style="margin-bottom: 5px;">Ref. Web: <span class="mono font-bold">{{ $order->codigo_entrega }}</span></div>
        @endif
        
        <div>¡Gracias por tu compra!</div>
        <div style="font-size: 10px; margin-top: 5px;">www.k-hamburguesas.com</div>
        
        <div style="margin-top: 15px;">. . . . . . . . . . . .</div>
    </div>

</body>
</html>