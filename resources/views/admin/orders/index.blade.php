@extends('layouts.admin')

@section('titulo', __('admin/orders/orders.title_index'))

@section('contenido')
<div class="py-6 space-y-6 max-w-7xl mx-auto">

    <div class="flex justify-between items-end">
        <div>
            <h1 class="text-3xl font-black text-white"><i class="fas fa-list-alt text-blue-500 mr-2"></i> {{ __('admin/orders/orders.header_title') }}</h1>
            <p class="text-slate-400">{{ __('admin/orders/orders.header_subtitle') }}</p>
        </div>
    </div>

    {{-- TARJETAS --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-slate-800 p-6 rounded-3xl border border-slate-300 shadow-lg flex items-center gap-4">
            <div class="h-14 w-14 rounded-2xl bg-orange-500/20 flex items-center justify-center text-orange-400 text-2xl"><i class="fas fa-fire"></i></div>
            <div>
                <p class="text-slate-400 text-[10px] font-bold uppercase tracking-widest">{{ __('admin/orders/orders.stats_process') }}</p>
                <p class="text-3xl font-black text-white">{{ $stats->total_pendientes }}</p>
            </div>
        </div>
        <div class="bg-slate-800 p-6 rounded-3xl border border-slate-300 shadow-lg flex items-center gap-4">
            <div class="h-14 w-14 rounded-2xl bg-emerald-500/20 flex items-center justify-center text-emerald-400 text-2xl"><i class="fas fa-check-double"></i></div>
            <div>
                <p class="text-slate-400 text-[10px] font-bold uppercase tracking-widest">{{ __('admin/orders/orders.stats_delivered') }}</p>
                <p class="text-3xl font-black text-white">{{ $stats->total_entregados }}</p>
            </div>
        </div>
        <div class="bg-slate-800 p-6 rounded-3xl border border-slate-300 shadow-lg flex items-center gap-4">
            <div class="h-14 w-14 rounded-2xl bg-blue-500/20 flex items-center justify-center text-blue-400 text-2xl"><i class="fas fa-dollar-sign"></i></div>
            <div>
                <p class="text-slate-400 text-[10px] font-bold uppercase tracking-widest">{{ __('admin/orders/orders.stats_income') }}</p>
                <p class="text-3xl font-black text-white">{{ formatCurrency($stats->ingresos_hoy) }}</p>
            </div>
        </div>
    </div>

    <div class="bg-slate-800 p-5 rounded-3xl border border-slate-300 shadow-lg">
        <form action="{{ route('admin.orders.index') }}" method="GET" id="filtro-ordenes">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                
                {{-- Búsqueda por Texto --}}
                <div class="relative lg:col-span-2">
                    <span class="absolute left-4 top-3 text-slate-500"><i class="fas fa-search"></i></span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('admin/orders/orders.filter_search_placeholder') }}" 
                           class="w-full bg-slate-900 border border-slate-600 text-white rounded-xl py-2.5 pl-11 pr-4 focus:outline-none focus:border-blue-500 transition-colors text-sm placeholder-slate-500">
                </div>

                {{-- Filtro de Estado --}}
                <div class="relative">
                    <select name="status" class="w-full bg-slate-900 border border-slate-600 text-white rounded-xl py-2.5 px-4 appearance-none focus:outline-none focus:border-blue-500 transition-colors text-sm cursor-pointer filter-dropdown">
                        <option value="">{{ __('admin/orders/orders.filter_any_status') }}</option>
                        <option value="pendiente" {{ request('status') == 'pendiente' ? 'selected' : '' }}>{{ __('admin/orders/orders.status_pendiente') }}</option>
                        <option value="preparando" {{ request('status') == 'preparando' ? 'selected' : '' }}>{{ __('admin/orders/orders.status_preparando') }}</option>
                        <option value="listo" {{ request('status') == 'listo' ? 'selected' : '' }}>{{ __('admin/orders/orders.status_listo') }}</option>
                        <option value="entregado" {{ request('status') == 'entregado' ? 'selected' : '' }}>{{ __('admin/orders/orders.status_entregado') }}</option>
                        <option value="cancelado" {{ request('status') == 'cancelado' ? 'selected' : '' }}>{{ __('admin/orders/orders.status_cancelado') }}</option>
                    </select>
                    <i class="fas fa-chevron-down absolute right-4 top-3.5 text-slate-500 text-xs pointer-events-none"></i>
                </div>

                {{-- Filtro de Tipo de Pedido --}}
                <div class="relative">
                    <select name="tipo" class="w-full bg-slate-900 border border-slate-600 text-white rounded-xl py-2.5 px-4 appearance-none focus:outline-none focus:border-blue-500 transition-colors text-sm cursor-pointer filter-dropdown">
                        <option value="">{{ __('admin/orders/orders.filter_any_type') }}</option>
                        <option value="domicilio" {{ request('tipo') == 'domicilio' ? 'selected' : '' }}>{{ __('admin/orders/orders.type_delivery') }}</option>
                        <option value="llevar" {{ request('tipo') == 'llevar' ? 'selected' : '' }}>{{ __('admin/orders/orders.type_takeaway') }}</option>
                        <option value="mesa" {{ request('tipo') == 'mesa' ? 'selected' : '' }}>{{ __('admin/orders/orders.type_dine_in') }}</option>
                    </select>
                    <i class="fas fa-chevron-down absolute right-4 top-3.5 text-slate-500 text-xs pointer-events-none"></i>
                </div>

                {{-- Fechas (Rango con regionalización automática del navegador) --}}
                {{-- Fechas (Controladas por el idioma de Laravel con Flatpickr) --}}
                <div class="flex gap-2 lg:col-span-2">
                    <div class="relative flex-1">
                        <span class="absolute left-3 top-2.5 text-slate-500 z-10"><i class="far fa-calendar-alt"></i></span>
                        <input type="text" name="fecha_inicio" value="{{ request('fecha_inicio') }}" placeholder="{{ __('admin/orders/orders.filter_date_start') }}"
                               class="flatpickr-date w-full bg-slate-900 border border-slate-600 text-white rounded-xl py-2.5 pl-10 pr-2 focus:outline-none focus:border-blue-500 transition-colors text-sm placeholder-slate-500 cursor-pointer">
                    </div>
                    <span class="text-slate-600 flex items-center">-</span>
                    <div class="relative flex-1">
                        <span class="absolute left-3 top-2.5 text-slate-500 z-10"><i class="far fa-calendar-check"></i></span>
                        <input type="text" name="fecha_fin" value="{{ request('fecha_fin') }}" placeholder="{{ __('admin/orders/orders.filter_date_end') }}"
                               class="flatpickr-date w-full bg-slate-900 border border-slate-600 text-white rounded-xl py-2.5 pl-10 pr-2 focus:outline-none focus:border-blue-500 transition-colors text-sm placeholder-slate-500 cursor-pointer">
                    </div>
                </div>

                {{-- Filtro Método de Pago --}}
                <div class="relative">
                    <select name="pago" class="w-full bg-slate-900 border border-slate-600 text-white rounded-xl py-2.5 px-4 appearance-none focus:outline-none focus:border-blue-500 transition-colors text-sm cursor-pointer filter-dropdown">
                        <option value="">{{ __('admin/orders/orders.filter_any_payment') }}</option>
                        <option value="efectivo" {{ request('pago') == 'efectivo' ? 'selected' : '' }}>{{ __('admin/orders/orders.pay_cash') }}</option>
                        <option value="tarjeta_entrega" {{ request('pago') == 'tarjeta_entrega' ? 'selected' : '' }}>{{ __('admin/orders/orders.pay_terminal') }}</option>
                        <option value="stripe" {{ request('pago') == 'stripe' ? 'selected' : '' }}>{{ __('admin/orders/orders.pay_online') }}</option>
                    </select>
                    <i class="fas fa-chevron-down absolute right-4 top-3.5 text-slate-500 text-xs pointer-events-none"></i>
                </div>

                {{-- Botones de Acción --}}
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-500 text-white font-bold py-2.5 rounded-xl transition shadow-lg shadow-blue-900/20 flex justify-center items-center gap-2 text-sm">
                        <i class="fas fa-filter"></i> {{ __('admin/orders/orders.filter_btn') }}
                    </button>
                    
                    @if(request()->anyFilled(['search', 'status', 'tipo', 'fecha_inicio', 'fecha_fin', 'pago']))
                        <a href="{{ route('admin.orders.index') }}" title="{{ __('admin/orders/orders.filter_clear') }}" class="flex items-center justify-center bg-slate-700 text-slate-300 hover:bg-red-500 hover:text-white px-4 rounded-xl transition-colors border border-slate-600">
                            <i class="fas fa-eraser"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    {{-- TABLA --}}
    <div class="bg-slate-800 border border-slate-300 rounded-3xl shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-slate-400">
                <thead class="text-[10px] font-black text-slate-500 uppercase tracking-widest bg-slate-900/80">
                    <tr>
                        <th class="px-6 py-4">{{ __('admin/orders/orders.col_order') }}</th>
                        <th class="px-6 py-4">{{ __('admin/orders/orders.col_date') }}</th>
                        <th class="px-6 py-4">{{ __('admin/orders/orders.col_client') }}</th>
                        <th class="px-6 py-4">{{ __('admin/orders/orders.col_type') }}</th>
                        <th class="px-6 py-4">{{ __('admin/orders/orders.col_payment') }}</th>
                        <th class="px-6 py-4">{{ __('admin/orders/orders.col_total') }}</th>
                        <th class="px-6 py-4">{{ __('admin/orders/orders.col_status') }}</th>
                        <th class="px-6 py-4 text-right">{{ __('admin/orders/orders.col_details') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/50">
                    @php
                        $formatoFecha = app()->getLocale() == 'en' ? 'm/d/Y H:i' : 'd/m/Y H:i';
                    @endphp
                    
                    @forelse($pedidos as $pedido)
                        <tr class="hover:bg-slate-700/30 transition-colors {{ strtolower($pedido->status) == 'cancelado' ? 'opacity-50 grayscale' : '' }}">
                            <td class="px-6 py-4 font-black text-white text-base">#{{ $pedido->id }}</td>
                            <td class="px-6 py-4 text-xs font-mono">{{ $pedido->created_at->format($formatoFecha) }}</td>
                            <td class="px-6 py-4 font-bold text-slate-300">{{ $pedido->cliente_nombre ?? __('admin/orders/orders.guest') }}</td>
                            
                            {{-- TIPO DE PEDIDO CORRECTO --}}
                            <td class="px-6 py-4">
                                @if($pedido->es_domicilio)
                                    <span class="text-blue-400 font-bold text-xs uppercase tracking-wider"><i class="fas fa-motorcycle mr-1"></i> {{ __('admin/orders/orders.type_delivery') }}</span>
                                @elseif($pedido->tipo_real === 'llevar')
                                    <span class="text-yellow-400 font-bold text-xs uppercase tracking-wider"><i class="fas fa-shopping-bag mr-1"></i> {{ __('admin/orders/orders.type_takeaway') }}</span>
                                @else
                                    <span class="text-orange-400 font-bold text-xs uppercase tracking-wider"><i class="fas fa-utensils mr-1"></i> {{ __('admin/orders/orders.type_dine_in') }}</span>
                                @endif
                            </td>

                            
                            <td class="px-6 py-4">
                                @if($pedido->metodo_pago === 'tarjeta_entrega')
                                    <span class="text-blue-400 font-bold text-xs uppercase tracking-wider"><i class="fas fa-terminal mr-1"></i> {{ __('admin/orders/orders.pay_terminal') }}</span>
                                @elseif($pedido->metodo_pago === 'stripe')
                                    <span class="text-purple-400 font-bold text-xs uppercase tracking-wider"><i class="fab fa-stripe text-lg mr-1 translate-y-0.5"></i> {{ __('admin/orders/orders.pay_online') }}</span>
                                @else
                                    <span class="text-emerald-400 font-bold text-xs uppercase tracking-wider"><i class="fas fa-money-bill-wave mr-1"></i> {{ __('admin/orders/orders.pay_cash') }}</span>
                                @endif
                            </td>
                            
                            <td class="px-6 py-4 font-mono font-bold text-emerald-400">{{ formatCurrency($pedido->total) }}</td>
                            
                            {{-- COLUMNA DE ESTADO RECUPERADA --}}
                            <td class="px-6 py-4">
                                @php
                                    $statusColor = match(strtolower($pedido->status)) {
                                        'pendiente' => 'text-orange-400 bg-orange-500/10 border-orange-500/20',
                                        'cocinando', 'preparando' => 'text-yellow-400 bg-yellow-500/10 border-yellow-500/20',
                                        'listo', 'en_camino' => 'text-blue-400 bg-blue-500/10 border-blue-500/20',
                                        'entregado' => 'text-emerald-400 bg-emerald-500/10 border-emerald-500/20',
                                        'cancelado' => 'text-red-400 bg-red-500/10 border-red-500/20',
                                        default => 'text-slate-400 bg-slate-500/10 border-slate-500/20'
                                    };
                                @endphp
                                <span class="{{ $statusColor }} px-3 py-1 rounded-md text-[10px] font-black uppercase tracking-widest inline-block border">
                                    {{ $pedido->status_traducido }}
                                </span>
                            </td>
                            
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.orders.show', $pedido->id) }}" class="bg-slate-700/50 text-slate-300 hover:bg-blue-600 hover:text-white px-3 py-1.5 rounded-lg flex items-center justify-center ml-auto transition-all border border-slate-600 hover:border-blue-500 w-fit text-xs font-bold gap-2">
                                    {{ __('admin/orders/orders.btn_ticket') }} <i class="fas fa-arrow-right"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-500">{{ __('admin/orders/orders.empty_table') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($pedidos->hasPages())
            <div class="p-4 bg-slate-900 border-t border-slate-300">
                {{ $pedidos->links() }}
            </div>
        @endif
    </div>
</div>

{{-- LIBRERÍAS DE FLATPICKR Y CONFIGURACIÓN REGIONAL --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/dark.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Detectamos el idioma actual de Laravel
        const idiomaActual = '{{ app()->getLocale() }}';
        
        // Estados Unidos (en) usa Mes/Día/Año. México (es) y Brasil (pt) usan Día/Mes/Año.
        const formatoVisual = idiomaActual === 'en' ? 'm/d/Y' : 'd/m/Y';

        flatpickr(".flatpickr-date", {
            dateFormat: "Y-m-d",      // El formato oculto que se envía al Backend (Laravel necesita este)
            altInput: true,           // Crea un input falso visualmente atractivo
            altFormat: formatoVisual, // El formato regionalizado que ve el usuario
            theme: "dark"
        });
    });
</script>
@endsection