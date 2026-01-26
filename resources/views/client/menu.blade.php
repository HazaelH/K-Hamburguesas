@extends('layouts.app')

@section('titulo', 'Nuestro Menú')

@section('contenido')
<div class="pb-20 min-h-screen">

    <div class="text-center py-10 animate-fade-in-down">
        <h1 class="text-4xl font-extrabold text-white mb-2">{{ $tituloRecomendacion }}</h1>
        <p class="text-gray-400">Selecciona una categoría para filtrar</p>
    </div>

    {{-- BARRA DE FILTROS --}}
    <div class="sticky top-0 z-30 bg-gray-900/95 backdrop-blur border-b border-gray-800 py-4 mb-8">
        <div class="container mx-auto px-4">
            <div class="flex overflow-x-auto gap-4 scrollbar-hide pb-2">
                <button onclick="filtrarCategoria('Todas')" 
                        class="filter-btn active whitespace-nowrap px-6 py-2 rounded-full font-bold text-sm transition bg-orange-600 text-white shadow-lg ring-2 ring-orange-500"
                        data-category="Todas">
                    Todas
                </button>

                @foreach($categorias as $cat)
                    <button onclick="filtrarCategoria('{{ $cat }}')" 
                            class="filter-btn whitespace-nowrap px-6 py-2 rounded-full font-bold text-sm transition bg-gray-800 text-gray-400 hover:bg-gray-700 hover:text-white"
                            data-category="{{ $cat }}">
                        {{ $cat }}
                    </button>
                @endforeach
            </div>
        </div>
    </div>

    {{-- GRID DE PRODUCTOS --}}
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6" id="products-grid">
            @foreach($productosIniciales as $producto)
                
                {{-- LÓGICA DE ESTADO: Definimos opacidad y si es clickeable --}}
                <div class="product-card bg-gray-800 rounded-2xl overflow-hidden shadow-lg border border-gray-700 flex flex-col group relative transition duration-300
                            {{ $producto->is_active ? 'hover:border-orange-500/50' : 'opacity-70 grayscale' }}"
                     data-categoria="{{ $producto->categoria }}">
                    
                    {{-- IMAGEN DEL PRODUCTO --}}
                    {{-- Solo permitimos abrir modal si is_active es true --}}
                    <div class="h-52 overflow-hidden relative {{ $producto->is_active ? 'cursor-pointer' : 'cursor-not-allowed' }}" 
                         onclick="{{ $producto->is_active ? 'abrirModal('.json_encode($producto).')' : '' }}">
                        
                        <img src="{{ asset('imagenes/' . $producto->imagen_url) }}" 
                             alt="{{ $producto->nombre }}" 
                             class="w-full h-full object-cover transform {{ $producto->is_active ? 'group-hover:scale-110' : '' }} transition duration-500">
                        
                        {{-- PRECIO (Solo si activo) --}}
                        @if($producto->is_active)
                            <div class="absolute top-2 right-2 bg-black/70 backdrop-blur text-white font-bold px-3 py-1 rounded-lg border border-orange-500 text-sm shadow-md">
                                ${{ number_format($producto->precio, 2) }}
                            </div>
                        @else
                            {{-- ETIQUETA DE AGOTADO (Overlay) --}}
                            <div class="absolute inset-0 bg-black/50 flex items-center justify-center z-10">
                                <span class="bg-red-600 text-white font-black uppercase tracking-widest px-4 py-2 text-lg transform -rotate-12 border-2 border-white shadow-2xl">
                                    AGOTADO
                                </span>
                            </div>
                        @endif
                    </div>

                    {{-- INFO DEL PRODUCTO --}}
                    <div class="p-5 flex-1 flex flex-col">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-xs font-bold text-orange-400 uppercase tracking-wide border border-orange-500/20 bg-orange-500/10 px-2 py-0.5 rounded">
                                {{ $producto->categoria }}
                            </span>
                        </div>

                        <h3 class="text-xl font-bold text-white mb-2 {{ $producto->is_active ? 'cursor-pointer hover:text-orange-500 transition' : '' }}" 
                            onclick="{{ $producto->is_active ? 'abrirModal('.json_encode($producto).')' : '' }}">
                            {{ $producto->nombre }}
                        </h3>
                        
                        <p class="text-gray-400 text-sm mb-4 line-clamp-2 flex-1">{{ $producto->descripcion }}</p>
                        
                        {{-- BOTÓN DE ACCIÓN --}}
                        @if($producto->is_active)
                            <button onclick="abrirModal({{ json_encode($producto) }})" 
                                    class="w-full bg-gray-700 hover:bg-orange-600 text-white font-bold py-3 rounded-xl transition flex items-center justify-center group-hover:shadow-lg group-hover:shadow-orange-500/20">
                                <i class="fas fa-plus mr-2"></i> Agregar al Pedido
                            </button>
                        @else
                            <button disabled class="w-full bg-gray-800 text-gray-500 font-bold py-3 rounded-xl border border-gray-700 cursor-not-allowed flex items-center justify-center">
                                <i class="fas fa-ban mr-2"></i> No Disponible
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

