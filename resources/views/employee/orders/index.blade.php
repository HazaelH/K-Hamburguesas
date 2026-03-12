@extends('layouts.employee')

@section('titulo', __('employee/orders/index.title'))

@section('contenido')

@vite(['resources/css/caja.css', 'resources/js/employee/caja.js'])

<div class="h-full flex flex-col bg-slate-950 p-6 relative">

    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-6">
        
        <div>
            <h1 class="text-3xl font-bold text-white flex items-center gap-3">
                <i class="fas fa-cash-register text-emerald-500"></i> {{ __('employee/orders/index.heading') }}
            </h1>
            <p class="text-slate-400 text-sm">{{ __('employee/orders/index.subtitle') }}</p>
        </div>

        <div class="flex-1 w-full md:max-w-md mx-auto">
            <form action="{{ route('employee.orders.scan') }}" method="POST" class="relative group">
                @csrf
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-qrcode text-slate-500 group-focus-within:text-orange-500 transition-colors"></i>
                </div>
                <input type="text" name="codigo" placeholder="{{ __('employee/orders/index.scan_placeholder') }}" autofocus
                       class="block w-full pl-10 pr-12 py-3 border border-slate-300 rounded-xl leading-5 bg-slate-800 text-slate-300 placeholder-slate-500 focus:outline-none focus:bg-slate-900 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 sm:text-sm transition-all shadow-lg"
                       onblur="this.focus()" 
                       autocomplete="off">
                <button type="submit" class="absolute inset-y-0 right-0 px-4 text-slate-400 hover:text-white transition-colors">
                    <i class="fas fa-arrow-right"></i>
                </button>
            </form>
        </div>
        
        <div class="bg-slate-800 px-6 py-3 rounded-xl border border-slate-300 shadow-lg flex flex-col items-end min-w-[200px]">
            <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider">{{ __('employee/orders/index.sales_today') }}</span>
            
            @php
                $totalVentas = $orders->filter(function($order) {
                    $datos = is_string($order->datos_entrega) ? json_decode($order->datos_entrega, true) : ($order->datos_entrega ?? []);
                    return ($order->status == 'pagado' || $order->status == 'entregado') || !empty($datos['pagado']);
                })->sum('total');
            @endphp

            <p class="text-3xl font-black text-emerald-400 tracking-tight">
                {{ formatCurrency($totalVentas) }}
            </p>
        </div>
    </div>

    <div class="bg-slate-900 rounded-2xl border border-white/5 overflow-hidden flex-1 shadow-2xl flex flex-col">
        @if($orders->isEmpty())
            <div class="flex-1 flex flex-col items-center justify-center text-slate-500">
                <i class="fas fa-inbox text-6xl mb-4 opacity-20"></i>
                <p>{{ __('employee/orders/index.no_orders') }}</p>
            </div>
        @else
            <div class="overflow-x-auto custom-scrollbar flex-1">
                <table class="w-full text-left text-gray-400">
                    <thead class="bg-slate-800 text-xs uppercase font-bold text-slate-300 sticky top-0 z-10 shadow-md">
                        <tr>
                            <th class="px-6 py-4">{{ __('employee/orders/index.col_order') }}</th>
                            <th class="px-6 py-4">{{ __('employee/orders/index.col_client') }}</th>
                            <th class="px-6 py-4">{{ __('employee/orders/index.col_total') }}</th>
                            <th class="px-6 py-4">{{ __('employee/orders/index.col_method') }}</th>
                            <th class="px-6 py-4">{{ __('employee/orders/index.col_status') }}</th>
                            <th class="px-6 py-4 text-right">{{ __('employee/orders/index.col_actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/50">
                        @foreach($orders as $order)
                            <tr class="hover:bg-slate-800/40 transition group">
                                
                                @php
                                    $datos = is_string($order->datos_entrega) ? json_decode($order->datos_entrega, true) : ($order->datos_entrega ?? []);
                                    $yaPago = ($order->status == 'pagado' || $order->status == 'entregado') || !empty($datos['pagado']);
                                @endphp

                                <td class="px-6 py-4 font-mono text-white group-hover:text-orange-400 transition-colors">
                                    #{{ $order->id }}
                                    @if($order->codigo_entrega)
                                        <span class="block text-[10px] text-slate-500 mt-1">QR: {{ $order->codigo_entrega }}</span>
                                    @endif
                                </td>
                                
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        @if($order->mesa || $order->tipo_servicio == 'para_llevar' || $order->tipo_servicio == 'comedor')
                                            @if($order->mesa)
                                                <span class="text-blue-400 font-bold flex items-center gap-2 text-sm">
                                                    <i class="fas fa-chair"></i> {{ __('employee/orders/index.table', ['number' => $order->mesa]) }}
                                                </span>
                                            @else
                                                <span class="text-purple-400 font-bold flex items-center gap-2 text-sm">
                                                    <i class="fas fa-shopping-bag"></i> {{ __('employee/orders/index.takeout') }}
                                                </span>
                                            @endif
                                            <span class="text-xs text-slate-500 font-medium">
                                                {{ $order->cliente_nombre ?? __('employee/orders/index.casual_client') }}
                                            </span>
                                        @else
                                            <span class="text-orange-400 font-bold flex items-center gap-2 text-sm">
                                                <i class="fas fa-motorcycle"></i> {{ __('employee/orders/index.web_delivery') }}
                                            </span>
                                            <span class="text-xs text-slate-500 font-medium">
                                                {{ $order->user->name ?? __('employee/orders/index.web_user') }}
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <span class="text-white font-bold text-lg tracking-tight">
                                        {{ formatCurrency($order->total) }}
                                    </span>
                                </td>

                                <td class="px-6 py-4">
                                    @if($order->metodo_pago == 'efectivo')
                                        <div class="flex items-center gap-2 text-green-400 bg-green-500/10 px-2.5 py-1 rounded-lg text-xs font-bold w-fit border border-green-500/20">
                                            <i class="fas fa-money-bill-wave"></i> {{ __('employee/orders/index.cash') }}
                                        </div>
                                    @else
                                        <div class="flex items-center gap-2 text-blue-400 bg-blue-500/10 px-2.5 py-1 rounded-lg text-xs font-bold w-fit border border-blue-500/20">
                                            <i class="fas fa-credit-card"></i> {{ __('employee/orders/index.card') }}
                                        </div>
                                    @endif
                                </td>

                                <td class="px-6 py-4">
                                    @if($order->status == 'cancelado')
                                        <span class="inline-flex items-center gap-1.5 bg-red-500/10 text-red-400 px-3 py-1 rounded-full text-xs font-bold border border-red-500/20">
                                            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> {{ $order->status_traducido }}
                                        </span>
                                    @elseif($order->status == 'entregado')
                                        <span class="inline-flex items-center gap-1.5 bg-emerald-500/10 text-emerald-400 px-3 py-1 rounded-full text-xs font-bold border border-emerald-500/20">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> {{ $order->status_traducido }}
                                        </span>
                                    @elseif($yaPago)
                                        <div class="flex flex-col gap-1 items-start">
                                            <span class="inline-flex items-center gap-1.5 bg-emerald-500/10 text-emerald-400 px-2 py-0.5 rounded text-[10px] font-bold border border-emerald-500/20 w-fit">
                                                <i class="fas fa-check"></i> {{ __('employee/orders/index.status_paid') }}
                                            </span>
                                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wide pl-1 flex items-center gap-1">
                                                <i class="fas fa-fire-alt text-orange-500"></i> {{ $order->status_traducido }}
                                            </span>
                                        </div>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 bg-yellow-500/10 text-yellow-400 px-3 py-1 rounded-full text-xs font-bold border border-yellow-500/20 animate-pulse">
                                            <span class="w-1.5 h-1.5 rounded-full bg-yellow-500"></span> {{ $order->status_traducido }}
                                        </span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end items-center gap-2">
                                        @if($order->status == 'entregado')
                                            <span class="text-slate-600 text-xs font-bold uppercase flex items-center gap-1">
                                                <i class="fas fa-lock"></i> {{ __('employee/orders/index.action_closed') }}
                                            </span>
                                        @elseif($order->status == 'cancelado')
                                            <span class="text-red-900/50 text-xs font-bold uppercase">{{ __('employee/orders/index.action_void') }}</span>
                                        @elseif($yaPago)
                                            <span class="text-emerald-500/50 text-xs font-bold uppercase flex items-center gap-1 border border-emerald-500/10 px-2 py-1 rounded-lg select-none">
                                                <i class="fas fa-check-circle"></i> {{ __('employee/orders/index.action_collected') }}
                                            </span>
                                        @else
                                            <button type="button" 
                                                    onclick="openConfirmModal('pay', {{ $order->id }}, {{ $order->total }})"
                                                    class="bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-2 px-4 rounded-lg shadow-lg shadow-emerald-900/20 transition-all hover:scale-105 flex items-center gap-2">
                                                <i class="fas fa-check"></i> <span class="hidden md:inline">{{ __('employee/orders/index.btn_charge') }}</span>
                                            </button>

                                            <button type="button" 
                                                    onclick="openConfirmModal('cancel', {{ $order->id }})"
                                                    class="bg-slate-700 hover:bg-red-600 text-slate-300 hover:text-white font-bold py-2 px-3 rounded-lg border border-slate-300 hover:border-red-500 transition-all hover:scale-105"
                                                    title="{{ __('employee/orders/index.btn_cancel') }}">
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

    <div id="toast-container" class="fixed bottom-5 right-5 z-50 flex flex-col gap-2 pointer-events-none"></div>

    <div id="custom-modal" class="hidden relative z-50 modal-hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div id="modal-backdrop" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm transition-opacity"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div id="modal-panel" class="relative transform overflow-hidden rounded-2xl bg-slate-900 border border-slate-300 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                    <div class="bg-slate-900 px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div id="modal-icon-box" class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-slate-800 sm:mx-0 sm:h-10 sm:w-10">
                                <i id="modal-icon" class="fas fa-question text-slate-400"></i>
                            </div>
                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                                <h3 class="text-xl font-bold leading-6 text-white" id="modal-title"></h3>
                                <div class="mt-2">
                                    <p class="text-sm text-slate-400" id="modal-desc"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-slate-800/50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 gap-3">
                        <form id="form-modal-action" method="POST" class="w-full sm:w-auto">
                            @csrf
                            <button type="submit" id="btn-confirm-action" class="inline-flex w-full justify-center rounded-lg bg-emerald-600 px-3 py-2 text-sm font-bold text-white shadow-sm hover:bg-emerald-500 sm:w-auto transition-colors">
                                </button>
                        </form>
                        <button type="button" onclick="closeModal()" class="mt-3 inline-flex w-full justify-center rounded-lg bg-slate-800 px-3 py-2 text-sm font-bold text-slate-300 shadow-sm ring-1 ring-inset ring-slate-700 hover:bg-slate-700 sm:mt-0 sm:w-auto transition-colors">
                            {{ __('employee/orders/index.modal_cancel') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    window.CAJA_LANG = {
        charge_title: `{{ __('employee/orders/index.js_charge_title') }}`,
        charge_desc: `{!! __('employee/orders/index.js_charge_desc') !!}`,
        charge_btn: `{{ __('employee/orders/index.js_charge_btn') }}`,
        cancel_title: `{{ __('employee/orders/index.js_cancel_title') }}`,
        cancel_desc: `{!! __('employee/orders/index.js_cancel_desc') !!}`,
        cancel_btn: `{{ __('employee/orders/index.js_cancel_btn') }}`
    };

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