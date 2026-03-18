@extends('layouts.admin')

@section('titulo', __('admin/dashboard.title'))

{{-- DEFINIMOS EL SÍMBOLO DE MONEDA SEGÚN EL IDIOMA --}}
@php
    $currencySymbol = app()->getLocale() == 'pt' ? 'R$' : '$';
@endphp

@section('contenido')
<div class="space-y-8 max-w-7xl mx-auto">

    <div class="flex flex-col lg:flex-row justify-between items-end lg:items-center gap-6 bg-slate-800 p-6 rounded-3xl border border-slate-300 shadow-lg relative overflow-hidden">
        <div class="absolute -right-20 -top-20 w-64 h-64 bg-orange-500/10 rounded-full blur-[80px] pointer-events-none"></div>

        <div class="relative z-10">
            <h1 class="text-3xl font-black text-white tracking-tight flex items-center gap-3">
                {{ __('admin/dashboard.title') }} <i class="fas fa-fire text-orange-500"></i>
            </h1>
            <p class="text-slate-300 mt-1">{{ __('admin/dashboard.subtitle') }}</p>
        </div>
        
        <div class="flex flex-wrap items-center gap-3 relative z-10 w-full lg:w-auto">
            {{-- BOTONES DE FILTRO AJAX --}}
            <div class="hidden sm:flex bg-slate-900 border border-slate-300 p-1 rounded-xl" id="filter-container">
                @php
                    $currentFilter = request('filter', 'month');
                @endphp
                
                <button data-filter="today" class="filter-btn px-4 py-1.5 text-xs font-bold transition rounded-lg {{ $currentFilter == 'today' ? 'text-white bg-orange-700 shadow active' : 'text-slate-300 hover:text-white hover:bg-slate-800' }}">
                    {{ __('admin/dashboard.filter_today') }}
                </button>
                
                <button data-filter="month" class="filter-btn px-4 py-1.5 text-xs font-bold transition rounded-lg {{ $currentFilter == 'month' ? 'text-white bg-orange-700 shadow active' : 'text-slate-300 hover:text-white hover:bg-slate-800' }}">
                    {{ __('admin/dashboard.filter_month') }}
                </button>
                
                <button data-filter="year" class="filter-btn px-4 py-1.5 text-xs font-bold transition rounded-lg {{ $currentFilter == 'year' ? 'text-white bg-orange-700 shadow active' : 'text-slate-300 hover:text-white hover:bg-slate-800' }}">
                    {{ __('admin/dashboard.filter_year') }}
                </button>
            </div>

            <div class="h-8 w-px bg-slate-700 mx-2 hidden lg:block"></div>

            <a href="{{ route('admin.reports.daily') }}" target="_blank" class="bg-slate-900 hover:bg-slate-700 text-white border border-slate-300 px-4 py-2.5 rounded-xl text-sm font-bold flex items-center gap-2 transition flex-1 justify-center shadow-inner group">
                <i class="fas fa-file-pdf text-red-500 group-hover:scale-110 transition-transform"></i> {{ __('admin/dashboard.btn_report') }}
            </a>
            
            <a href="{{ route('admin.reports.excel') }}" target="_blank" class="bg-slate-900 hover:bg-slate-700 text-white border border-slate-300 px-4 py-2.5 rounded-xl text-sm font-bold flex items-center gap-2 transition flex-1 justify-center shadow-inner group">
                <i class="fas fa-file-excel text-emerald-500 group-hover:scale-110 transition-transform"></i> {{ __('admin/dashboard.btn_excel') }}
            </a>
        </div>
    </div>

    {{-- MÉTRICAS PRINCIPALES --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <div class="bg-slate-800 border border-slate-300 p-6 rounded-3xl shadow-lg relative overflow-hidden group hover:border-emerald-500/50 transition-colors">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-slate-300 text-[10px] font-bold uppercase tracking-widest mb-1">{{ __('admin/dashboard.revenue_month') }}</p>
                    {{-- USO DEL SÍMBOLO DINÁMICO --}}
                    <h2 class="text-3xl font-black text-white" id="metric-revenue">{{ $currencySymbol }}{{ number_format($ingresosMensuales, 2) }}</h2>
                </div>
                <div class="w-10 h-10 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-lg transform group-hover:rotate-12 transition-transform shadow-inner">
                    <i class="fas fa-wallet"></i>
                </div>
            </div>
            <div class="flex items-center gap-2 text-xs">
                <span class="text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded font-bold">+12%</span>
                <span class="text-slate-400">{{ __('admin/dashboard.vs_last_month') }}</span>
            </div>
        </div>

        <div class="bg-slate-800 border border-slate-300 p-6 rounded-3xl shadow-lg relative overflow-hidden group hover:border-orange-500/50 transition-colors">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-slate-300 text-[10px] font-bold uppercase tracking-widest mb-1">{{ __('admin/dashboard.pending_orders') }}</p>
                    <h2 class="text-3xl font-black text-white" id="metric-pending">{{ $pedidosPendientes }}</h2>
                </div>
                <div class="w-10 h-10 rounded-xl bg-orange-500/20 text-orange-400 flex items-center justify-center text-lg transform group-hover:-rotate-12 transition-transform shadow-inner">
                    <i class="fas fa-fire"></i>
                </div>
            </div>
            <div class="w-full bg-slate-900 rounded-full h-1.5 mt-2">
                <div class="bg-orange-500 h-1.5 rounded-full" style="width: 45%"></div>
            </div>
        </div>

        <div class="bg-slate-800 border border-slate-300 p-6 rounded-3xl shadow-lg relative overflow-hidden group hover:border-blue-500/50 transition-colors">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-slate-300 text-[10px] font-bold uppercase tracking-widest mb-1">{{ __('admin/dashboard.active_menu') }}</p>
                    <h2 class="text-3xl font-black text-white" id="metric-products">{{ $totalProductos }}</h2>
                </div>
                <div class="w-10 h-10 rounded-xl bg-blue-500/20 text-blue-400 flex items-center justify-center text-lg transform group-hover:scale-110 transition-transform shadow-inner">
                    <i class="fas fa-hamburger"></i>
                </div>
            </div>
            <a href="{{ route('admin.products.index') }}" class="text-blue-400 text-xs font-bold flex items-center gap-1 hover:text-blue-300 transition mt-2">
                {{ __('admin/dashboard.manage_catalog') }} <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        
        <div class="bg-slate-800 border border-slate-300 p-6 rounded-3xl shadow-lg relative overflow-hidden group hover:border-purple-500/50 transition-colors">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-slate-300 text-[10px] font-bold uppercase tracking-widest mb-1">{{ __('admin/dashboard.registered_clients') }}</p>
                    <h2 class="text-3xl font-black text-white" id="metric-clients">{{ $totalClientes }}</h2>
                </div>
                <div class="w-10 h-10 rounded-xl bg-purple-500/20 text-purple-400 flex items-center justify-center text-lg transform group-hover:scale-110 transition-transform shadow-inner">
                    <i class="fas fa-users"></i>
                </div>
            </div>
            <div class="flex items-center gap-2 text-xs">
                <span class="text-purple-400 bg-purple-500/10 px-2 py-0.5 rounded font-bold">+5</span>
                <span class="text-slate-400">{{ __('admin/dashboard.this_week') }}</span>
            </div>
        </div>
    </div>

    {{-- ACCESOS DIRECTOS --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <a href="{{ route('kitchen.live') }}" class="bg-slate-800 border border-slate-300 hover:border-orange-500 p-5 rounded-3xl shadow-lg flex items-center justify-between group transition-all transform hover:-translate-y-1">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-orange-500/20 text-orange-500 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform"><i class="fas fa-tv"></i></div>
                <div>
                    <h2 class="text-white font-bold text-lg">{{ __('admin/dashboard.kitchen_monitor') }}</h2>
                    <p class="text-xs text-slate-400">{{ __('admin/dashboard.kitchen_desc') }}</p>
                </div>
            </div>
            <i class="fas fa-chevron-right text-slate-400 group-hover:text-orange-500 transition-colors"></i>
        </a>

        <a href="{{ route('employee.pos') }}" class="bg-slate-800 border border-slate-300 hover:border-blue-500 p-5 rounded-3xl shadow-lg flex items-center justify-between group transition-all transform hover:-translate-y-1">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-blue-500/20 text-blue-500 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform"><i class="fas fa-cash-register"></i></div>
                <div>
                    <h2 class="text-white font-bold text-lg">{{ __('admin/dashboard.pos') }}</h2>
                    <p class="text-xs text-slate-400">{{ __('admin/dashboard.pos_desc') }}</p>
                </div>
            </div>
            <i class="fas fa-chevron-right text-slate-400 group-hover:text-blue-500 transition-colors"></i>
        </a>

        <a href="{{ route('admin.users.index') }}" class="bg-slate-800 border border-slate-300 hover:border-purple-500 p-5 rounded-3xl shadow-lg flex items-center justify-between group transition-all transform hover:-translate-y-1">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-purple-500/20 text-purple-500 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform"><i class="fas fa-user-tie"></i></div>
                <div>
                    <h2 class="text-white font-bold text-lg">{{ __('admin/dashboard.staff') }}</h2>
                    <p class="text-xs text-slate-400">{{ __('admin/dashboard.staff_desc') }}</p>
                </div>
            </div>
            <i class="fas fa-chevron-right text-slate-400 group-hover:text-purple-500 transition-colors"></i>
        </a>
    </div>

    {{-- GRÁFICAS --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="lg:col-span-2 bg-slate-800 border border-slate-300 rounded-3xl shadow-xl p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="font-black text-white text-lg flex items-center gap-2">
                    <i class="fas fa-chart-area text-emerald-400"></i> {{ __('admin/dashboard.sales_evolution') }}
                </h2>
            </div>
            <div class="relative h-[300px] w-full">
                <canvas id="myAreaChart"></canvas>
            </div>
        </div>

        <div class="bg-slate-800 border border-slate-300 rounded-3xl shadow-xl p-6 flex flex-col justify-between">
            <h2 class="font-black text-white text-lg mb-2 flex items-center gap-2">
                <i class="fas fa-trophy text-yellow-500"></i> {{ __('admin/dashboard.top_selling') }}
            </h2>
            <p class="text-xs text-slate-400 mb-4">{{ __('admin/dashboard.top_period') }}</p>
            <div class="relative h-[220px] w-full flex-1 flex justify-center">
                <canvas id="topProductsChart"></canvas>
            </div>
        </div>

        <div class="lg:col-span-3 bg-slate-800 border border-slate-300 rounded-3xl shadow-xl p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="font-black text-white text-lg flex items-center gap-2">
                    <i class="fas fa-clock text-blue-400"></i> {{ __('admin/dashboard.peak_hours') }}
                </h2>
                <span class="text-xs text-slate-400">{{ __('admin/dashboard.peak_hours_desc') }}</span>
            </div>
            <div class="relative h-[250px] w-full">
                <canvas id="peakHoursChart"></canvas>
            </div>
        </div>
    </div>

    {{-- TABLA DE PEDIDOS RECIENTES --}}
    <div class="bg-slate-800 border border-slate-300 rounded-3xl shadow-xl overflow-hidden">
        <div class="p-6 border-b border-slate-300 flex justify-between items-center bg-slate-900/50">
            <h2 class="font-black text-white text-lg flex items-center gap-2">
                <i class="fas fa-list-alt text-blue-400"></i> {{ __('admin/dashboard.latest_orders') }}
            </h2>
            <a href="{{ route('admin.orders.index') }}" class="text-sm bg-blue-600 hover:bg-blue-500 text-white px-4 py-2 rounded-xl font-bold transition shadow-lg shadow-blue-900/30">
                {{ __('admin/dashboard.view_full_history') }}
            </a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-slate-300">
                <thead class="text-[10px] font-black text-slate-300 uppercase tracking-widest bg-slate-900/80">
                    <tr>
                        <th class="px-6 py-4">{{ __('admin/dashboard.col_order_id') }}</th>
                        <th class="px-6 py-4">{{ __('admin/dashboard.col_client') }}</th>
                        <th class="px-6 py-4">{{ __('admin/dashboard.col_amount') }}</th>
                        <th class="px-6 py-4">{{ __('admin/dashboard.col_status') }}</th>
                        <th class="px-6 py-4">{{ __('admin/dashboard.col_time') }}</th>
                        <th class="px-6 py-4 text-right">{{ __('admin/dashboard.col_action') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/50">
                    @forelse($recentOrders as $order)
                        <tr class="hover:bg-slate-700/30 transition-colors group">
                            <td class="px-6 py-4 font-black text-white text-base">#{{ $order->id }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-slate-700 flex items-center justify-center text-xs text-white font-black border border-slate-600 shadow-inner">
                                        {{ substr($order->cliente_nombre ?? __('admin/dashboard.guest'), 0, 2) }}
                                    </div>
                                    <span class="font-bold text-slate-200">{{ $order->cliente_nombre ?? __('admin/dashboard.guest') }}</span>
                                </div>
                            </td>
                            {{-- USO DEL SÍMBOLO DINÁMICO EN LA TABLA --}}
                            <td class="px-6 py-4 font-mono font-bold text-emerald-400">{{ $currencySymbol }}{{ number_format($order->total, 2) }}</td>
                            <td class="px-6 py-4">
                                @php
                                    $statusColor = match($order->status) {
                                        'pendiente' => 'text-orange-400 bg-orange-500/10 border border-orange-500/20',
                                        'cocinando', 'preparando' => 'text-yellow-400 bg-yellow-500/10 border border-yellow-500/20',
                                        'listo', 'en_camino' => 'text-blue-400 bg-blue-500/10 border border-blue-500/20',
                                        'entregado' => 'text-emerald-400 bg-emerald-500/10 border border-emerald-500/20',
                                        default => 'text-slate-300 bg-slate-500/10 border border-slate-500/20'
                                    };
                                @endphp
                                <span class="{{ $statusColor }} px-3 py-1 rounded-md text-[10px] font-black uppercase tracking-widest inline-block">
                                    {{ strtoupper($order->status_traducido) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-400">{{ $order->created_at->diffForHumans(null, true, true) }}</td>
                            <td class="px-6 py-4 text-right">
                                <button aria-label="{{ __('admin/dashboard.view_ticket') }}" onclick="window.openOrderModal({{ $order->id }})" 
                                        class="bg-slate-700/50 text-slate-300 hover:bg-orange-700 hover:text-white h-9 w-9 rounded-lg flex items-center justify-center ml-auto transition-all border border-slate-600 hover:border-orange-500" 
                                        title="{{ __('admin/dashboard.view_ticket') }}">
                                    <i class="fas fa-eye text-xs pointer-events-none"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                <div class="bg-slate-900 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 border border-slate-800">
                                    <i class="fas fa-receipt text-2xl text-slate-400"></i>
                                </div>
                                <p class="text-white font-bold text-lg">{{ __('admin/dashboard.no_recent_orders') }}</p>
                                <p class="text-sm">{{ __('admin/dashboard.no_movements_today') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div id="dashboard-data" class="hidden"
         data-line-labels="{{ json_encode($chartLabels) }}"
         data-line-values="{{ json_encode($chartData) }}"
         data-pie-labels="{{ json_encode($topProductsLabels) }}"
         data-pie-values="{{ json_encode($topProductsValues) }}"
         data-bar-labels="{{ json_encode($peakHoursLabels ?? []) }}"  
         data-bar-values="{{ json_encode($peakHoursValues ?? []) }}">
    </div>

</div>

{{-- MODAL TICKET --}}
<div id="order-modal" class="fixed inset-0 z-[100] hidden flex items-center justify-center p-4 sm:p-6 opacity-0 transition-opacity duration-300" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="absolute inset-0 bg-black/80 backdrop-blur-md" onclick="closeOrderModal()"></div>

    <div class="relative w-full max-w-md bg-slate-900 border border-slate-300 rounded-3xl shadow-2xl overflow-hidden transform scale-95 transition-all duration-300 flex flex-col max-h-[90vh]" id="order-modal-panel">
        
        <div class="bg-slate-800 p-6 text-center border-b border-slate-300 relative overflow-hidden">
            <div class="absolute -right-4 -top-4 text-slate-700/30">
                <i class="fas fa-hamburger text-6xl"></i>
            </div>
            <h2 class="text-sm font-bold text-slate-300 uppercase tracking-widest mb-1">{{ __('admin/dashboard.modal_ticket') }}</h2>
            <p class="text-3xl font-black text-white font-mono">#<span id="modal-order-id">...</span></p>
            <span class="inline-block mt-3 bg-slate-900 text-slate-300 border border-slate-300 px-3 py-1 rounded-full text-xs font-bold" id="modal-status"></span>
            
            <button aria-label="Cerrar modal de detalles" onclick="closeOrderModal()" class="absolute top-4 right-4 w-8 h-8 bg-slate-700/50 hover:bg-red-500 rounded-full flex items-center justify-center text-white transition-colors z-10">
                <i class="fas fa-times text-sm pointer-events-none"></i>
            </button>
        </div>

        <div class="px-6 py-4 flex justify-between items-center text-sm border-b border-slate-800 bg-slate-900/50">
            <div>
                <p class="text-slate-400 text-xs font-bold uppercase tracking-wider">{{ __('admin/dashboard.modal_client') }}</p>
                <p class="text-white font-bold" id="modal-cliente">...</p>
            </div>
            <div class="text-right">
                <p class="text-slate-400 text-xs font-bold uppercase tracking-wider">{{ __('admin/dashboard.modal_time') }}</p>
                <p class="text-slate-300 font-mono" id="modal-fecha">...</p>
            </div>
        </div>

        <div class="p-6 overflow-y-auto custom-scrollbar flex-1 bg-slate-900">
            <p class="text-slate-400 text-xs font-bold uppercase tracking-wider mb-3">{{ __('admin/dashboard.modal_details') }}</p>
            <div id="modal-items-container" class="space-y-3">
            </div>
        </div>

        <div class="bg-slate-800 p-6 border-t border-slate-300 shadow-[0_-10px_20px_rgba(0,0,0,0.2)]">
            <div class="flex justify-between items-end mb-4">
                <span class="text-slate-300 font-bold uppercase tracking-wider">{{ __('admin/dashboard.modal_total') }}</span>
                {{-- USO DEL SÍMBOLO DINÁMICO EN EL MODAL --}}
                <p class="text-3xl font-black text-emerald-400 font-mono">{{ $currencySymbol }}<span id="modal-total">0.00</span></p>
            </div>
            <a href="#" id="modal-link-completo" class="w-full block text-center bg-orange-700 hover:bg-orange-500 text-white font-bold py-3.5 rounded-xl transition shadow-lg shadow-orange-900/30">
                {{ __('admin/dashboard.modal_view_full') }}
            </a>
        </div>
    </div>
</div>

<script>
    // PASAMOS EL SÍMBOLO DE MONEDA A JAVASCRIPT
    window.ADMIN_LANG = {
        currency_symbol: `{{ $currencySymbol }}`,
        revenue_mxn: `{{ __('admin/dashboard.js_revenue_mxn') }}`,
        loading_ticket: `{{ __('admin/dashboard.js_loading_ticket') }}`,
        server_error: `{{ __('admin/dashboard.js_server_error') }}`,
        status_pending: `{{ __('admin/dashboard.js_status_pending') }}`,
        status_ready: `{{ __('admin/dashboard.js_status_ready') }}`,
        status_delivered: `{{ __('admin/dashboard.js_status_delivered') }}`,
        orders: `{{ __('admin/dashboard.js_orders') }}`
    };
</script>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @vite(['resources/js/admin/dashboard.js'])
@endpush

@endsection