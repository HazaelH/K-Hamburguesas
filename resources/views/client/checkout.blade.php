@extends('layouts.app')

@section('titulo', 'Finalizar Compra')

@section('contenido')
<div class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8 min-h-screen">
    
    <h1 class="text-3xl font-extrabold text-white mb-8 flex items-center">
        <i class="fas fa-credit-card text-orange-500 mr-3"></i> Finalizar Compra
    </h1>

    {{-- 1. ALERTA DE VALIDACIÓN (Si faltan campos) --}}
    @if ($errors->any())
        <div class="bg-red-500/10 border-l-4 border-red-500 text-red-400 p-4 mb-6 rounded-r-lg" role="alert">
            <p class="font-bold">¡Ups! Faltan datos.</p>
            <p>Por favor revisa los campos marcados en rojo.</p>
        </div>
    @endif

    {{-- 2. NUEVA ALERTA DE ERROR DE SISTEMA (Si falla la BD) --}}
    @if (session('error'))
        <div class="bg-red-600 text-white p-4 mb-6 rounded-lg shadow-lg flex items-center gap-3 animate-pulse border border-red-400">
            <i class="fas fa-exclamation-triangle text-2xl"></i>
            <div>
                <p class="font-bold text-lg">Error del Sistema</p>
                <p class="text-sm opacity-90">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    <form action="{{ route('checkout.process') }}" method="POST" class="grid grid-cols-1 lg:grid-cols-2 gap-10">
        @csrf

        {{-- COLUMNA IZQUIERDA: DATOS --}}
        <div class="space-y-6">
            
            <div class="bg-gray-800 p-6 rounded-2xl border border-gray-700 shadow-lg">
                <h2 class="text-xl font-bold text-white mb-4 border-b border-gray-700 pb-2">
                    <i class="fas fa-map-marker-alt text-orange-400 mr-2"></i> Datos de Entrega
                </h2>

                {{-- TELÉFONO --}}
                <div class="mb-4">
                    <label class="block text-gray-400 text-sm font-bold mb-2">Teléfono de contacto</label>
                    <input type="tel" name="telefono" value="{{ old('telefono') }}" placeholder="55 1234 5678"
                           class="w-full bg-gray-900 text-white border rounded-lg p-3 focus:outline-none focus:ring-1 transition
                           @error('telefono') border-red-500 focus:border-red-500 focus:ring-red-500 @else border-gray-600 focus:border-orange-500 focus:ring-orange-500 @enderror">
                    @error('telefono')
                        <p class="text-red-400 text-xs mt-1 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                    @enderror
                </div>

                {{-- CALLE Y NÚMERO --}}
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-gray-400 text-sm font-bold mb-2">Calle</label>
                        <input type="text" name="calle" value="{{ old('calle') }}" placeholder="Av. Principal"
                               class="w-full bg-gray-900 text-white border rounded-lg p-3 focus:outline-none focus:ring-1 transition
                               @error('calle') border-red-500 focus:border-red-500 @else border-gray-600 focus:border-orange-500 @enderror">
                        @error('calle') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-gray-400 text-sm font-bold mb-2">Número Ext.</label>
                        <input type="text" name="numero" value="{{ old('numero') }}" placeholder="123"
                               class="w-full bg-gray-900 text-white border rounded-lg p-3 focus:outline-none focus:ring-1 transition
                               @error('numero') border-red-500 focus:border-red-500 @else border-gray-600 focus:border-orange-500 @enderror">
                        @error('numero') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- CP Y COLONIA --}}
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-gray-400 text-sm font-bold mb-2">Código Postal</label>
                        <input type="text" name="codigo_postal" id="cp_input" value="{{ old('codigo_postal') }}" placeholder="50000" maxlength="5"
                               class="w-full bg-gray-900 text-white border rounded-lg p-3 focus:outline-none focus:ring-1 transition
                               @error('codigo_postal') border-red-500 focus:border-red-500 @else border-gray-600 focus:border-orange-500 @enderror">
                        <p class="text-xs text-orange-500 mt-1 hidden" id="cp_loading">Buscando...</p>
                        @error('codigo_postal') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    
                    <div id="colonia_container">
                        <label class="block text-gray-400 text-sm font-bold mb-2">Colonia</label>
                        <input type="text" name="colonia" id="colonia_input" list="lista_colonias" autocomplete="off" value="{{ old('colonia') }}"
                            class="w-full bg-gray-900 text-white border rounded-lg p-3 focus:outline-none focus:ring-1 placeholder-gray-600 transition
                            @error('colonia') border-red-500 focus:border-red-500 @else border-gray-600 focus:border-orange-500 @enderror"
                            placeholder="Escribe o selecciona...">
                        <datalist id="lista_colonias"></datalist>
                        @error('colonia') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- ESTADO Y MUNICIPIO (Readonly) --}}
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-gray-400 text-sm font-bold mb-2">Estado</label>
                        <input type="text" name="estado" id="estado_input" readonly value="{{ old('estado') }}"
                               class="w-full bg-gray-800 text-gray-400 border border-gray-600 rounded-lg p-3 cursor-not-allowed">
                    </div>
                    <div>
                        <label class="block text-gray-400 text-sm font-bold mb-2">Municipio</label>
                        <input type="text" name="municipio" id="municipio_input" readonly value="{{ old('municipio') }}"
                               class="w-full bg-gray-800 text-gray-400 border border-gray-600 rounded-lg p-3 cursor-not-allowed">
                    </div>
                </div>

                {{-- REFERENCIAS --}}
                <div>
                    <label class="block text-gray-400 text-sm font-bold mb-2">Referencias / Instrucciones</label>
                    <textarea name="referencias" rows="2" placeholder="Casa azul, portón negro..."
                              class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg p-3 focus:border-orange-500 focus:outline-none">{{ old('referencias') }}</textarea>
                </div>
            </div>

            {{-- SECCIÓN MÉTODO DE PAGO --}}
            <div class="bg-gray-800 p-6 rounded-2xl border border-gray-700 shadow-lg">
                <h2 class="text-xl font-bold text-white mb-4 border-b border-gray-700 pb-2">
                    <i class="fas fa-wallet text-orange-400 mr-2"></i> Método de Pago
                </h2>
                
                <div class="space-y-3">
                    <label class="flex items-center bg-gray-900 p-4 rounded-xl border cursor-pointer transition group
                                @error('metodo_pago') border-red-500 @else border-gray-600 hover:border-orange-500 @enderror">
                        <input type="radio" name="metodo_pago" value="efectivo" class="text-orange-600 focus:ring-orange-500 h-5 w-5 bg-gray-800 border-gray-500"
                               {{ old('metodo_pago', 'efectivo') == 'efectivo' ? 'checked' : '' }}>
                        <span class="ml-3 text-white font-bold group-hover:text-orange-400 transition">Efectivo (Contra entrega)</span>
                        <i class="fas fa-money-bill-wave ml-auto text-green-400 text-xl"></i>
                    </label>

                    <label class="flex items-center bg-gray-900 p-4 rounded-xl border cursor-pointer transition group
                                @error('metodo_pago') border-red-500 @else border-gray-600 hover:border-orange-500 @enderror">
                        <input type="radio" name="metodo_pago" value="tarjeta" class="text-orange-600 focus:ring-orange-500 h-5 w-5 bg-gray-800 border-gray-500"
                               {{ old('metodo_pago') == 'tarjeta' ? 'checked' : '' }}>
                        <span class="ml-3 text-white font-bold group-hover:text-orange-400 transition">Tarjeta (Terminal)</span>
                        <i class="fas fa-credit-card ml-auto text-blue-400 text-xl"></i>
                    </label>
                </div>
                @error('metodo_pago') <p class="text-red-400 text-xs mt-2 text-center">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- COLUMNA DERECHA: RESUMEN --}}
        <div>
            <div class="bg-gray-800 p-6 rounded-2xl border border-gray-700 shadow-xl sticky top-24">
                <h2 class="text-xl font-bold text-white mb-6">Resumen del Pedido</h2>
                
                <div class="space-y-4 mb-6 max-h-60 overflow-y-auto pr-2 scrollbar-thin scrollbar-thumb-gray-600">
                    @foreach($carrito as $item)
                        <div class="flex justify-between items-center text-sm">
                            <div class="flex items-center">
                                <span class="bg-gray-700 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs mr-3 font-bold shrink-0">{{ $item['cantidad'] }}</span>
                                <span class="text-gray-300 line-clamp-1">{{ $item['nombre'] }}</span>
                            </div>
                            <span class="text-white font-bold whitespace-nowrap">${{ number_format($item['precio'] * $item['cantidad'], 2) }}</span>
                        </div>
                    @endforeach
                </div>

                <div class="border-t border-gray-700 pt-4 space-y-3">
                    <div class="flex justify-between text-gray-400">
                        <span>Subtotal</span>
                        <span>${{ number_format($subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400">Costo de Envío</span>
                        @if($envio == 0)
                            <span class="text-green-400 font-bold flex items-center gap-1"><i class="fas fa-check-circle"></i> GRATIS</span>
                        @else
                            <span class="text-white font-bold">${{ number_format($envio, 2) }}</span>
                        @endif
                    </div>
                    <div class="text-xs text-center">
                        @if($envio == 0)
                            <div class="text-green-400 bg-green-500/10 px-3 py-2 rounded-lg border border-green-500/20"><i class="fas fa-gift mr-1"></i> {{ $mensajeEnvio }}</div>
                        @else
                            <div class="text-orange-300 bg-orange-500/10 px-3 py-2 rounded-lg border border-orange-500/20"><i class="fas fa-info-circle mr-1"></i> {{ $mensajeEnvio }}</div>
                        @endif
                    </div>
                    <div class="flex justify-between text-2xl font-bold text-white pt-4 border-t border-gray-700 mt-2">
                        <span>Total a Pagar</span>
                        <span class="text-orange-500">${{ number_format($totalFinal, 2) }}</span>
                    </div>
                </div>

                <button type="submit" class="w-full mt-8 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white font-bold py-4 rounded-xl shadow-lg transform transition hover:scale-[1.02] flex justify-center items-center gap-2 group">
                    <i class="fas fa-check-circle group-hover:animate-pulse"></i> Confirmar Pedido
                </button>
            </div>
        </div>

    </form> 
</div>

@vite(['resources/js/client/checkout.js'])
@endsection