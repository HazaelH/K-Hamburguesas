<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\AuditLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ReportController extends Controller
{
    // ==========================================
    // 1. GENERAR PDF (Reporte del Día + Auditoría)
    // ==========================================
    public function dailyReport()
    {
        $today = Carbon::today();
        
        $orders = Order::whereDate('created_at', $today)
                       ->with('user')
                       ->get();

        $totalIngresos = $orders->where('status', '!=', 'cancelado')->sum('total');
        $totalPedidos = $orders->where('status', '!=', 'cancelado')->count();

        $auditLogs = AuditLog::whereDate('created_at', $today)
                             ->orderBy('created_at', 'desc')
                             ->get();

        $pdf = Pdf::loadView('admin.reports.daily_pdf', compact('orders', 'totalIngresos', 'totalPedidos', 'today', 'auditLogs'));
        
        $prefix = app()->getLocale() == 'en' ? 'Cash_Register_' : 'Cierre_Caja_';
        return $pdf->download($prefix . $today->format('d-m-Y') . '.pdf');
    }

    // ==========================================
    // 2. EXPORTAR A EXCEL (CSV Nativo - Histórico)
    // ==========================================
    public function exportExcel()
    {
        $fileName = __('admin/reports/daily_pdf.csv_filename') . date('Y_m_d_H_i') . '.csv';

        $columns = [
            __('admin/reports/daily_pdf.csv_col_id'), 
            __('admin/reports/daily_pdf.csv_col_date'), 
            __('admin/reports/daily_pdf.csv_col_time'), 
            __('admin/reports/daily_pdf.csv_col_type'), 
            __('admin/reports/daily_pdf.csv_col_client'), 
            __('admin/reports/daily_pdf.csv_col_items'), 
            __('admin/reports/daily_pdf.csv_col_total'), 
            __('admin/reports/daily_pdf.csv_col_method'), 
            __('admin/reports/daily_pdf.csv_col_status'), 
            __('admin/reports/daily_pdf.csv_col_exceptions')
        ];

        return response()->streamDownload(function () use ($columns) {
            $file = fopen('php://output', 'w');
            
            fputs($file, "\xEF\xBB\xBF"); 
            fputcsv($file, $columns);

            foreach (Order::with(['user', 'items.product'])->orderBy('created_at', 'desc')->cursor() as $order) {
                
                $nombreReal = $order->cliente_nombre ?? ($order->user ? $order->user->name : __('admin/reports/daily_pdf.counter'));

                $tipoOrden = __('admin/reports/daily_pdf.type_local');
                $excepcion = __('admin/reports/daily_pdf.exception_none');

                if ($order->datos_entrega) {
                    $json = is_string($order->datos_entrega) ? json_decode($order->datos_entrega) : $order->datos_entrega;
                    
                    if (json_last_error() === JSON_ERROR_NONE && $json) {
                        if (isset($json->direccion)) {
                            $tipoOrden = __('admin/reports/daily_pdf.type_delivery');
                        }
                        if (isset($json->empleado_solicitante) && $order->status === 'cancelado') {
                            $excepcion = __('admin/reports/daily_pdf.exception_canceled_by') . $json->empleado_solicitante;
                        }
                    } else {
                        $textoLimpio = strip_tags((string)$order->datos_entrega);
                        if (strlen($textoLimpio) > 5) $tipoOrden = 'Delivery';
                    }
                }

                $resumenArticulos = [];
                foreach ($order->items as $item) {
                    $nombreProducto = $item->product ? $item->product->nombre : __('admin/reports/daily_pdf.deleted_product');
                    $resumenArticulos[] = $item->cantidad . 'x ' . $nombreProducto;
                }
                $textoArticulos = implode(', ', $resumenArticulos);
                if (empty($textoArticulos)) $textoArticulos = __('admin/reports/daily_pdf.no_details');

                $dateFormat = app()->getLocale() == 'en' ? 'Y-m-d' : 'd-m-Y';
                $timeFormat = app()->getLocale() == 'en' ? 'h:i:s A' : 'H:i:s';

                fputcsv($file, [
                    $order->id,
                    $order->created_at ? $order->created_at->format($dateFormat) : 'S/F',
                    $order->created_at ? $order->created_at->format($timeFormat) : 'S/H',
                    $tipoOrden,
                    $nombreReal,
                    $textoArticulos, 
                    convertCurrencyValue($order->total),
                    ucfirst($order->metodo_pago ?? 'N/A'),
                    strtoupper($order->status ?? 'N/A'),
                    $excepcion
                ]);
            }
            fclose($file);
            
        }, $fileName, [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ]);
    }
}