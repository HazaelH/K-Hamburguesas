@extends('layouts.admin')

@section('titulo', __('admin/orders/orders.title_show') . $id)

@section('contenido')
<div class="max-w-5xl mx-auto space-y-6 py-6">

    {{-- Botón de regreso --}}
    <a href="{{ route('admin.orders.index') }}" class="text-slate-400 hover:text-white transition flex items-center gap-2 font-bold w-fit bg-slate-800 px-4 py-2 rounded-xl border border-slate-300">
        <i class="fas fa-arrow-left"></i> {{ __('admin/orders/orders.btn_back') }}
    </a>

    {{-- Tarjeta Principal del Ticket --}}
    <div class="bg-slate-800 rounded-3xl border border-slate-300 shadow-2xl overflow-hidden relative">
        
        {{-- Marca de agua Cancelado --}}
        @if($order->status === 'cancelado')
            <div class="absolute inset-0 flex items-center justify-center pointer-events-none z-0 opacity-20">
                <h1 class="text-8xl font-black text-red-500 transform -rotate-12 uppercase tracking-widest border-8 border-red-500 p-8 rounded-3xl">{{ __('admin/orders/orders.watermark_cancelled') }}</h1>
            </div>
        @endif

        {{-- Cabecera --}}
        <div class="bg-slate-900/80 p-8 border-b border-slate-300 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 relative overflow-hidden z-10">
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
            
            {{-- Botón para Marcar como Completado --}}
            @if(!in_array($order->status, ['cancelado', 'entregado']))
                <form id="form-complete-order" action="{{ route('admin.orders.complete', $id) }}" method="POST" class="relative z-10">
                    @csrf
                    <button type="button" onclick="abrirModalCompletar()" class="bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-3 px-6 rounded-xl shadow-lg shadow-emerald-900/30 transition transform hover:-translate-y-1 flex items-center gap-2">
                        <i class="fas fa-check-circle"></i> {{ __('admin/orders/orders.btn_mark_delivered') }}
                    </button>
                </form>
            @endif
        </div>

        {{-- Cajas de Información (Ahora en 3 columnas) --}}
        <div class="p-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 relative z-10">
            
            {{-- Info del Cliente --}}
            <div class="space-y-4">
                <h3 class="text-lg font-black text-white uppercase tracking-widest border-b border-slate-300 pb-2"><i class="fas fa-user text-orange-500 mr-2"></i> {{ __('admin/orders/orders.client_data') }}</h3>
                
                <div>
                    <p class="text-xs text-slate-500 font-bold uppercase tracking-wider">{{ __('admin/orders/orders.client_name') }}</p>
                    <p class="text-slate-300 font-bold text-lg">{{ $order->cliente_nombre ?? __('admin/orders/orders.guest') }}</p>
                </div>
                
                <div>
                    <p class="text-xs text-slate-500 font-bold uppercase tracking-wider">{{ __('admin/orders/orders.client_phone') }}</p>
                    <p class="text-slate-300 font-mono">{{ $order->telefono ?? __('admin/orders/orders.unregistered_phone') }}</p>
                </div>
            </div>

            {{-- NUEVO: Información de Pago --}}
            <div class="space-y-4">
                <h3 class="text-lg font-black text-white uppercase tracking-widest border-b border-slate-300 pb-2"><i class="fas fa-wallet text-emerald-500 mr-2"></i> {{ __('admin/orders/orders.payment_method') }}</h3>
                
                <div class="bg-slate-900/50 p-4 rounded-xl border border-slate-300 shadow-inner">
                    @if($order->metodo_pago === 'tarjeta_entrega')
                        <p class="font-bold text-blue-400 uppercase tracking-widest text-xs mb-2 flex items-center gap-2">
                            <i class="fas fa-terminal text-lg"></i> {{ __('admin/orders/orders.pay_terminal') }}
                        </p>
                        
                        @php
                            $datosEntrega = is_string($order->datos_entrega) ? json_decode($order->datos_entrega, true) : ($order->datos_entrega ?? []);
                            $referenciaTerminal = $order->referencia_tarjeta ?? ($datosEntrega['terminal_ref'] ?? null);
                        @endphp
                        
                        @if($referenciaTerminal)
                            <p class="text-slate-400 text-xs font-bold uppercase mt-3 mb-1">{{ __('admin/orders/orders.verify_digits') }}</p>
                            <div class="bg-slate-800 border border-slate-600 rounded-lg px-3 py-2 flex items-center gap-2 w-fit">
                                <i class="fas fa-asterisk text-slate-500 text-[8px]"></i>
                                <i class="fas fa-asterisk text-slate-500 text-[8px]"></i>
                                <i class="fas fa-asterisk text-slate-500 text-[8px]"></i>
                                <i class="fas fa-asterisk text-slate-500 text-[8px]"></i>
                                <span class="font-mono font-black text-white text-base tracking-widest ml-1">{{ $referenciaTerminal }}</span>
                            </div>
                        @else
                            <p class="text-xs text-red-400 font-bold bg-red-500/10 px-2 py-1 rounded border border-red-500/20 inline-block"><i class="fas fa-exclamation-circle"></i> {{ __('admin/orders/orders.no_reference') }}</p>
                        @endif

                    @elseif($order->metodo_pago === 'stripe')
                        <p class="font-bold text-purple-400 uppercase tracking-widest text-xs flex items-center gap-2">
                            <i class="fab fa-stripe text-2xl"></i> {{ __('admin/orders/orders.pay_online') }}
                        </p>
                        <p class="text-xs text-slate-400 mt-2"><i class="fas fa-check-circle text-emerald-500"></i> {{ __('admin/orders/orders.payment_authorized') }}</p>
                    @else
                        <p class="font-bold text-emerald-400 uppercase tracking-widest text-xs flex items-center gap-2">
                            <i class="fas fa-money-bill-wave text-xl"></i> {{ __('admin/orders/orders.pay_cash') }}
                        </p>
                        <p class="text-xs text-slate-400 mt-2">{{ __('admin/orders/orders.collect_cash') }}</p>
                    @endif
                </div>
            </div>

            {{-- Info de Entrega --}}
            <div class="space-y-4">
                <h3 class="text-lg font-black text-white uppercase tracking-widest border-b border-slate-300 pb-2"><i class="fas fa-map-marker-alt text-blue-500 mr-2"></i> {{ __('admin/orders/orders.order_details') }}</h3>
                
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
                            <p class="text-slate-300 leading-relaxed text-sm mt-1 bg-slate-900/50 p-3 rounded-lg border border-slate-300">
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

        {{-- Detalles de los Productos (No se modifica, queda intacto) --}}
        <div class="bg-slate-900/50 p-8 border-t border-slate-300 relative z-10">
            <h3 class="text-lg font-black text-white uppercase tracking-widest mb-4"><i class="fas fa-shopping-basket text-emerald-500 mr-2"></i> {{ __('admin/orders/orders.purchase_summary') }}</h3>
            
            <div class="bg-slate-800 rounded-2xl border border-slate-300 overflow-hidden">
                <table class="w-full text-left text-slate-300">
                    <thead class="bg-slate-900 text-slate-500 text-xs uppercase font-black tracking-widest border-b border-slate-300">
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
                                    {{ $item->product ? $item->product->nombre_traducido : __('admin/orders/orders.unknown_product') }}
                                    
                                    @if($item->product && $item->product->trashed())
                                        <span class="text-[10px] bg-red-500/10 text-red-400 px-2 py-0.5 rounded border border-red-500/20 ml-2 uppercase tracking-widest">{{ __('admin/orders/orders.badge_obsolete') }}</span>
                                    @endif
                                    
                                    @if($item->opciones_elegidas)
                                        @php
                                            $opciones = json_decode($item->opciones_elegidas, true);
                                            $locale = app()->getLocale();
                                        @endphp
                                        @if(is_array($opciones) && count($opciones) > 0)
                                            <ul class="mt-1 text-xs text-slate-500 font-normal list-disc list-inside ml-4">
                                                @foreach($opciones as $opcion)
                                                    @php
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

