@extends('layouts.app')

@section('titulo', __('client/success.title'))

@section('contenido')
<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-gray-50">
    <div class="max-w-md w-full bg-white rounded-3xl shadow-2xl relative overflow-hidden border border-gray-100">
        
        <div class="absolute top-0 left-0 w-full h-3 bg-gradient-to-r from-orange-400 to-red-500"></div>
        <div class="absolute top-0 left-1/2 transform -translate-x-1/2 -mt-6 bg-gray-900 rounded-full w-12 h-12 shadow-lg"></div>

        <div class="p-8 pb-4 text-center">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4 animate-bounce-in">
                <i class="fas fa-check text-2xl text-green-600"></i>
            </div>
            
            <h2 class="text-3xl font-extrabold text-gray-900 mb-1">{{ __('client/success.order_confirmed') }}</h2>
            <p class="text-sm text-gray-500 mb-6">{{ __('client/success.received_correctly') }}</p>

            <div class="bg-gray-50 border border-gray-200 rounded-2xl p-4 mb-6 relative overflow-hidden group hover:border-orange-300 transition-colors">
                <div class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">{{ __('client/success.your_delivery_code') }}</div>
                
                <div class="flex justify-center mb-3">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ $order->codigo_entrega }}" 
                         alt="QR Entrega" 
                         class="w-32 h-32 mix-blend-multiply opacity-90 group-hover:opacity-100 transition-opacity">
                </div>

                <div class="bg-white border border-gray-200 rounded-lg py-2 px-4 inline-block shadow-sm">
                    <span class="text-2xl font-black text-gray-800 tracking-widest font-mono select-all">{{ $order->codigo_entrega }}</span>
                </div>
                <p class="text-[10px] text-gray-400 mt-2">{{ __('client/success.show_code') }}</p>
            </div>
        </div>

        <div class="bg-gray-50 px-8 py-6 border-t border-gray-100 border-dashed">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wide">{{ __('client/success.summary') }}</h3>
                <span class="text-xs font-bold text-gray-900 bg-gray-200 px-2 py-1 rounded">{{ __('client/success.order_number') }}{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</span>
            </div>
            
            <ul class="space-y-3 mb-4">
                @foreach($order->items as $item)
                    <li class="flex justify-between items-start text-sm">
                        <div class="flex-1 pr-4">
                            {{-- PRODUCTO TRADUCIDO --}}
                            <span class="font-bold text-gray-700">{{ $item->cantidad }}x {{ $item->product ? $item->product->nombre_traducido : __('admin/orders/orders.unknown_product') }}</span>
                            @php $opciones = json_decode($item->opciones, true); @endphp
                            @if(!empty($opciones))
                                <p class="text-xs text-gray-500 italic mt-0.5 line-clamp-1">
                                    {{ implode(', ', array_column($opciones, 'valor')) }}
                                </p>
                            @endif
                        </div>
                        {{-- PRECIO FORMATEADO --}}
                        <span class="font-medium text-gray-600">{{ formatCurrency($item->precio_unitario * $item->cantidad) }}</span>
                    </li>
                @endforeach
            </ul>

            <div class="flex justify-between items-center pt-4 border-t border-gray-200">
                <span class="font-bold text-gray-800">{{ __('client/success.total') }}</span>
                {{-- TOTAL FORMATEADO --}}
                <span class="text-2xl font-black text-orange-700">{{ formatCurrency($order->total) }}</span>
            </div>
        </div>

        <div class="p-8 pt-4 space-y-3">
            <a href="{{ route('client.ticket', $order->id) }}" target="_blank" 
               class="w-full flex justify-center items-center gap-2 py-3 px-4 border-2 border-dashed border-gray-300 rounded-xl text-sm font-bold text-gray-600 hover:text-orange-700 hover:border-orange-400 hover:bg-orange-50 transition-all group">
                <i class="fas fa-file-pdf text-lg group-hover:scale-110 transition-transform"></i> {{ __('client/success.download_pdf') }}
            </a>

            <a href="{{ route('menu') }}" 
               class="w-full flex justify-center py-4 px-4 rounded-xl shadow-lg shadow-gray-900/20 text-sm font-bold text-white bg-gray-900 hover:bg-gray-800 transform hover:-translate-y-0.5 transition-all">
                {{ __('client/success.back_to_menu') }}
            </a>
        </div>
    </div>
</div>
@endsection