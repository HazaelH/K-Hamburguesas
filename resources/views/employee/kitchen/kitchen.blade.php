@extends('layouts.employee')

@section('titulo', __('employee/kitchen/kitchen.title'))

@section('contenido')
<div class="p-4 md:p-6 h-full flex flex-col relative overflow-hidden"> 
    
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 shrink-0">
        <h1 class="text-2xl md:text-3xl font-bold text-white flex items-center gap-3 tracking-tight">
            <i class="fas fa-fire text-orange-500"></i> {{ __('employee/kitchen/kitchen.active_orders') }}
        </h1>
        
        <div class="flex items-center gap-2 bg-slate-800/50 px-3 py-1.5 rounded-full border border-slate-300/50">
            <div class="relative flex h-3 w-3">
              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
              <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
            </div>
            {{-- Accesibilidad: text-slate-400 subido a text-slate-300 para mejor contraste --}}
            <span class="text-slate-300 text-xs font-bold uppercase tracking-wider ml-1">{{ __('employee/kitchen/kitchen.live_indicator') }}</span>
        </div>
    </div>

    <div id="orders-grid-container" class="flex-1 min-h-0">
        @if($orders->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-4 lg:gap-6 overflow-y-auto h-full pb-6 pr-2 custom-scrollbar">
                
                @foreach($orders as $order)
                    @php
                        $datosJSON = is_string($order->datos_entrega) ? json_decode($order->datos_entrega, true) : ($order->datos_entrega ?? []);
                        $esperandoAdmin = isset($datosJSON['solicita_cancelacion']) && $datosJSON['solicita_cancelacion'] === true;

                        $statusConfig = match($order->status) {
                            'pendiente', 'pagado' => ['border' => 'border-red-500', 'badge' => 'bg-red-500 text-white', 'text' => __('employee/kitchen/kitchen.status_new')],
                            'cocinando', 'preparando' => ['border' => 'border-yellow-500', 'badge' => 'bg-yellow-500 text-black', 'text' => __('employee/kitchen/kitchen.status_cooking')],
                            'en_camino', 'listo' => ['border' => 'border-green-500', 'badge' => 'bg-green-500 text-white', 'text' => __('employee/kitchen/kitchen.status_ready')],
                            // Accesibilidad: Se declaró text-white en el default
                            default => ['border' => 'border-slate-500', 'badge' => 'bg-slate-600 text-white', 'text' => strtoupper($order->status_traducido ?? $order->status)]
                        };
                    @endphp

                    <div id="order-card-{{ $order->id }}" class="bg-slate-800 border-t-4 {{ $statusConfig['border'] }} rounded-2xl shadow-xl flex flex-col relative h-[420px] transition-transform hover:-translate-y-1 group before:absolute before:-inset-3 before:content-[''] before:z-[-1]">
                        
                        <div class="p-4 border-b border-slate-300 flex justify-between items-start bg-slate-800/50 rounded-t-xl shrink-0">
                            <div>
                                <span class="text-3xl font-black text-slate-200 block leading-none">#{{ $order->id }}</span>
                                {{-- Accesibilidad: Opacidad removida, colores ajustados --}}
                                <span class="text-[11px] font-bold text-slate-300 mt-1 block">
                                    {{ $order->created_at->format(app()->getLocale() == 'en' ? 'h:i A' : 'H:i') }} 
                                    <span class="font-normal text-slate-400">({{ $order->created_at->diffForHumans(null, true, true) }})</span>
                                </span>
                            </div>

                            <div class="flex flex-col items-end gap-2">
                                <span class="{{ $statusConfig['badge'] }} text-[10px] font-bold px-2 py-1 rounded shadow-lg">
                                    {{ $statusConfig['text'] }}
                                </span>

                                @if($order->status == 'pendiente' && !$esperandoAdmin)
                                    {{-- Accesibilidad: aria-label agregado, px y py aumentados para mejorar el 'Touch Target' --}}
                                    <button aria-label="Cancelar orden {{ $order->id }}" onclick="cancelarOrden({{ $order->id }})" class="text-slate-400 hover:text-red-400 transition-colors text-sm flex items-center gap-1 group/cancel bg-slate-900 px-3 py-2 rounded-lg border border-slate-500 hover:border-red-500/50">
                                        <i class="fas fa-trash-alt pointer-events-none"></i>
                                    </button>
                                @endif
                            </div>
                        </div>

                        <div class="p-4 flex-1 overflow-y-auto space-y-3 custom-scrollbar bg-slate-800/80">
                            @foreach($order->items as $item)
                                <div class="flex items-start gap-3 border-b border-slate-300/50 pb-3 last:border-0 last:pb-0">
                                    <span class="bg-slate-900 text-white font-black rounded-lg w-8 h-8 flex items-center justify-center text-sm shrink-0 border border-slate-300 shadow-inner">
                                        {{ $item->cantidad }}
                                    </span>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-base font-bold text-slate-200 leading-tight">
                                            {{ $item->product ? $item->product->nombre_traducido : '---' }}
                                        </p>
                                        @if($item->opciones)
                                            @php $opciones = is_string($item->opciones) ? json_decode($item->opciones, true) : $item->opciones; @endphp
                                            @if(!empty($opciones))
                                                <div class="mt-1.5 flex flex-wrap gap-1">
                                                    @foreach($opciones as $opcion)
                                                        @php
                                                            // EL BLINDAJE: Ahora busca 'nombre' (POS/Ticket) o 'valor' (Web antigua)
                                                            $valorOriginal = is_array($opcion) ? ($opcion['nombre'] ?? $opcion['valor'] ?? '') : $opcion;
                                                            $textoOpcion = $valorOriginal;

                                                            if ($item->product && !empty($item->product->opciones_personalizacion)) {
                                                                $catOpciones = is_string($item->product->opciones_personalizacion) ? json_decode($item->product->opciones_personalizacion, true) : $item->product->opciones_personalizacion;
                                                                $locale = app()->getLocale();
                                                                if (is_array($catOpciones)) {
                                                                    foreach ($catOpciones as $catOp) {
                                                                        if (isset($catOp['nombre']) && $catOp['nombre'] === $valorOriginal) {
                                                                            if ($locale === 'en' && !empty($catOp['nombre_en'])) {
                                                                                $textoOpcion = $catOp['nombre_en'];
                                                                            } elseif ($locale === 'pt' && !empty($catOp['nombre_pt'])) {
                                                                                $textoOpcion = $catOp['nombre_pt'];
                                                                            }
                                                                            break;
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        @endphp
                                                        
                                                        {{-- Solo imprimimos si realmente hay un texto que mostrar --}}
                                                        @if(!empty($textoOpcion))
                                                            <span class="bg-red-500/10 text-red-400 border border-red-500/20 text-[10px] px-1.5 py-0.5 rounded font-bold uppercase truncate max-w-full">
                                                                {{ $textoOpcion }}
                                                            </span>
                                                        @endif
                                                        
                                                    @endforeach
                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="shrink-0 mt-auto rounded-b-xl overflow-hidden">
                            <div class="bg-slate-900 p-3 border-t border-slate-300 flex items-center gap-3 h-16">
                                @if(!empty($order->mesa))
                                    <div class="bg-blue-600/20 text-blue-400 w-10 h-10 flex items-center justify-center rounded-lg shrink-0"><i class="fas fa-utensils text-lg"></i></div>
                                    <div class="min-w-0">
                                        <p class="uppercase font-bold text-blue-400 text-[10px] leading-none mb-0.5">{{ __('employee/kitchen/kitchen.dine_in') }}</p>
                                        <p class="text-white text-base font-black truncate">{{ __('employee/kitchen/kitchen.table', ['number' => $order->mesa]) }}</p>
                                    </div>
                                @elseif($order->tipo_servicio == 'para_llevar')
                                    <div class="bg-yellow-600/20 text-yellow-400 w-10 h-10 flex items-center justify-center rounded-lg shrink-0"><i class="fas fa-shopping-bag text-lg"></i></div>
                                    <div class="min-w-0">
                                        <p class="uppercase font-bold text-yellow-400 text-[10px] leading-none mb-0.5">{{ __('employee/kitchen/kitchen.takeout') }}</p>
                                        <p class="text-white text-base font-black truncate">{{ $order->cliente_nombre ?? '---' }}</p>
                                    </div>
                                @else
                                    <div class="bg-orange-700/20 text-orange-400 w-10 h-10 flex items-center justify-center rounded-lg shrink-0"><i class="fas fa-motorcycle text-lg"></i></div>
                                    <div class="min-w-0">
                                        <p class="uppercase font-bold text-orange-400 text-[10px] leading-none mb-0.5">{{ __('employee/kitchen/kitchen.delivery') }}</p>
                                        <p class="text-slate-300 text-sm truncate font-medium">{{ $order->direccion ?? __('employee/kitchen/kitchen.see_details') }}</p>
                                    </div>
                                @endif
                            </div>

                            <div class="p-3 bg-slate-900 border-t border-slate-800">
                                @if($esperandoAdmin)
                                    <button disabled class="w-full bg-slate-700 text-slate-300 font-black py-3 rounded-xl flex justify-center items-center gap-2 cursor-not-allowed opacity-80 border border-slate-300">
                                        <i class="fas fa-lock text-red-400"></i> {{ __('employee/kitchen/kitchen.status_waiting_admin') }}
                                    </button>
                                @elseif($order->status == 'pendiente' || $order->status == 'pagado')
                                    <button onclick="actualizarEstado({{ $order->id }}, 'cocinando')" class="w-full bg-red-600 hover:bg-red-500 text-white font-black py-3 rounded-xl transition-all shadow-[0_0_15px_rgba(220,38,38,0.2)] active:scale-95 flex justify-center items-center gap-2 tracking-wide">
                                        <i class="fas fa-fire-alt"></i> {{ __('employee/kitchen/kitchen.btn_to_kitchen') }}
                                    </button>
                                @elseif($order->status == 'cocinando' || $order->status == 'preparando')
                                    <button onclick="actualizarEstado({{ $order->id }}, 'listo')" class="w-full bg-yellow-500 hover:bg-yellow-400 text-slate-900 font-black py-3 rounded-xl transition-all shadow-[0_0_15px_rgba(234,179,8,0.2)] active:scale-95 flex justify-center items-center gap-2 tracking-wide">
                                        <i class="fas fa-bell"></i> {{ __('employee/kitchen/kitchen.btn_mark_ready') }}
                                    </button>
                                @elseif($order->status == 'en_camino' || $order->status == 'listo')
                                    <button onclick="actualizarEstado({{ $order->id }}, 'entregado')" class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-black py-3 rounded-xl transition-all shadow-[0_0_15px_rgba(5,150,105,0.2)] active:scale-95 flex justify-center items-center gap-2 tracking-wide">
                                        <i class="fas fa-check-double"></i> {{ __('employee/kitchen/kitchen.btn_finish') }}
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            {{-- Accesibilidad: Contraste ajustado a text-slate-400 --}}
            <div class="flex flex-col items-center justify-center h-full text-slate-400">
                <div class="bg-slate-800 p-8 rounded-full mb-6 shadow-2xl border border-slate-300">
                    <i class="fas fa-check-circle text-5xl text-green-500/50"></i>
                </div>
                <h2 class="text-3xl font-bold text-white mb-2">{{ __('employee/kitchen/kitchen.all_clear') }}</h2>
                <p class="text-lg text-slate-400">{{ __('employee/kitchen/kitchen.waiting_orders') }}</p>
            </div>
        @endif
    </div>

    <div id="toast-container" class="fixed top-20 right-4 lg:right-8 z-50 flex flex-col gap-2 pointer-events-none w-72"></div>

    <div id="confirmation-modal" class="fixed inset-0 z-[60] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900/80 backdrop-blur-sm transition-opacity opacity-0" id="modal-backdrop"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-2xl bg-slate-900 border border-slate-300 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md scale-95 opacity-0" id="modal-panel">
                    <div class="p-6">
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-red-500/20 border border-red-500/50 mb-4">
                            <i class="fas fa-exclamation-triangle text-red-500 text-2xl"></i>
                        </div>
                        <h3 class="text-2xl font-black text-center text-white mb-2">{{ __('employee/kitchen/kitchen.modal_cancel_title') }}</h3>
                        {{-- Accesibilidad: Contraste ajustado a text-slate-300 --}}
                        <p class="text-sm text-center text-slate-300 mb-6" id="modal-desc-container"></p>
                        
                        <div class="flex gap-3">
                            <button type="button" onclick="closeModal()" class="flex-1 bg-slate-800 hover:bg-slate-700 text-white font-bold py-3 rounded-xl transition-all">{{ __('employee/kitchen/kitchen.btn_go_back') }}</button>
                            <button type="button" id="btn-confirm-delete" class="flex-1 bg-red-600 hover:bg-red-500 text-white font-bold py-3 rounded-xl shadow-lg transition-all">{{ __('employee/kitchen/kitchen.btn_confirm_cancel') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    window.KITCHEN_LANG = {
        requesting: `{{ __('employee/kitchen/kitchen.js_requesting') }}`,
        request_sent: `{{ __('employee/kitchen/kitchen.js_request_sent') }}`,
        conn_error: `{{ __('employee/kitchen/kitchen.js_conn_error') }}`,
        status_updated: `{{ __('employee/kitchen/kitchen.js_status_updated') }}`,
        waiting_admin: `{{ __('employee/kitchen/kitchen.status_waiting_admin') }}`,
        cancel_desc: `{!! __('employee/kitchen/kitchen.modal_cancel_desc') !!}`
    };
</script>

@vite(['resources/js/employee/kitchen.js'])
@endsection