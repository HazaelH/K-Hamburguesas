<!DOCTYPE html>
<html>
<head>
    <title>Reporte Diario</title>
    <style>
        body { font-family: sans-serif; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #ea580c; padding-bottom: 10px; }
        .header h1 { color: #ea580c; margin: 0; text-transform: uppercase; }
        .info { text-align: center; font-size: 12px; color: #666; margin-bottom: 20px; }
        
        .resumen { width: 100%; margin-bottom: 20px; background-color: #f3f4f6; padding: 15px; text-align: center; }
        .resumen h3 { margin: 0; font-size: 24px; color: #111; }
        .resumen span { font-size: 12px; text-transform: uppercase; color: #666; }

        table { width: 100%; border-collapse: collapse; font-size: 12px; }
        th { background-color: #ea580c; color: white; padding: 8px; text-align: left; }
        td { border-bottom: 1px solid #ddd; padding: 8px; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        
        .footer { position: fixed; bottom: 0; left: 0; right: 0; font-size: 10px; text-align: center; color: #999; }
    </style>
</head>
<body>

    <div class="header">
        <h1>K-Hamburguesas</h1>
        <p>Reporte de Cierre de Caja</p>
    </div>

    <div class="info">
        <strong>Fecha:</strong> {{ $today->format('d/m/Y') }} <br>
        <strong>Generado por:</strong> {{ Auth::user()->name }}
    </div>

    <table style="width: 100%; margin-bottom: 20px;">
        <tr>
            <td class="resumen">
                <span>Total Ventas Hoy</span><br>
                <h3>${{ number_format($totalIngresos, 2) }}</h3>
            </td>
            <td class="resumen">
                <span>Pedidos Totales</span><br>
                <h3>{{ $totalPedidos }}</h3>
            </td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Hora</th>
                <th>Cliente</th>
                <th>Método</th>
                <th>Estado</th>
                <th style="text-align: right;">Monto</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
            <tr>
                <td>#{{ $order->id }}</td>
                <td>{{ $order->created_at->format('H:i') }}</td>
                <td>{{ $order->cliente_nombre ?? ($order->user->name ?? 'Invitado') }}</td>
                <td>{{ ucfirst($order->metodo_pago) }}</td>
                <td>{{ ucfirst($order->status) }}</td>
                <td style="text-align: right;">${{ number_format($order->total, 2) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center; padding: 20px;">No se han registrado ventas hoy.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Documento interno de K-Hamburguesas.
    </div>

</body>
</html>