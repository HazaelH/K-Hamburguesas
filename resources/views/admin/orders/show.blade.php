@extends('layouts.admin')

@section('titulo', __('admin/orders/orders.title_show') . $id)

@section('contenido')
<div class="max-w-4xl mx-auto space-y-6 py-6">

    {{-- Botón de regreso --}}
    <a href="{{ route('admin.orders.index') }}" class="text-slate-400 hover:text-white transition flex items-center gap-2 font-bold w-fit bg-slate-800 px-4 py-2 rounded-xl border border-slate-700">
        <i class="fas fa-arrow-left"></i> {{ __('admin/orders/orders.btn_back') }}
    </a>

    {{-- Tarjeta Principal del Ticket --}}
    <div class="bg-slate-800 rounded-3xl border border-slate-700 shadow-2xl overflow-hidden relative">
        
        {{-- Marca de agua Cancelado --}}
        @if($order->status === 'cancelado')
            <div class="absolute inset-0 flex items-center justify-center pointer-events-none z-0 opacity-20">
                <h1 class="text-8xl font-black text-red-500 transform -rotate-12 uppercase tracking-widest border-8 border-red-500 p-8 rounded-3xl">{{ __('admin/orders/orders.watermark_cancelled') }}</h1>
            </div>
        @endif

        {{-- Cabecera --}}
        <div class="bg-slate-900/80 p-8 border-b border-slate-700 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 relative overflow-hidden z-10">
            <div class="relative z-10">
                <h1 class="text-4xl font-black text-white font-mono tracking-tight">{{ __('admin/orders/orders.order_number') }}{{ $id }}</h1>
                <p class="text-slate-400 mt-1">
                    @php
                        $formatoHora = app()->getLocale() == 'en' ? 'm/d/Y - h:i A' : 'd/m/Y - h:i A';
                    @endphp
                    <i class="far fa-clock mr-1"></i> {{ $order->created_at->format($formatoHora) }}
                    <span class="mx-2 text-slate-600">|</span>
                    <span class="font-bold text-white uppercase tracking-widest text-xs">{{ $order->status_traducido }}</span>
                </p>
            </div>
            
            {{-- Botón para Marcar como Completado (SOLO SI NO ESTÁ CANCELADO NI ENTREGADO) --}}
            @if(!in_array($order->status, ['cancelado', 'entregado']))
                <form action="{{ route('admin.orders.complete', $id) }}" method="POST" class="relative z-10" onsubmit="return confirm('{{ __('admin/orders/orders.confirm_delivery') }}');">
                    @csrf
                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-3 px-6 rounded-xl shadow-lg shadow-emerald-900/30 transition transform hover:-translate-y-1 flex items-center gap-2">
                        <i class="fas fa-check-circle"></i> {{ __('admin/orders/orders.btn_mark_delivered') }}
                    </button>
                </form>
            @endif
        </div>

        <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-8 relative z-10">
            
            {{-- Info del Cliente --}}
            <div class="space-y-4">
                <h3 class="text-lg font-black text-white uppercase tracking-widest border-b border-slate-700 pb-2"><i class="fas fa-user text-orange-500 mr-2"></i> {{ __('admin/orders/orders.client_data') }}</h3>
                
                <div>
                    <p class="text-xs text-slate-500 font-bold uppercase tracking-wider">{{ __('admin/orders/orders.client_name') }}</p>
                    <p class="text-slate-300 font-bold text-lg">{{ $order->cliente_nombre ?? __('admin/orders/orders.guest') }}</p>
                </div>
                
                <div>
                    <p class="text-xs text-slate-500 font-bold uppercase tracking-wider">{{ __('admin/orders/orders.client_phone') }}</p>
                    <p class="text-slate-300 font-mono">{{ $order->telefono ?? __('admin/orders/orders.unregistered_phone') }}</p>
                </div>
            </div>

            {{-- Info de Entrega --}}
            <div class="space-y-4">
                <h3 class="text-lg font-black text-white uppercase tracking-widest border-b border-slate-700 pb-2"><i class="fas fa-map-marker-alt text-blue-500 mr-2"></i> {{ __('admin/orders/orders.order_details') }}</h3>
                
                @if(!$esDomicilio)
                    <div class="bg-orange-500/10 border border-orange-500/20 p-4 rounded-xl flex items-center gap-3">
                        @if($tipoBruto === 'llevar')
                            <i class="fas fa-shopping-bag text-orange-400 text-3xl"></i>
                            <div>
                                <p class="font-bold text-orange-400 uppercase tracking-widest text-xs">{{ __('admin/orders/orders.type_takeaway') }}</p>
                                <p class="text-sm text-slate-400 mt-0.5">{{ __('admin/orders/orders.desc_takeaway') }}</p>
                            </div>
                        @else
                            <i class="fas fa-utensils text-orange-400 text-3xl"></i>
                            <div>
                                <p class="font-bold text-orange-400 uppercase tracking-widest text-xs">{{ __('admin/orders/orders.desc_dine_in') }}</p>
                                <p class="text-sm text-slate-400 mt-0.5">{{ __('admin/orders/orders.desc_dine_in_sub') }}</p>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="bg-blue-500/10 border border-blue-500/20 p-4 rounded-xl flex items-center gap-3 mb-3">
                        <i class="fas fa-motorcycle text-blue-400 text-3xl"></i>
                        <div>
                            <p class="font-bold text-blue-400 uppercase tracking-widest text-xs">{{ __('admin/orders/orders.desc_delivery') }}</p>
                        </div>
                    </div>

                    @if(!empty($order->direccion))
                        <div>
                            <p class="text-xs text-slate-500 font-bold uppercase tracking-wider">{{ __('admin/orders/orders.address_title') }}</p>
                            <p class="text-slate-300 leading-relaxed text-sm mt-1 bg-slate-900/50 p-3 rounded-lg border border-slate-700">
                                {{ $order->direccion }}
                            </p>
                        </div>
                    @else
                        <div class="bg-red-500/10 border border-red-500/20 p-3 rounded-lg">
                            <p class="text-red-400 text-sm font-bold"><i class="fas fa-exclamation-triangle"></i> {{ __('admin/orders/orders.address_missing') }}</p>
                        </div>
                    @endif
                @endif
            </div>
        </div>

        {{-- Detalles de los Productos --}}
        <div class="bg-slate-900/50 p-8 border-t border-slate-700 relative z-10">
            <h3 class="text-lg font-black text-white uppercase tracking-widest mb-4"><i class="fas fa-shopping-basket text-emerald-500 mr-2"></i> {{ __('admin/orders/orders.purchase_summary') }}</h3>
            
            <div class="bg-slate-800 rounded-2xl border border-slate-700 overflow-hidden">
                <table class="w-full text-left text-slate-300">
                    <thead class="bg-slate-900 text-slate-500 text-xs uppercase font-black tracking-widest border-b border-slate-700">
                        <tr>
                            <th class="px-6 py-4">{{ __('admin/orders/orders.col_qty') }}</th>
                            <th class="px-6 py-4">{{ __('admin/orders/orders.col_product') }}</th>
                            <th class="px-6 py-4 text-right">{{ __('admin/orders/orders.col_unit_price') }}</th>
                            <th class="px-6 py-4 text-right">{{ __('admin/orders/orders.col_subtotal') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700/50">
                        @php $granTotal = 0; @endphp
                        @foreach($order->items as $item)
                            <tr class="hover:bg-slate-700/30 transition">
                                <td class="px-6 py-4 font-black text-orange-500">x{{ $item->cantidad }}</td>
                                <td class="px-6 py-4 font-bold">
                                    {{-- Nombre traducido del producto si existe --}}
                                    {{ $item->product ? $item->product->nombre_traducido : __('admin/orders/orders.unknown_product') }}
                                    
                                    @if($item->product && $item->product->trashed())
                                        <span class="text-[10px] bg-red-500/10 text-red-400 px-2 py-0.5 rounded border border-red-500/20 ml-2 uppercase tracking-widest">{{ __('admin/orders/orders.badge_obsolete') }}</span>
                                    @endif
                                    
                                    {{-- Mostramos las opciones de personalización (extras) si las hay --}}
                                    @if($item->opciones_elegidas)
                                        @php
                                            $opciones = json_decode($item->opciones_elegidas, true);
                                            $locale = app()->getLocale();
                                        @endphp
                                        @if(is_array($opciones) && count($opciones) > 0)
                                            <ul class="mt-1 text-xs text-slate-500 font-normal list-disc list-inside ml-4">
                                                @foreach($opciones as $opcion)
                                                    @php
                                                        // Determinamos qué idioma mostrar
                                                        $nombreOpcion = $opcion['nombre'] ?? 'Extra';
                                                        if ($locale === 'en' && !empty($opcion['nombre_en'])) {
                                                            $nombreOpcion = $opcion['nombre_en'];
                                                        } elseif ($locale === 'pt' && !empty($opcion['nombre_pt'])) {
                                                            $nombreOpcion = $opcion['nombre_pt'];
                                                        }
                                                    @endphp
                                                    <li>{{ $nombreOpcion }} ({{ formatCurrency($opcion['precio'] ?? 0) }})</li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right font-mono text-slate-400">{{ formatCurrency($item->precio_unitario) }}</td>
                                <td class="px-6 py-4 text-right font-mono font-bold text-emerald-400">{{ formatCurrency($item->cantidad * $item->precio_unitario) }}</td>
                            </tr>
                            @php $granTotal += ($item->cantidad * $item->precio_unitario); @endphp
                        @endforeach
                    </tbody>
                    <tfoot class="bg-slate-900/80 border-t border-slate-600">
                        <tr>
                            <td colspan="3" class="px-6 py-6 text-right font-black text-slate-400 uppercase tracking-widest">{{ __('admin/orders/orders.total_paid') }}</td>
                            <td class="px-6 py-6 text-right font-mono font-black text-3xl text-emerald-400">{{ formatCurrency($granTotal) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection