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
@endsection