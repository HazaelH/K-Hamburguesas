@extends('layouts.admin')

@section('titulo', 'Panel de Control')

@section('contenido')
<div class="space-y-6">

    {{-- 1. HEADER & ACCIONES RÁPIDAS --}}
    <div class="flex flex-col lg:flex-row justify-between items-end gap-4">
        <div>
            <h1 class="text-3xl font-bold text-white">Panel de Control</h1>
            <p class="text-slate-400">Resumen operativo de K-Hamburguesas</p>
        </div>
        
        <div class="flex gap-2">
            {{-- Botón PDF --}}
            <a href="{{ route('admin.reports.daily') }}" target="_blank" class="bg-slate-800 hover:bg-slate-700 text-white border border-slate-700 px-4 py-2 rounded-lg text-sm font-bold flex items-center gap-2 transition" title="Reporte del Día">
                <i class="fas fa-file-pdf text-red-500"></i> Cierre PDF
            </a>
            
            {{-- Botón Excel --}}
            <a href="{{ route('admin.reports.excel') }}" target="_blank" class="bg-slate-800 hover:bg-slate-700 text-white border border-slate-700 px-4 py-2 rounded-lg text-sm font-bold flex items-center gap-2 transition" title="Exportar Todo">
                <i class="fas fa-file-excel text-green-500"></i> Histórico CSV
            </a>

            {{-- Botón Nueva Oferta --}}
            <a href="{{ route('admin.offers.index') }}" class="bg-orange-600 hover:bg-orange-500 text-white px-4 py-2 rounded-lg text-sm font-bold flex items-center gap-2 shadow-lg shadow-orange-900/50 transition">
                <i class="fas fa-plus"></i> Nueva Promo
            </a>
        </div>
    </div>

    {{-- 2. GRID DE METRICAS --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        
        {{-- Tarjeta 1: INGRESOS --}}
        <div class="bg-gradient-to-br from-emerald-900 to-slate-900 border border-emerald-500/30 p-6 rounded-2xl shadow-lg relative overflow-hidden group">
            <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <i class="fas fa-dollar-sign text-6xl text-emerald-400 transform -rotate-12 translate-x-2 -translate-y-2"></i>
            </div>
            <div class="relative z-10">
                <p class="text-emerald-400 text-xs font-bold uppercase tracking-wider mb-1">Ingresos (Mes)</p>
                <h3 class="text-3xl font-bold text-white">${{ number_format($ingresosMensuales, 2) }}</h3>
                <p class="text-emerald-200/50 text-xs mt-2 flex items-center gap-1">
                    <i class="fas fa-arrow-up"></i> Actualizado hoy
                </p>
            </div>
        </div>

        {{-- Tarjeta 2: PENDIENTES (AQUÍ ESTABA EL ERROR) --}}
        <div class="bg-gradient-to-br from-orange-900 to-slate-900 border border-orange-500/30 p-6 rounded-2xl shadow-lg relative overflow-hidden group">
            <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <i class="fas fa-utensils text-6xl text-orange-400 transform -rotate-12 translate-x-2 -translate-y-2"></i>
            </div>
            <div class="relative z-10">
                <p class="text-orange-400 text-xs font-bold uppercase tracking-wider mb-1">Pendientes</p>
                <h3 class="text-3xl font-bold text-white">{{ $pedidosPendientes }}</h3>
                
                {{-- CORRECCIÓN: Usamos un enlace a la cocina, no el modal, porque aquí no hay ID de orden --}}
                <a href="{{ route('kitchen.live') }}" 
                   class="text-slate-400 hover:text-orange-400 transition hover:scale-110 inline-block mt-2" 
                   title="Ir a Cocina">
                    <span class="text-xs flex items-center gap-1">Ver todos <i class="fas fa-arrow-right"></i></span>
                </a>
            </div>
        </div>

        {{-- Tarjeta 3: PRODUCTOS --}}
        <div class="bg-gradient-to-br from-blue-900 to-slate-900 border border-blue-500/30 p-6 rounded-2xl shadow-lg relative overflow-hidden group">
            <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <i class="fas fa-boxes text-6xl text-blue-400 transform -rotate-12 translate-x-2 -translate-y-2"></i>
            </div>
            <div class="relative z-10">
                <p class="text-blue-400 text-xs font-bold uppercase tracking-wider mb-1">Productos</p>
                <h3 class="text-3xl font-bold text-white">{{ $totalProductos }}</h3>
                <a href="{{ route('admin.products.index') }}" class="text-blue-200/50 text-xs mt-2 flex items-center gap-1 hover:text-blue-300 transition">
                    Gestionar Menú <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
        
        {{-- Tarjeta 4: CLIENTES --}}
        <div class="bg-gradient-to-br from-purple-900 to-slate-900 border border-purple-500/30 p-6 rounded-2xl shadow-lg relative overflow-hidden group">
            <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <i class="fas fa-users text-6xl text-purple-400 transform -rotate-12 translate-x-2 -translate-y-2"></i>
            </div>
            <div class="relative z-10">
                <p class="text-purple-400 text-xs font-bold uppercase tracking-wider mb-1">Clientes</p>
                <h3 class="text-3xl font-bold text-white">{{ $totalClientes }}</h3>
                <p class="text-purple-200/50 text-xs mt-2 flex items-center gap-1">
                    <i class="fas fa-user-check"></i> Registrados
                </p>
            </div>
        </div>
    </div>

    {{-- 3. GRÁFICAS --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-slate-800 border border-slate-700 rounded-2xl shadow-xl p-6">
            <div class="flex justify-between items-center mb-6">
                <h6 class="font-bold text-white text-lg flex items-center gap-2">
                    <i class="fas fa-chart-line text-orange-500"></i> Tendencia de Ingresos
                </h6>
            </div>
            <div class="relative h-[300px] w-full">
                <canvas id="myAreaChart"></canvas>
            </div>
        </div>

        <div class="bg-slate-800 border border-slate-700 rounded-2xl shadow-xl p-6 flex flex-col">
            <h6 class="font-bold text-white text-lg mb-4 flex items-center gap-2">
                <i class="fas fa-crown text-yellow-500"></i> Top Ventas
            </h6>
            <div class="relative h-[200px] w-full flex-1 flex justify-center">
                <canvas id="topProductsChart"></canvas>
            </div>
            <div class="mt-4 text-center">
                <p class="text-xs text-slate-400">Productos más solicitados</p>
            </div>
        </div>
    </div>

    {{-- 4. TABLA DE ACTIVIDAD RECIENTE --}}
    <div class="bg-slate-800 border border-slate-700 rounded-2xl shadow-xl overflow-hidden">
        <div class="p-6 border-b border-slate-700 flex justify-between items-center">
            <h6 class="font-bold text-white text-lg flex items-center gap-2">
                <i class="fas fa-history text-blue-400"></i> Actividad Reciente
            </h6>
            <a href="{{ route('kitchen.live') }}" class="text-sm text-blue-400 hover:text-blue-300 font-bold transition">
                Ver monitores <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-slate-400">
                <thead class="text-xs text-slate-300 uppercase bg-slate-900/50">
                    <tr>
                        <th class="px-6 py-3">ID</th>
                        <th class="px-6 py-3">Cliente</th>
                        <th class="px-6 py-3">Total</th>
                        <th class="px-6 py-3">Estado</th>
                        <th class="px-6 py-3">Hace</th>
                        <th class="px-6 py-3 text-right">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700">
                    @forelse($recentOrders as $order)
                        <tr class="hover:bg-slate-700/30 transition">
                            <td class="px-6 py-4 font-bold text-white">#{{ $order->id }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-slate-600 flex items-center justify-center text-[10px] text-white font-bold">
                                        {{ substr($order->cliente_nombre ?? 'G', 0, 1) }}
                                    </div>
                                    {{ $order->cliente_nombre ?? 'Invitado' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 font-mono text-emerald-400">${{ number_format($order->total, 2) }}</td>
                            <td class="px-6 py-4">
                                @php
                                    $statusColor = match($order->status) {
                                        'pendiente' => 'text-red-400 bg-red-500/10 border border-red-500/20',
                                        'cocinando', 'preparando' => 'text-yellow-400 bg-yellow-500/10 border border-yellow-500/20',
                                        'listo', 'en_camino' => 'text-blue-400 bg-blue-500/10 border border-blue-500/20',
                                        'entregado' => 'text-green-400 bg-green-500/10 border border-green-500/20',
                                        default => 'text-slate-400 bg-slate-500/10 border border-slate-500/20'
                                    };
                                @endphp
                                <span class="{{ $statusColor }} px-2 py-1 rounded text-xs font-bold uppercase tracking-wider">
                                    {{ $order->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4">{{ $order->created_at->diffForHumans(null, true, true) }}</td>
                            <td class="px-6 py-4 text-right">
                                {{-- AQUI SÍ VA EL MODAL (Vista Rápida) PORQUE ESTAMOS EN EL BUCLE --}}
                                <button onclick="openOrderModal({{ $order->id }})" 
                                        class="text-slate-400 hover:text-orange-400 transition transform hover:scale-110" 
                                        title="Vista Rápida">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-slate-500">
                                <p>No hay actividad reciente</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- DATOS OCULTOS --}}
    <div id="dashboard-data" class="hidden"
         data-line-labels="{{ json_encode($chartLabels) }}"
         data-line-values="{{ json_encode($chartData) }}"
         data-pie-labels="{{ json_encode($topProductsLabels) }}"
         data-pie-values="{{ json_encode($topProductsValues) }}">
    </div>

</div>

{{-- MODAL VISTA RÁPIDA --}}
<div id="order-modal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-900/80 backdrop-blur-sm transition-opacity" onclick="closeOrderModal()"></div>

    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center">
            <div class="relative transform overflow-hidden rounded-2xl bg-slate-800 border border-slate-700 text-left shadow-2xl transition-all sm:w-full sm:max-w-lg">
                <div class="bg-slate-900 px-4 py-3 sm:px-6 border-b border-slate-700 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        <i class="fas fa-receipt text-orange-500"></i> Orden #<span id="modal-order-id">...</span>
                    </h3>
                    <button onclick="closeOrderModal()" class="text-slate-400 hover:text-white">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="px-4 py-4 sm:p-6">
                    <div class="flex justify-between mb-4 text-sm">
                        <div>
                            <p class="text-slate-400">Cliente</p>
                            <p class="text-white font-bold" id="modal-cliente">...</p>
                        </div>
                        <div class="text-right">
                            <p class="text-slate-400">Fecha</p>
                            <p class="text-white font-bold" id="modal-fecha">...</p>
                        </div>
                    </div>
                    <div class="bg-slate-900/50 rounded-lg p-3 mb-4 max-h-40 overflow-y-auto custom-scrollbar" id="modal-items-container"></div>
                    <div class="flex justify-between items-center border-t border-slate-700 pt-3">
                        <span class="text-slate-400 font-bold uppercase text-xs" id="modal-status">PENDIENTE</span>
                        <div class="text-right">
                            <span class="text-slate-400 text-xs">Total</span>
                            <p class="text-2xl font-bold text-emerald-400">$<span id="modal-total">0.00</span></p>
                        </div>
                    </div>
                </div>
                <div class="bg-slate-900 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" onclick="closeOrderModal()" class="mt-3 inline-flex w-full justify-center rounded-md bg-slate-700 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-slate-600 sm:mt-0 sm:w-auto">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @vite(['resources/js/admin/dashboard.js'])
@endpush

@endsection