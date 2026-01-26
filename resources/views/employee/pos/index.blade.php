@extends('layouts.employee')

@section('titulo', 'Punto de Venta')

@section('contenido')
{{-- Incluimos el CSS específico de esta vista --}}
@vite(['resources/css/pos.css'])

<div class="flex flex-col md:flex-row h-[calc(100vh-64px)] md:h-full overflow-hidden bg-slate-950 relative">

    {{-- Fondo Decorativo --}}
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none z-0">
        <div class="absolute top-[-10%] left-[-10%] w-[500px] h-[500px] bg-orange-600/10 rounded-full blur-[100px]"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[500px] h-[500px] bg-blue-600/10 rounded-full blur-[100px]"></div>
    </div>

    {{-- =========================================================
         SECCIÓN 1: CATÁLOGO (Arriba en móvil, Izquierda en PC)
         ========================================================= --}}
    <div class="w-full md:flex-1 flex flex-col h-[55%] md:h-full z-10 relative border-b md:border-b-0 md:border-r border-white/5 bg-slate-950/50 backdrop-blur-sm order-1">
        
        {{-- Header Catálogo --}}
        <div class="p-3 md:p-6 pb-2 shrink-0">
            <h1 class="hidden md:flex text-2xl font-bold text-white mb-4 items-center gap-2">
                <i class="fas fa-th text-orange-500"></i> Menú
            </h1>
            
            <div class="relative group">
                <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                    <i class="fas fa-search text-lg"></i>
                </span>
                <input type="text" id="buscador-pos" placeholder="Buscar..." 
                       class="w-full bg-slate-900/80 backdrop-blur-md text-white border border-white/10 rounded-xl md:rounded-2xl py-2 md:py-3 pl-12 focus:outline-none focus:border-orange-500/50 shadow-lg transition-all placeholder-slate-500 text-sm md:text-base">
            </div>
        </div>

        {{-- Grid Productos --}}
        <div class="flex-1 overflow-y-auto p-3 md:p-6 pt-2 custom-scrollbar">
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-3 xl:grid-cols-4 gap-3 md:gap-4" id="grid-productos">
                @foreach($productos as $producto)
                    <div onclick="addToCart({{ $producto->id_producto }}, '{{ addslashes($producto->nombre) }}', {{ $producto->precio }}, '{{ asset('imagenes/' . $producto->imagen_url) }}')"
                        class="product-item group relative bg-slate-800/60 backdrop-blur-sm border border-white/5 rounded-xl md:rounded-2xl overflow-hidden cursor-pointer active:scale-95 flex flex-col h-full min-h-[140px]"
                        data-nombre="{{ strtolower($producto->nombre) }}">
                        
                        {{-- Imagen --}}
                        <div class="h-24 md:h-32 w-full relative overflow-hidden bg-slate-800 shrink-0">
                            @if($producto->imagen_url)
                                <img src="{{ asset('imagenes/' . $producto->imagen_url) }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-slate-600"><i class="fas fa-image"></i></div>
                            @endif
                            <div class="absolute top-1 right-1 bg-black/60 backdrop-blur-md rounded-md px-1.5 py-0.5 text-white text-[10px] md:text-xs font-bold">
                                ${{ number_format($producto->precio, 0) }}
                            </div>
                        </div>
                        
                        {{-- Info --}}
                        <div class="p-2 md:p-3 flex-1 flex flex-col justify-center bg-slate-900/40">
                            <h4 class="font-bold text-white text-xs md:text-sm leading-tight line-clamp-2">
                                {{ $producto->nombre }}
                            </h4>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- =========================================================
         SECCIÓN 2: TICKET / CARRITO (Abajo en móvil, Derecha en PC)
         ========================================================= --}}
    <div class="w-full md:w-96 flex flex-col h-[45%] md:h-full bg-slate-900 shadow-[0_-5px_20px_rgba(0,0,0,0.5)] md:shadow-2xl z-20 border-l border-white/10 relative order-2">
        
        {{-- Agarre móvil --}}
        <div class="md:hidden w-full flex justify-center pt-2 pb-1 bg-slate-900" onclick="toggleCartMobile()">
            <div class="w-12 h-1 bg-slate-700 rounded-full"></div>
        </div>

        {{-- HEADER TICKET --}}
        <div class="px-4 pb-2 md:p-5 bg-slate-900 border-b border-white/10 shrink-0">
            <div class="hidden md:flex justify-between items-center mb-4">
                <h2 class="text-white font-bold text-lg flex items-center gap-2">
                    <span class="w-2 h-6 bg-orange-500 rounded-full"></span> Orden Actual
                </h2>
                <button onclick="cart=[]; updateCartUI();" class="text-xs text-red-400 hover:text-white flex items-center gap-1 transition-colors">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-1 gap-2 md:gap-3">
                <div class="relative">
                    <select id="pos-mesa" class="w-full bg-slate-800 text-white rounded-lg md:rounded-xl py-2 pl-2 md:pl-10 pr-6 text-xs md:text-sm border border-slate-700 outline-none appearance-none">
                        <option value="">🛍️ Llevar</option>
                        @for($i=1; $i<=15; $i++) <option value="{{$i}}">🍽️ Mesa {{ $i }}</option> @endfor
                    </select>
                    <i class="fas fa-chevron-down absolute right-2 top-2.5 text-slate-500 text-[10px] pointer-events-none"></i>
                </div>
                
                <div class="relative">
                    <input type="text" id="pos-cliente" placeholder="Cliente..." 
                           class="w-full bg-slate-800 text-white rounded-lg md:rounded-xl py-2 pl-2 md:pl-10 pr-2 text-xs md:text-sm border border-slate-700 outline-none">
                </div>
            </div>
        </div>

        {{-- LISTA DE ITEMS --}}
        <div class="flex-1 overflow-y-auto p-3 space-y-2 custom-scrollbar bg-slate-900/50 min-h-0" id="cart-items">
            {{-- JS Inyecta aquí --}}
        </div>

        {{-- FOOTER TOTAL --}}
        <div class="p-3 md:p-5 bg-slate-900 border-t border-white/10 shrink-0 z-30">
            
            {{-- SELECTOR DE PAGO --}}
            <div class="grid grid-cols-2 gap-2 mb-4">
                <label class="cursor-pointer relative">
                    <input type="radio" name="metodo_pago_pos" value="efectivo" class="peer sr-only" checked>
                    <div class="bg-slate-800 border border-slate-700 text-slate-400 py-2 rounded-lg text-center text-xs font-bold uppercase transition-all
                                peer-checked:bg-green-600 peer-checked:text-white peer-checked:border-green-500 peer-checked:shadow-lg hover:bg-slate-700">
                        <i class="fas fa-money-bill-wave mr-1"></i> Efectivo
                    </div>
                </label>

                <label class="cursor-pointer relative">
                    <input type="radio" name="metodo_pago_pos" value="tarjeta" class="peer sr-only">
                    <div class="bg-slate-800 border border-slate-700 text-slate-400 py-2 rounded-lg text-center text-xs font-bold uppercase transition-all
                                peer-checked:bg-blue-600 peer-checked:text-white peer-checked:border-blue-500 peer-checked:shadow-lg hover:bg-slate-700">
                        <i class="fas fa-credit-card mr-1"></i> Tarjeta
                    </div>
                </label>
            </div>
            
            {{-- TOTAL --}}
            <div class="flex justify-between items-end mb-2 md:mb-4 px-1">
                <span class="text-slate-400 text-xs md:text-sm font-medium">Total</span>
                <span class="text-2xl md:text-3xl font-black text-white tracking-tight" id="cart-total">$0.00</span>
            </div>
            
            {{-- BOTÓN ENVIAR --}}
            <button id="btn-pagar" onclick="submitOrder()" disabled
                    class="w-full bg-orange-600 hover:bg-orange-500 text-white font-bold py-3 md:py-4 rounded-xl shadow-lg shadow-orange-900/50 transition-all flex justify-center items-center gap-2 text-sm md:text-base disabled:opacity-50 disabled:cursor-not-allowed">
                <span>Enviar a Caja</span> <i class="fas fa-paper-plane"></i>
            </button>
        </div>

    </div>

    {{-- Toasts Container --}}
    <div id="toast-container" class="fixed top-5 left-1/2 transform -translate-x-1/2 md:translate-x-0 md:top-20 md:right-5 md:left-auto z-50 flex flex-col gap-2 pointer-events-none w-[90%] md:w-80"></div>

</div>

{{-- Incluimos el JS específico de esta vista --}}
@vite(['resources/js/employee/pos.js'])
@endsection