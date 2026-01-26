@extends('layouts.employee')

@section('titulo', 'Cocina en Vivo')

@section('contenido')
<div class="p-6 h-full flex flex-col relative"> 
    
    {{-- HEADER SUPERIOR --}}
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-white flex items-center gap-3">
            <i class="fas fa-fire text-orange-500 animate-pulse"></i> Comandas Activas
        </h1>
        
        <div class="flex items-center gap-4 bg-slate-800 p-2 rounded-lg border border-slate-700">
            <span class="text-slate-400 text-sm font-mono">
                <i class="fas fa-sync fa-spin text-orange-500 mr-2"></i>Refresco: 
                <span id="timer" class="text-white font-bold">30</span>s
            </span>
            <button onclick="window.location.reload()" class="bg-slate-700 hover:bg-slate-600 text-white p-2 rounded transition" title="Actualizar ya">
                <i class="fas fa-redo-alt"></i>
            </button>
        </div>
    </div>

    {{-- GRID DE PEDIDOS --}}
    @if($orders->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 overflow-y-auto pb-20 pr-2 custom-scrollbar">
            
            @foreach($orders as $order)
                @php
                    $statusConfig = match($order->status) {
                        'pendiente', 'pagado' => ['border' => 'border-red-500', 'badge' => 'bg-red-500', 'text' => 'NUEVA'],
                        'cocinando', 'preparando' => ['border' => 'border-yellow-500', 'badge' => 'bg-yellow-500 text-black', 'text' => 'COCINANDO'],
                        'en_camino', 'listo' => ['border' => 'border-green-500', 'badge' => 'bg-green-500', 'text' => 'LISTA/ENVÍO'],
                        default => ['border' => 'border-gray-500', 'badge' => 'bg-gray-500', 'text' => $order->status]
                    };
                @endphp

                <div id="order-card-{{ $order->id }}" class="bg-slate-800 border-t-4 {{ $statusConfig['border'] }} rounded-xl shadow-xl flex flex-col relative h-full transition-transform hover:scale-[1.01] group">
                    
                    {{-- CABECERA DE LA TARJETA --}}
                    <div class="p-4 border-b border-slate-700 flex justify-between items-start bg-slate-800/50">
                        <div>
                            <span class="text-4xl font-black text-slate-200 block">#{{ $order->id }}</span>
                            <span class="text-xs font-bold text-slate-400 mt-1 block">
                                {{ $order->created_at->format('H:i') }} <span class="font-normal opacity-70">({{ $order->created_at->diffForHumans(null, true, true) }})</span>
                            </span>
                        </div>

                        <div class="flex flex-col items-end gap-2">
                            <span class="{{ $statusConfig['badge'] }} text-white text-xs font-bold px-2 py-1 rounded uppercase tracking-wider shadow-lg">
                                {{ $statusConfig['text'] }}
                            </span>

                            {{-- BOTÓN CANCELAR (Solo visible si es Pendiente) --}}
                            @if($order->status == 'pendiente')
                                <button onclick="cancelarOrden({{ $order->id }})" 
                                        class="text-slate-500 hover:text-red-500 transition-colors text-sm flex items-center gap-1 group/cancel"
                                        title="Cancelar Orden">
                                    <span class="text-[10px] opacity-0 group-hover/cancel:opacity-100 transition-opacity">CANCELAR</span>
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            @endif
                        </div>
                    </div>

                    {{-- LISTA DE ITEMS --}}
                    <div class="p-4 flex-1 overflow-y-auto max-h-[300px] space-y-4 custom-scrollbar bg-slate-800">
                        @foreach($order->items as $item)
                            <div class="flex items-start gap-3 border-b border-slate-700/50 pb-2 last:border-0">
                                <span class="bg-slate-700 text-white font-bold rounded-md w-8 h-8 flex items-center justify-center text-lg shrink-0 border border-slate-600">
                                    {{ $item->cantidad }}
                                </span>
                                <div>
                                    <p class="text-lg font-bold text-slate-200 leading-tight">
                                        {{ $item->product ? $item->product->nombre : 'Producto Eliminado' }}
                                    </p>
                                    
                                    {{-- Opciones de personalización --}}
                                    @if($item->opciones)
                                        @php $opciones = is_string($item->opciones) ? json_decode($item->opciones, true) : $item->opciones; @endphp
                                        @if(!empty($opciones))
                                            <div class="mt-2 flex flex-wrap gap-1">
                                                @foreach($opciones as $opcion)
                                                    <span class="bg-red-500/10 text-red-400 border border-red-500/20 text-[10px] px-1.5 py-0.5 rounded font-bold uppercase">
                                                        {{ is_array($opcion) ? $opcion['valor'] : $opcion }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- PIE DE PAGINA (Mesa o Delivery) --}}
                    <div class="bg-black/20 p-3 text-xs text-slate-400 border-t border-slate-700 flex items-center gap-3">
                        
                        {{-- CASO 1: COMEDOR (Tiene número de mesa) --}}
                        @if(!empty($order->mesa))
                            <div class="bg-blue-600/20 text-blue-400 p-2 rounded-lg shrink-0">
                                <i class="fas fa-utensils text-lg"></i>
                            </div>
                            <div>
                                <p class="uppercase font-bold text-blue-400">Comer Aquí</p>
                                <p class="text-white text-lg font-black">MESA {{ $order->mesa }}</p>
                                @if($order->cliente_nombre)<p class="text-slate-500 truncate max-w-[120px]">{{ $order->cliente_nombre }}</p>@endif
                            </div>

                        {{-- CASO 2: PARA LLEVAR (Sin mesa, pero venta en local/POS) --}}
                        {{-- Identificamos esto si es 'para_llevar' y NO tiene dirección compleja o viene del POS --}}
                        @elseif($order->tipo_servicio == 'para_llevar')
                            <div class="bg-yellow-600/20 text-yellow-400 p-2 rounded-lg shrink-0">
                                <i class="fas fa-shopping-bag text-lg"></i>
                            </div>
                            <div>
                                <p class="uppercase font-bold text-yellow-400">Para Llevar</p>
                                <p class="text-white text-base font-bold truncate max-w-[150px]">
                                    {{ $order->cliente_nombre ?? 'Cliente Mostrador' }}
                                </p>
                                <p class="text-[10px] text-slate-500">Recoge en Barra</p>
                            </div>

                        {{-- CASO 3: A DOMICILIO (Venta web) --}}
                        @else
                            <div class="bg-orange-600/20 text-orange-400 p-2 rounded-lg shrink-0">
                                <i class="fas fa-motorcycle text-lg"></i>
                            </div>
                            <div class="overflow-hidden">
                                <p class="uppercase font-bold text-orange-400">Delivery</p>
                                <p class="text-slate-300 truncate font-medium max-w-[150px]" title="{{ $order->direccion }}">
                                    {{ Str::limit($order->direccion ?? 'Ver detalles...', 25) }}
                                </p>
                                <p class="text-[10px] text-slate-500">{{ $order->user->name ?? 'Web' }}</p>
                            </div>
                        @endif
                        
                    </div>

                    {{-- BOTONES DE ACCIÓN --}}
                    <div class="p-3 bg-slate-900 rounded-b-xl border-t border-slate-700">
                        @if($order->status == 'pendiente' || $order->status == 'pagado')
                            <button onclick="actualizarEstado({{ $order->id }}, 'cocinando')" 
                                    class="w-full bg-red-600 hover:bg-red-500 text-white font-bold py-3 rounded-lg transition flex justify-center items-center gap-2 group shadow-lg shadow-red-900/30">
                                <i class="fas fa-fire-alt group-hover:animate-bounce"></i> A COCINA
                            </button>
                        @elseif($order->status == 'cocinando' || $order->status == 'preparando')
                            <button onclick="actualizarEstado({{ $order->id }}, 'listo')" 
                                    class="w-full bg-yellow-600 hover:bg-yellow-500 text-white font-bold py-3 rounded-lg transition flex justify-center items-center gap-2 shadow-lg shadow-yellow-900/30">
                                <i class="fas fa-bell"></i> MARCAR LISTO
                            </button>
                        @elseif($order->status == 'en_camino' || $order->status == 'listo')
                            <button onclick="actualizarEstado({{ $order->id }}, 'entregado')" 
                                    class="w-full bg-green-600 hover:bg-green-500 text-white font-bold py-3 rounded-lg transition flex justify-center items-center gap-2 shadow-lg shadow-green-900/30">
                                <i class="fas fa-check-double"></i> FINALIZAR
                            </button>
                        @endif
                    </div>

                </div>
            @endforeach
        </div>
    @else
        {{-- ESTADO VACÍO --}}
        <div class="flex flex-col items-center justify-center h-full text-slate-500 pb-20">
            <div class="bg-slate-800 p-8 rounded-full mb-6 animate-pulse shadow-2xl border border-slate-700">
                <i class="fas fa-check text-5xl text-green-500"></i>
            </div>
            <h2 class="text-3xl font-bold text-white mb-2">¡Todo limpio, Chef!</h2>
            <p class="text-lg text-slate-400">Esperando que caigan las comandas...</p>
        </div>
    @endif

    {{-- CONTENEDOR DE NOTIFICACIONES (TOASTS) --}}
    <div id="toast-container" class="fixed bottom-5 right-5 z-50 flex flex-col gap-2 pointer-events-none"></div>

    {{-- MODAL DE CONFIRMACIÓN (Esencial para que funcione el botón cancelar) --}}
    <div id="confirmation-modal" class="fixed inset-0 z-[60] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm transition-opacity opacity-0" id="modal-backdrop"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-2xl bg-slate-800 border border-slate-700 text-left shadow-2xl shadow-black transition-all sm:my-8 sm:w-full sm:max-w-lg scale-95 opacity-0" id="modal-panel">
                    <div class="bg-slate-800 px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-900/30 sm:mx-0 sm:h-10 sm:w-10">
                                <i class="fas fa-exclamation-triangle text-red-500 text-lg"></i>
                            </div>
                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                                <h3 class="text-xl font-bold leading-6 text-white" id="modal-title">¿Cancelar Orden?</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-slate-400">
                                        Estás a punto de cancelar la <span class="font-bold text-white" id="modal-order-id">Orden #000</span>. 
                                        Esta acción <span class="text-red-400 font-bold">no se puede deshacer</span> y desaparecerá de la lista.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-slate-900/50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 gap-2">
                        <button type="button" id="btn-confirm-delete" class="inline-flex w-full justify-center rounded-lg bg-red-600 px-3 py-2 text-sm font-bold text-white shadow-sm hover:bg-red-500 sm:w-auto transition-colors flex items-center gap-2">
                            <i class="fas fa-trash-alt"></i> Sí, Cancelar
                        </button>
                        <button type="button" onclick="closeModal()" class="mt-3 inline-flex w-full justify-center rounded-lg bg-slate-700 px-3 py-2 text-sm font-bold text-gray-300 shadow-sm ring-1 ring-inset ring-slate-600 hover:bg-slate-600 sm:mt-0 sm:w-auto transition-colors">
                            No, Regresar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

@vite(['resources/js/employee/kitchen.js'])

@endsection