{{-- MODAL DE CONFIRMACIÓN DE ENTREGA --}}
<div id="modal-complete" class="fixed inset-0 z-[100] hidden flex items-center justify-center bg-black/80 backdrop-blur-sm transition-opacity opacity-0 p-4">
    <div id="modal-complete-panel" class="bg-slate-900 border border-slate-300 p-6 sm:p-8 rounded-3xl shadow-2xl w-full max-w-sm transform scale-95 transition-all text-center">
        
        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full mb-5 bg-emerald-500/20 text-emerald-500 border border-emerald-500/50 shadow-[0_0_15px_rgba(16,185,129,0.3)]">
            <i class="fas fa-check-double text-3xl animate-bounce"></i>
        </div>
        
        <h3 class="text-2xl font-black text-white mb-2">{{ __('admin/orders/orders.confirm_delivery_title') }}</h3>
        <p class="text-sm text-slate-400 mb-8 leading-relaxed">{{ __('admin/orders/orders.confirm_delivery_desc') }}</p>
        
        <div class="flex gap-3">
            <button type="button" onclick="cerrarModalCompletar()" class="flex-1 bg-slate-800 hover:bg-slate-700 text-white font-bold py-3.5 rounded-xl transition-all border border-slate-600">
                {{ __('admin/orders/orders.btn_cancel_modal') }}
            </button>
            <button type="button" id="btn-confirm-complete" class="flex-1 bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-3.5 rounded-xl shadow-lg transition-all shadow-emerald-900/50 flex justify-center items-center gap-2">
                <i class="fas fa-check"></i> {{ __('admin/orders/orders.btn_confirm_delivery') }}
            </button>
        </div>
    </div>
