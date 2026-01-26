<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf; // Importamos el generador de PDF
use Carbon\Carbon;

class ReportController extends Controller
{
    // ==========================================
    // 1. GENERAR PDF (Reporte del Día)
    // ==========================================
    public function dailyReport()
    {
        $today = Carbon::today();
        
        // Obtenemos ventas de hoy que no estén canceladas
        $orders = Order::whereDate('created_at', $today)
                       ->with('user') // Traer datos del cliente
                       ->get();

        $totalIngresos = $orders->sum('total');
        $totalPedidos = $orders->count();

        // Generamos el PDF usando una vista que crearemos en el paso 3
        $pdf = Pdf::loadView('admin.reports.daily_pdf', compact('orders', 'totalIngresos', 'totalPedidos', 'today'));
        
        return $pdf->download('Cierre_Caja_' . $today->format('d-m-Y') . '.pdf');
    }

    // ==========================================
    // 2. EXPORTAR A EXCEL (CSV Nativo - Sin instalar nada extra)
    // ==========================================
    public function exportExcel()
    {
        $fileName = 'ventas_historicas.csv';
        
        // Traemos TODAS las órdenes
        $orders = Order::with('user')->orderBy('created_at', 'desc')->get();

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array('ID', 'Fecha', 'Cliente', 'Total', 'Metodo Pago', 'Estado', 'Direccion');

        $callback = function() use($orders, $columns) {
            $file = fopen('php://output', 'w');
            
            // Truco para que Excel lea bien las ñ y acentos
            fputs($file, "\xEF\xBB\xBF"); 
            
            fputcsv($file, $columns);

            foreach ($orders as $order) {
                // 1. LÓGICA DE CLIENTE (Prioridad al nombre manual)
                $nombreReal = $order->cliente_nombre ?? ($order->user ? $order->user->name : 'Invitado');

                // 2. LÓGICA DE DIRECCIÓN MEJORADA (Para aguantar órdenes viejas y nuevas)
                $direccion = 'Mostrador / General'; // Valor por defecto si todo es null

                if ($order->datos_entrega) {
                    // Intentamos decodificar por si es una Orden Nueva (JSON)
                    $json = json_decode($order->datos_entrega);

                    if (json_last_error() === JSON_ERROR_NONE && isset($json->direccion)) {
                        // CASO 1: Es una orden NUEVA (tiene estructura JSON)
                        $direccion = $json->direccion;
                    } else {
                        // CASO 2: Es una orden VIEJA (era texto plano o algo raro)
                        // Limpiamos un poco el texto por si acaso
                        $textoLimpio = strip_tags((string)$order->datos_entrega);
                        $direccion = strlen($textoLimpio) > 5 ? $textoLimpio : 'Venta Antigua';
                    }
                }

                $row['ID']        = $order->id;
                $row['Fecha']     = $order->created_at->format('d/m/Y H:i');
                $row['Cliente']   = $nombreReal;
                $row['Total']     = '$' . number_format($order->total, 2);
                $row['Metodo']    = ucfirst($order->metodo_pago);
                $row['Estado']    = ucfirst($order->status);
                $row['Direccion'] = $direccion; // <--- Usamos la variable inteligente

                fputcsv($file, array($row['ID'], $row['Fecha'], $row['Cliente'], $row['Total'], $row['Metodo'], $row['Estado'], $row['Direccion']));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}