</div>

{{-- EL RESTO DE TUS MODALES Y TOASTS SIGUEN IGUAL --}}
<div id="product-modal" class="fixed inset-0 z-50 hidden" aria-modal="true">
    <div class="fixed inset-0 bg-black/90 backdrop-blur-sm transition-opacity" onclick="cerrarModal()"></div>

    <div class="flex min-h-screen items-center justify-center p-4">
        <div class="relative w-full max-w-lg bg-gray-800 rounded-2xl shadow-2xl border border-gray-700 overflow-hidden transform transition-all">
            
            <button onclick="cerrarModal()" class="absolute top-4 right-4 z-10 bg-black/50 text-white w-8 h-8 rounded-full hover:bg-red-500 transition flex items-center justify-center">
                <i class="fas fa-times"></i>
            </button>

            <div class="h-48 w-full bg-gray-700">
                <img id="modal-img" src="" class="w-full h-full object-cover">
            </div>

            <div class="p-6">
                <div class="flex justify-between items-start mb-2">
                    <h3 class="text-2xl font-bold text-white" id="modal-title">Hamburguesa</h3>
                    <p class="text-2xl font-bold text-orange-500" id="modal-price">$0.00</p>
                </div>
                <p class="text-gray-400 text-sm mb-6" id="modal-desc">Descripción...</p>

                <div class="space-y-4">
                    <div class="flex items-center justify-between bg-gray-900 p-3 rounded-xl border border-gray-700">
                        <span class="text-white font-bold ml-2">Cantidad</span>
                        <div class="flex items-center space-x-4">
                            <button onclick="cambiarCantidad(-1)" class="w-8 h-8 rounded-full bg-gray-700 text-white hover:bg-orange-500 transition font-bold text-lg">-</button>
                            <span id="cantidad-span" class="text-white font-bold text-xl w-8 text-center">1</span>
                            <button onclick="cambiarCantidad(1)" class="w-8 h-8 rounded-full bg-gray-700 text-white hover:bg-orange-500 transition font-bold text-lg">+</button>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-400 mb-2">Notas para la cocina</label>
                        <textarea id="modal-notas" rows="2" class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg p-3 focus:outline-none focus:border-orange-500 text-sm placeholder-gray-600" placeholder="Ej: Sin cebolla, extra picante..."></textarea>
                    </div>

                    <button onclick="agregarAlCarrito()" class="w-full bg-orange-600 hover:bg-orange-700 text-white font-bold py-4 rounded-xl shadow-lg transform transition hover:scale-[1.01] flex items-center justify-center">
                        <span id="btn-add-text">Agregar al Pedido</span>
                        <span id="modal-total" class="ml-2 bg-black/20 px-2 py-0.5 rounded text-sm">$0.00</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="toast-notification" class="fixed bottom-5 right-5 z-50 transform transition-all duration-500 ease-out translate-y-24 opacity-0">
    <div class="bg-gray-900 border-l-4 border-orange-500 text-white px-6 py-4 rounded-lg shadow-2xl flex items-center gap-4 max-w-sm">
        
        <div class="bg-green-500/20 rounded-full p-2 text-green-400">
            <i class="fas fa-check-circle text-xl animate-bounce"></i>
        </div>

        <div>
            <h4 class="font-bold text-sm text-orange-400">¡Producto Agregado!</h4>
            <p class="text-xs text-gray-300" id="toast-message">Tu hamburguesa está en el carrito.</p>
        </div>

        <a href="{{ route('cart.index') }}" class="ml-2 bg-gray-800 hover:bg-gray-700 text-white text-xs font-bold py-2 px-3 rounded border border-gray-600 transition">
            Ver <i class="fas fa-arrow-right ml-1"></i>
        </a>
    </div>
</div>

<div id="toast-error" class="fixed bottom-5 right-5 z-50 transform transition-all duration-500 ease-out translate-y-24 opacity-0">
    <div class="bg-gray-900 border-l-4 border-red-500 text-white px-6 py-4 rounded-lg shadow-2xl flex items-center gap-4 max-w-sm">
        
        <div class="bg-red-500/20 rounded-full p-2 text-red-400">
            <i class="fas fa-exclamation-circle text-xl animate-pulse"></i>
        </div>

        <div>
            <h4 class="font-bold text-sm text-red-400">Hubo un problema</h4>
            <p class="text-xs text-gray-300" id="toast-error-message">No se pudo conectar.</p>
        </div>
    </div>
</div>

@vite(['resources/js/client/menu-interaction.js'])
@endsection