</div>

{{-- SCRIPT DEL MODAL Y BLINDAJE --}}
<script>
    const modalComplete = document.getElementById('modal-complete');
    const panelComplete = document.getElementById('modal-complete-panel');
    const btnConfirmComplete = document.getElementById('btn-confirm-complete');
    const formComplete = document.getElementById('form-complete-order');
    let isCompleting = false;

    function abrirModalCompletar() {
        if (modalComplete && panelComplete) {
            modalComplete.classList.remove('hidden');
            setTimeout(() => {
                modalComplete.classList.remove('opacity-0');
                panelComplete.classList.remove('scale-95');
                panelComplete.classList.add('scale-100');
            }, 10);
        }
    }

    function cerrarModalCompletar() {
        if (modalComplete && panelComplete) {
            modalComplete.classList.add('opacity-0');
            panelComplete.classList.remove('scale-100');
            panelComplete.classList.add('scale-95');
            setTimeout(() => modalComplete.classList.add('hidden'), 300);
        }
    }

    // BLINDAJE ANTI-SPAM (F5)
    if (btnConfirmComplete && formComplete) {
        btnConfirmComplete.addEventListener('click', function() {
            if (isCompleting) return;
            isCompleting = true;

            // Bloquear botón visualmente
            btnConfirmComplete.disabled = true;
            btnConfirmComplete.style.pointerEvents = 'none';
            btnConfirmComplete.classList.add('opacity-75', 'cursor-not-allowed');
            btnConfirmComplete.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Procesando...';
            
            // Enviar formulario
            formComplete.submit();
        });
    }

    // Cerrar modal al hacer clic en el fondo oscuro
    if (modalComplete) {
        modalComplete.addEventListener('click', (e) => {
            if (e.target === modalComplete) cerrarModalCompletar();
        });
    }
</script>
@endsection