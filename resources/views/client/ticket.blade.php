@extends('layouts.app')

@section('titulo', 'Ticket de Compra')

@section('contenido')
<div class="min-h-screen py-10 flex items-center justify-center bg-gray-100">
    
    {{-- Contenedor Principal --}}
    <div id="ticket-imprimible" class="bg-white text-slate-900 w-full max-w-md shadow-2xl rounded-sm overflow-hidden relative">
        
        {{-- Borde dentado ARRIBA (Efecto papel) --}}
        <div class="absolute top-0 left-0 w-full h-4 bg-slate-900 print:hidden" 
             style="clip-path: polygon(0% 0%, 5% 100%, 10% 0%, 15% 100%, 20% 0%, 25% 100%, 30% 0%, 35% 100%, 40% 0%, 45% 100%, 50% 0%, 55% 100%, 60% 0%, 65% 100%, 70% 0%, 75% 100%, 80% 0%, 85% 100%, 90% 0%, 95% 100%, 100% 0%);">
        </div>

        <div class="p-8 pt-12 pb-16">
            
            {{-- 1. HEADER --}}
            <div class="text-center mb-6 border-b-2 border-dashed border-slate-300 pb-6">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-slate-900 text-white rounded-full mb-4 print:hidden">
                    <i class="fas fa-utensils text-2xl"></i>
                </div>
                <h2 class="text-2xl font-black uppercase tracking-widest">K-Hamburguesas</h2>
                <p class="text-xs text-slate-500 font-mono mt-1">Sabor que manda</p>
                
                <div class="mt-4 text-sm font-mono text-slate-600 flex justify-between px-4">
                    <div class="text-left">
                        <span class="block text-[10px] uppercase text-slate-400">Orden</span>
                        <span class="font-bold">#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <div class="text-right">
                        <span class="block text-[10px] uppercase text-slate-400">Fecha</span>
                        <span class="font-bold">{{ $order->created_at->format('d/m/Y') }}</span>
                        <span class="block text-[10px]">{{ $order->created_at->format('h:i A') }}</span>
                    </div>
                </div>
            </div>

            {{-- 2. SECCIÓN QR DE ENTREGA (NUEVA) --}}
            @if($order->codigo_entrega)
            <div class="mb-8 bg-slate-50 border-2 border-slate-900 border-dashed rounded-lg p-4 text-center relative overflow-hidden">
                <p class="text-[10px] font-bold uppercase text-slate-400 mb-2 tracking-widest">Escanea para recibir</p>
                
                {{-- QR --}}
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ $order->codigo_entrega }}" 
                     alt="QR Entrega" 
                     class="mx-auto w-32 h-32 mix-blend-multiply opacity-90 mb-2">
                
                {{-- Código Manual --}}
                <div class="bg-white border border-slate-200 inline-block px-4 py-1 rounded shadow-sm">
                    <p class="text-2xl font-black font-mono tracking-[0.2em] text-slate-900">{{ $order->codigo_entrega }}</p>
                </div>
                <p class="text-[9px] text-slate-400 mt-2">Muestra este código al repartidor/cajero</p>
            </div>
            @endif

            {{-- 3. LISTA DE ITEMS --}}
            <div class="mb-6">
                <table class="w-full text-sm font-mono">
                    <thead>
                        <tr class="text-slate-400 text-[10px] border-b border-slate-200">
                            <th class="text-left pb-2 w-8">CANT</th>
                            <th class="text-left pb-2">DESCRIPCIÓN</th>
                            <th class="text-right pb-2">TOTAL</th>
                        </tr>
                    </thead>
                    <tbody class="text-slate-700">
                        @foreach($order->items as $item)
                        <tr>
                            <td class="py-3 pr-2 align-top font-bold">{{ $item->cantidad }}</td>
                            <td class="py-3 align-top">
                                <span class="block font-bold">{{ $item->product->nombre }}</span>
                                @php 
                                    // Decodificación segura del JSON
                                    $opciones = is_string($item->opciones) ? json_decode($item->opciones, true) : ($item->opciones ?? []);
                                @endphp
                                @if(!empty($opciones))
                                    <div class="text-[10px] text-slate-500 leading-tight mt-1 pl-2 border-l-2 border-slate-200">
                                        @foreach($opciones as $op)
                                            {{ is_array($op) ? $op['valor'] : $op->valor }}<br>
                                        @endforeach
                                    </div>
                                @endif
                            </td>
                            <td class="py-3 text-right align-top font-bold">
                                ${{ number_format($item->precio_unitario * $item->cantidad, 2) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- 4. TOTALES --}}
            <div class="border-t-2 border-dashed border-slate-300 pt-4 mb-8">
                @php
                    // Lógica para separar envío del subtotal
                    $datos = is_string($order->datos_entrega) ? json_decode($order->datos_entrega, true) : ($order->datos_entrega ?? []);
                    $costoEnvio = $datos['costo_envio_cobrado'] ?? 0;
                    $subtotal = $order->total - $costoEnvio;
                @endphp

                <div class="flex justify-between items-center text-xs font-mono mb-1">
                    <span class="text-slate-500">Subtotal</span>
                    <span>${{ number_format($subtotal, 2) }}</span>
                </div>
                
                @if($costoEnvio > 0)
                <div class="flex justify-between items-center text-xs font-mono mb-1">
                    <span class="text-slate-500">Envío</span>
                    <span>${{ number_format($costoEnvio, 2) }}</span>
                </div>
                @endif

                <div class="flex justify-between items-center text-2xl font-black mt-3 pt-3 border-t border-slate-100">
                    <span>TOTAL</span>
                    <span>${{ number_format($order->total, 2) }}</span>
                </div>
                
                <div class="mt-6 grid grid-cols-2 gap-4 text-center text-xs font-mono text-slate-500">
                    <div class="border border-slate-200 rounded p-2">
                        <span class="block text-[9px] uppercase">Método Pago</span>
                        <span class="uppercase font-bold text-slate-800">{{ $order->metodo_pago }}</span>
                    </div>
                    <div class="border border-slate-200 rounded p-2">
                        <span class="block text-[9px] uppercase">Estado</span>
                        <span class="uppercase font-bold {{ $order->status == 'entregado' ? 'text-green-600' : 'text-orange-500' }}">
                            {{ $order->status }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- 5. FOOTER DECORATIVO --}}
            <div class="text-center">
                {{-- Código de barras CSS --}}
                <div class="h-8 w-3/4 mx-auto mb-2 opacity-50" 
                     style="background: repeating-linear-gradient(90deg, #000, #000 1px, transparent 1px, transparent 3px, #000 3px, #000 4px);">
                </div>
                <p class="text-[9px] text-slate-400 font-mono">ID REF: {{ $order->created_at->timestamp }}-{{ $order->id }}</p>
                <p class="text-[9px] text-slate-400 mt-1">¡Gracias por tu preferencia!</p>
            </div>

        </div>

        {{-- 6. BOTONES (No se imprimen) --}}
        <div class="bg-slate-100 p-4 flex gap-4 print:hidden absolute bottom-4 left-0 w-full z-10 px-8">
            <a href="{{ route('menu') }}" class="flex-1 bg-white border border-slate-300 text-slate-700 py-3 rounded-lg font-bold text-center text-sm hover:bg-slate-50 transition shadow-sm">
                <i class="fas fa-arrow-left mr-2"></i> Menú
            </a>
            <button onclick="window.print()" class="flex-1 bg-slate-900 text-white py-3 rounded-lg font-bold text-sm hover:bg-slate-800 transition shadow-lg shadow-slate-900/20">
                <i class="fas fa-print mr-2"></i> Imprimir
            </button>
        </div>
        
        {{-- Borde dentado ABAJO (Efecto papel) --}}
        <div class="absolute bottom-0 left-0 w-full h-4 bg-slate-900 print:hidden" 
             style="clip-path: polygon(0% 100%, 5% 0%, 10% 100%, 15% 0%, 20% 100%, 25% 0%, 30% 100%, 35% 0%, 40% 100%, 45% 0%, 50% 100%, 55% 0%, 60% 100%, 65% 0%, 70% 100%, 75% 0%, 80% 100%, 85% 0%, 90% 100%, 95% 0%, 100% 100%);">
        </div>
    </div>
</div>

<style>
    @media print {
        @page { margin: 0; }
        body { 
            background: white; 
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding-top: 20px;
        }
        nav, footer, .print\:hidden { display: none !important; }
        
        #ticket-imprimible {
            box-shadow: none !important;
            border: none !important;
            width: 100%;
            max-width: 80mm; /* Ancho estándar de impresora térmica */
            padding-bottom: 0 !important;
        }
        
        /* Ajustes para ahorrar tinta y mejorar legibilidad */
        img { filter: grayscale(100%) contrast(150%); }
    }
</style>
@endsection