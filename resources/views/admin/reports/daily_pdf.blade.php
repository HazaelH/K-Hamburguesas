<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <title>{{ __('admin/reports/daily_pdf.daily_report_title') }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #333; line-height: 1.4; }
        .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #ea580c; padding-bottom: 10px; }
        .header h1 { color: #ea580c; margin: 0; text-transform: uppercase; font-size: 22px; letter-spacing: 1px;}
        .info { text-align: center; font-size: 11px; color: #555; margin-bottom: 25px; }
        
        .resumen-container { width: 100%; margin-bottom: 25px; }
        .resumen { background-color: #f8fafc; padding: 12px; text-align: center; border: 1px solid #e2e8f0; border-radius: 4px; }
        .resumen h3 { margin: 5px 0 0 0; font-size: 22px; color: #0f172a; }
        .resumen span { font-size: 10px; text-transform: uppercase; font-weight: bold; color: #64748b; letter-spacing: 0.5px;}

        .seccion-titulo { color: #ea580c; font-size: 14px; text-transform: uppercase; border-bottom: 1px solid #e2e8f0; padding-bottom: 4px; margin-top: 30px; margin-bottom: 10px; font-weight: bold; }

        table { width: 100%; border-collapse: collapse; font-size: 10px; margin-bottom: 20px; }
        th { background-color: #ea580c; color: white; padding: 6px 8px; text-align: left; text-transform: uppercase; font-size: 9px; letter-spacing: 0.5px;}
        td { border-bottom: 1px solid #f1f5f9; padding: 7px 8px; color: #334155; }
        tr:nth-child(even) td { background-color: #f8fafc; }
        
        .badge-aprobo { color: #166534; font-weight: bold; }
        .badge-rechazo { color: #991b1b; font-weight: bold; }

        .footer { position: absolute; bottom: -20px; left: 0; right: 0; font-size: 9px; text-align: center; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 10px;}
    </style>
</head>
<body>

    <div class="header">
        <h1>{{ __('admin/reports/daily_pdf.brand_name') }}</h1>
        <p style="margin: 5px 0 0 0; font-size: 12px; font-weight: bold; color: #475569;">{{ __('admin/reports/daily_pdf.daily_report_title') }}</p>
    </div>

    <div class="info">
        <strong>{{ __('admin/reports/daily_pdf.operative_day') }}</strong> {{ $today->format(__('admin/reports/daily_pdf.date_format')) }} &nbsp;|&nbsp; 
        <strong>{{ __('admin/reports/daily_pdf.issued_by') }}</strong> {{ Auth::user()->name }}
    </div>

    <table class="resumen-container">
        <tr>
            <td style="padding-right: 10px; width: 50%;">
                <div class="resumen">
                    <span>{{ __('admin/reports/daily_pdf.total_cash_flow') }}</span><br>
                    <h3>{{ formatCurrency($totalIngresos) }}</h3> </div>
            </td>
            <td style="padding-left: 10px; width: 50%;">
                <div class="resumen">
                    <span>{{ __('admin/reports/daily_pdf.successful_transactions') }}</span><br>
                    <h3>{{ $totalPedidos }}</h3>
                </div>
            </td>
        </tr>
    </table>

    <div class="seccion-titulo">{{ __('admin/reports/daily_pdf.transactions_breakdown') }}</div>
    <table>
        <thead>
            <tr>
                <th style="width: 10%;">{{ __('admin/reports/daily_pdf.col_folio') }}</th>
                <th style="width: 15%;">{{ __('admin/reports/daily_pdf.col_time') }}</th>
                <th style="width: 30%;">{{ __('admin/reports/daily_pdf.col_consumer') }}</th>
                <th style="width: 15%;">{{ __('admin/reports/daily_pdf.col_method') }}</th>
                <th style="width: 15%;">{{ __('admin/reports/daily_pdf.col_status') }}</th>
                <th style="text-align: right; width: 15%;">{{ __('admin/reports/daily_pdf.col_amount') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
            <tr>
                <td style="font-weight: bold;">#{{ $order->id }}</td>
                <td>{{ app()->getLocale() == 'en' ? $order->created_at->format('h:i A') : $order->created_at->format('H:i') }}</td>
                <td>{{ $order->cliente_nombre ?? ($order->user->name ?? __('admin/reports/daily_pdf.counter')) }}</td>
                <td>{{ ucfirst($order->metodo_pago) }}</td>
                <td>{{ ucfirst($order->status) }}</td>
                <td style="text-align: right; font-weight: bold;">{{ formatCurrency($order->total) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center; padding: 20px; font-style: italic; color: #94a3b8;">{{ __('admin/reports/daily_pdf.no_financial_movements') }}</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="seccion-titulo">{{ __('admin/reports/daily_pdf.operative_audit') }}</div>
    <table>
        <thead>
            <tr>
                <th style="width: 12%;">{{ __('admin/reports/daily_pdf.col_time') }}</th>
                <th style="width: 15%;">{{ __('admin/reports/daily_pdf.col_verdict') }}</th>
                <th style="width: 33%;">{{ __('admin/reports/daily_pdf.col_exception_detail') }}</th>
                <th style="width: 20%;">{{ __('admin/reports/daily_pdf.col_authorized_by') }}</th>
                <th style="width: 20%;">{{ __('admin/reports/daily_pdf.col_requested_by') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($auditLogs as $log)
            <tr>
                <td>{{ app()->getLocale() == 'en' ? $log->created_at->format('h:i:s A') : $log->created_at->format('H:i:s') }}</td>
                <td class="{{ $log->accion == 'APROBÓ' ? 'badge-aprobo' : 'badge-rechazo' }}">
                    {{ $log->accion }}
                </td>
                <td>{{ $log->detalle }}</td>
                <td>{{ $log->admin_name }}</td>
                <td>{{ $log->empleado_name }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center; padding: 20px; font-style: italic; color: #94a3b8;">{{ __('admin/reports/daily_pdf.no_exceptions') }}</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        {{ __('admin/reports/daily_pdf.confidential_doc') }}
    </div>

</body>
</html>