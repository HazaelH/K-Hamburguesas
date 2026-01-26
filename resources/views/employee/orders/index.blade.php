@extends('layouts.employee')

@section('titulo', 'Control de Caja')

@section('contenido')

{{-- Cargamos los recursos específicos de esta vista --}}
@vite(['resources/css/caja.css', 'resources/js/employee/caja.js'])

<div class="h-full flex flex-col bg-slate-950 p-6 relative">

    {{-- HEADER Y ESCÁNER --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-6">
        
        {{-- Título --}}
        <div>
            <h1 class="text-3xl font-bold text-white flex items-center gap-3">
                <i class="fas fa-cash-register text-emerald-500"></i> Monitor de Caja
            </h1>
            <p class="text-slate-400 text-sm">Gestiona cobros y escanea entregas.</p>
        </div>

        {{-- ESCÁNER QR (Formulario central) --}}
        <div class="flex-1 w-full md:max-w-md mx-auto">
            <form action="{{ route('employee.orders.scan') }}" method="POST" class="relative group">
                @csrf
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-qrcode text-slate-500 group-focus-within:text-orange-500 transition-colors"></i>
                </div>
                <input type="text" name="codigo" placeholder="Escanear Código QR aquí..." autofocus
                       class="block w-full pl-10 pr-12 py-3 border border-slate-700 rounded-xl leading-5 bg-slate-800 text-slate-300 placeholder-slate-500 focus:outline-none focus:bg-slate-900 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 sm:text-sm transition-all shadow-lg"
                       onblur="this.focus()" {{-- Mantiene el foco para pistolas lectoras --}}
                       autocomplete="off">
                <button type="submit" class="absolute inset-y-0 right-0 px-4 text-slate-400 hover:text-white transition-colors">
                    <i class="fas fa-arrow-right"></i>
                </button>
            </form>
        </div>
        
        {{-- TARJETA DE TOTAL VENTAS (Lógica Corregida) --}}
        <div class="bg-slate-800 px-6 py-3 rounded-xl border border-slate-700 shadow-lg flex flex-col items-end min-w-[200px]">
            <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider">Ventas del Día</span>
            
            @php
                $totalVentas = $orders->filter(function($order) {
                    $datos = is_string($order->datos_entrega) ? json_decode($order->datos_entrega, true) : ($order->datos_entrega ?? []);
                    // Sumamos si el status es pagado/entregado O si la bandera 'pagado' existe
                    return ($order->status == 'pagado' || $order->status == 'entregado') || !empty($datos['pagado']);
                })->sum('total');
            @endphp

            <p class="text-3xl font-black text-emerald-400 tracking-tight">
                ${{ number_format($totalVentas, 2) }}
            </p>
        </div>
    </div>

    {{-- TABLA DE ÓRDENES --}}
    <div class="bg-slate-900 rounded-2xl border border-white/5 overflow-hidden flex-1 shadow-2xl flex flex-col">
        @if($orders->isEmpty())
            <div class="flex-1 flex flex-col items-center justify-center text-slate-500">
                <i class="fas fa-inbox text-6xl mb-4 opacity-20"></i>
                <p>No hay órdenes activas hoy</p>
            </div>
        @else
            <div class="overflow-x-auto custom-scrollbar flex-1">
                <table class="w-full text-left text-gray-400">
                    <thead class="bg-slate-800 text-xs uppercase font-bold text-slate-300 sticky top-0 z-10 shadow-md">
                        <tr>
                            <th class="px-6 py-4">Orden #</th>
                            <th class="px-6 py-4">Cliente / Origen</th>
                            <th class="px-6 py-4">Total</th>
                            <th class="px-6 py-4">Método</th>
                            <th class="px-6 py-4">Estado</th>
                            <th class="px-6 py-4 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/50">
                        @foreach($orders as $order)
                            <tr class="hover:bg-slate-800/40 transition group">
                                
                                {{-- LÓGICA MAESTRA PARA DETECTAR PAGO --}}
                                @php
                                    $datos = is_string($order->datos_entrega) ? json_decode($order->datos_entrega, true) : ($order->datos_entrega ?? []);
                                    $yaPago = ($order->status == 'pagado' || $order->status == 'entregado') || !empty($datos['pagado']);
                                @endphp

                                {{-- ID --}}
                                <td class="px-6 py-4 font-mono text-white group-hover:text-orange-400 transition-colors">
                                    #{{ $order->id }}
                                    @if($order->codigo_entrega)
                                        <span class="block text-[10px] text-slate-500 mt-1">QR: {{ $order->codigo_entrega }}</span>
                                    @endif
                                </td>
                                
                                {{-- CLIENTE --}}
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        @if($order->mesa || $order->tipo_servicio == 'para_llevar' || $order->tipo_servicio == 'comedor')
                                            @if($order->mesa)
                                                <span class="text-blue-400 font-bold flex items-center gap-2 text-sm">
                                                    <i class="fas fa-chair"></i> Mesa {{ $order->mesa }}
                                                </span>
                                            @else
                                                <span class="text-purple-400 font-bold flex items-center gap-2 text-sm">
                                                    <i class="fas fa-shopping-bag"></i> Para Llevar
                                                </span>
                                            @endif
                                            <span class="text-xs text-slate-500 font-medium">
                                                {{ $order->cliente_nombre ?? 'Cliente Casual' }}
                                            </span>
                                        @else
                                            <span class="text-orange-400 font-bold flex items-center gap-2 text-sm">
                                                <i class="fas fa-motorcycle"></i> Envío Web
                                            </span>
                                            <span class="text-xs text-slate-500 font-medium">
                                                {{ $order->user->name ?? 'Usuario Web' }}
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                {{-- TOTAL --}}
                                <td class="px-6 py-4">
                                    <span class="text-white font-bold text-lg tracking-tight">
                                        ${{ number_format($order->total, 2) }}
                                    </span>
                                </td>

                                {{-- MÉTODO PAGO --}}
                                <td class="px-6 py-4">
                                    @if($order->metodo_pago == 'efectivo')
                                        <div class="flex items-center gap-2 text-green-400 bg-green-500/10 px-2.5 py-1 rounded-lg text-xs font-bold w-fit border border-green-500/20">
                                            <i class="fas fa-money-bill-wave"></i> Efectivo
                                        </div>
                                    @else
                                        <div class="flex items-center gap-2 text-blue-400 bg-blue-500/10 px-2.5 py-1 rounded-lg text-xs font-bold w-fit border border-blue-500/20">
                                            <i class="fas fa-credit-card"></i> Tarjeta
                                        </div>
                                    @endif
                                </td>

                                {{-- ESTADO (Visualización Inteligente) --}}
                                <td class="px-6 py-4">
                                    @if($order->status == 'cancelado')
                                        <span class="inline-flex items-center gap-1.5 bg-red-500/10 text-red-400 px-3 py-1 rounded-full text-xs font-bold border border-red-500/20">
                                            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> CANCELADO
                                        </span>
                                    @elseif($order->status == 'entregado')
                                        <span class="inline-flex items-center gap-1.5 bg-emerald-500/10 text-emerald-400 px-3 py-1 rounded-full text-xs font-bold border border-emerald-500/20">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> ENTREGADO
                                        </span>
                                    @elseif($yaPago)
                                        {{-- SI YA PAGÓ pero la cocina sigue trabajando --}}
                                        <div class="flex flex-col gap-1 items-start">
                                            <span class="inline-flex items-center gap-1.5 bg-emerald-500/10 text-emerald-400 px-2 py-0.5 rounded text-[10px] font-bold border border-emerald-500/20 w-fit">
                                                <i class="fas fa-check"></i> PAGADO
                                            </span>
                                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wide pl-1 flex items-center gap-1">
                                                <i class="fas fa-fire-alt text-orange-500"></i> {{ str_replace('_', ' ', $order->status) }}
                                            </span>
                                        </div>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 bg-yellow-500/10 text-yellow-400 px-3 py-1 rounded-full text-xs font-bold border border-yellow-500/20 animate-pulse">
                                            <span class="w-1.5 h-1.5 rounded-full bg-yellow-500"></span> POR COBRAR
                                        </span>
                                    @endif
                                </td>

                                {{-- ACCIONES (Blindado) --}}
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end items-center gap-2">
                                        
                                        @if($order->status == 'entregado')
                                            <span class="text-slate-600 text-xs font-bold uppercase flex items-center gap-1">
                                                <i class="fas fa-lock"></i> Cerrado
                                            </span>
                                        
                                        @elseif($order->status == 'cancelado')
                                            <span class="text-red-900/50 text-xs font-bold uppercase">Anulado</span>
                                        
                                        @elseif($yaPago)
                                            {{-- YA PAGÓ: Solo mostramos indicador visual --}}
                                            <span class="text-emerald-500/50 text-xs font-bold uppercase flex items-center gap-1 border border-emerald-500/10 px-2 py-1 rounded-lg select-none">
                                                <i class="fas fa-check-circle"></i> Cobrado
                                            </span>
                                            
                                        @else
                                            {{-- FALTA PAGO: Botones activos --}}
                                            <button type="button" 
                                                    onclick="openConfirmModal('pay', {{ $order->id }}, {{ $order->total }})"
                                                    class="bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-2 px-4 rounded-lg shadow-lg shadow-emerald-900/20 transition-all hover:scale-105 flex items-center gap-2">
                                                <i class="fas fa-check"></i> <span class="hidden md:inline">Cobrar</span>
                                            </button>

                                            <button type="button" 
                                                    onclick="openConfirmModal('cancel', {{ $order->id }})"
                                                    class="bg-slate-700 hover:bg-red-600 text-slate-300 hover:text-white font-bold py-2 px-3 rounded-lg border border-slate-600 hover:border-red-500 transition-all hover:scale-105"
                                                    title="Cancelar Orden">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- TOASTS CONTAINER --}}
    <div id="toast-container" class="fixed bottom-5 right-5 z-50 flex flex-col gap-2 pointer-events-none"></div>

    {{-- MODAL PERSONALIZADO --}}
    <div id="custom-modal" class="hidden relative z-50 modal-hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div id="modal-backdrop" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm transition-opacity"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div id="modal-panel" class="relative transform overflow-hidden rounded-2xl bg-slate-900 border border-slate-700 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                    <div class="bg-slate-900 px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div id="modal-icon-box" class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-slate-800 sm:mx-0 sm:h-10 sm:w-10">
                                <i id="modal-icon" class="fas fa-question text-slate-400"></i>
                            </div>
                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                                <h3 class="text-xl font-bold leading-6 text-white" id="modal-title">Título Modal</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-slate-400" id="modal-desc">Descripción de la acción.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-slate-800/50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 gap-3">
                        <form id="form-modal-action" method="POST" class="w-full sm:w-auto">
                            @csrf
                            <button type="submit" id="btn-confirm-action" class="inline-flex w-full justify-center rounded-lg bg-emerald-600 px-3 py-2 text-sm font-bold text-white shadow-sm hover:bg-emerald-500 sm:w-auto transition-colors">
                                Confirmar
                            </button>
                        </form>
                        <button type="button" onclick="closeModal()" class="mt-3 inline-flex w-full justify-center rounded-lg bg-slate-800 px-3 py-2 text-sm font-bold text-slate-300 shadow-sm ring-1 ring-inset ring-slate-700 hover:bg-slate-700 sm:mt-0 sm:w-auto transition-colors">
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- SCRIPT PUENTE --}}
<script>
    document.addEventListener('DOMContentLoaded', () => {
        @if(session('success'))
            if(window.showToast) window.showToast("{{ session('success') }}", 'success');
        @endif
        @if(session('error'))
            if(window.showToast) window.showToast("{{ session('error') }}", 'error');
        @endif
    });
</script>
@endsection