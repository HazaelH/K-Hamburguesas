@extends('layouts.app')

@section('titulo', 'Tu Pedido')

@section('contenido')
<div class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8 min-h-screen">

    <h1 class="text-3xl font-extrabold text-white mb-8 flex items-center">
        <i class="fas fa-shopping-cart text-orange-500 mr-3"></i> Tu Pedido
    </h1>

    @if(count($carrito) > 0)
        <div class="flex flex-col lg:flex-row gap-8">
            
            <div class="lg:w-2/3 space-y-4">
                @foreach($carrito as $item)
                    <div class="bg-gray-800 rounded-xl p-4 flex items-center justify-between border border-gray-700 shadow-lg relative group">
                        
                        <div class="flex items-center space-x-4">
                            <div class="w-20 h-20 rounded-lg overflow-hidden flex-shrink-0 bg-gray-700">
                                <img src="{{ asset('imagenes/' . $item['imagen_url']) }}" class="w-full h-full object-cover">
                            </div>
                            
                            <div>
                                <h3 class="text-lg font-bold text-white">{{ $item['nombre'] }}</h3>
                                @if($item['descripcion_mods'])
                                    <p class="text-sm text-gray-400 italic">
                                        <i class="fas fa-sticky-note mr-1 text-orange-400"></i> {{ $item['descripcion_mods'] }}
                                    </p>
                                @endif
                                <p class="text-orange-500 font-bold mt-1">${{ number_format($item['precio'], 2) }} x {{ $item['cantidad'] }}</p>
                            </div>
                        </div>

                        <div class="flex flex-col items-end space-y-2">
                            <span class="text-xl font-bold text-white">
                                ${{ number_format($item['precio'] * $item['cantidad'], 2) }}
                            </span>
                            
                            <form action="{{ route('cart.remove', $item['row_id']) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-400 hover:text-red-300 text-sm font-bold flex items-center transition hover:scale-105">
                                    <i class="fas fa-trash-alt mr-1"></i> Eliminar
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
                
                <a href="{{ route('menu') }}" class="inline-block mt-4 text-gray-400 hover:text-white transition">
                    <i class="fas fa-arrow-left mr-2"></i> Seguir agregando comida
                </a>
            </div>

        <div class="lg:w-1/3">
                <div class="bg-gray-800 rounded-2xl p-6 border border-gray-700 shadow-2xl sticky top-24">
                    <h2 class="text-xl font-bold text-white mb-6 border-b border-gray-700 pb-4">Resumen</h2>
                    
                    {{-- SUBTOTAL (Base Imponible) --}}
                    <div class="flex justify-between mb-2 text-gray-400">
                        <span>Subtotal (Sin IVA)</span>
                        <span>${{ number_format($subtotal, 2) }}</span>
                    </div>

                    {{-- IVA CALCULADO --}}
                    <div class="flex justify-between mb-6 text-gray-400">
                        <span>IVA (16%)</span>
                        <span>${{ number_format($iva, 2) }}</span>
                    </div>

                    {{-- TOTAL FINAL (Lo que paga el cliente) --}}
                    <div class="flex justify-between mb-8 text-2xl font-bold text-white border-t border-gray-700 pt-4">
                        <span>Total</span>
                        <span class="text-orange-500">${{ number_format($total, 2) }}</span>
                    </div>

                    <a href="{{ route('checkout') }}" class="block w-full bg-orange-600 hover:bg-orange-700 text-white text-center font-bold py-4 rounded-xl shadow-lg transform transition hover:scale-[1.02]">
                        Proceder al Pago <i class="fas fa-credit-card ml-2"></i>
                    </a>
                </div>
            </div>
        </div>

    @else
        <div class="text-center py-20 bg-gray-800 rounded-2xl border border-gray-700 border-dashed">
            <i class="fas fa-shopping-basket text-6xl text-gray-600 mb-4"></i>
            <h2 class="text-2xl font-bold text-white mb-2">Tu pedido está vacío</h2>
            <p class="text-gray-400 mb-8">¿No tienes hambre? ¡Nuestro menú está delicioso!</p>
            <a href="{{ route('menu') }}" class="bg-orange-600 hover:bg-orange-700 text-white font-bold py-3 px-8 rounded-full transition shadow-lg">
                Ir al Menú
            </a>
        </div>
    @endif

</div>
@endsection