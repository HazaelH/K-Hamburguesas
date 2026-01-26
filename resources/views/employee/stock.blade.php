@extends('layouts.employee')

@section('titulo', 'Gestión de Stock')

@section('contenido')
<div class="h-full flex flex-col bg-slate-950 relative overflow-hidden">
    
    {{-- Fondo Decorativo (Ahora Verde Esmeralda) --}}
    <div class="absolute top-0 left-0 w-full h-full pointer-events-none z-0">
        <div class="absolute top-[-10%] right-[-10%] w-[600px] h-[600px] bg-emerald-600/10 rounded-full blur-[120px]"></div>
    </div>

    {{-- HEADER --}}
    <div class="p-6 md:p-8 z-10 flex flex-col md:flex-row justify-between items-end gap-6 border-b border-white/5 bg-slate-900/50 backdrop-blur-md sticky top-0">
        <div>
            <h1 class="text-3xl font-bold text-white flex items-center gap-3 mb-2">
                {{-- Icono con fondo Emerald --}}
                <span class="w-10 h-10 bg-emerald-600 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-500/30">
                    <i class="fas fa-boxes text-lg text-white"></i>
                </span>
                Inventario
            </h1>
            <p class="text-slate-400 text-sm">Gestiona la disponibilidad de productos en tiempo real.</p>
        </div>
        
        <div class="relative w-full md:w-96 group">
            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-500 group-focus-within:text-emerald-500 transition-colors">
                <i class="fas fa-search"></i>
            </span>
            <input type="text" id="buscador" placeholder="Buscar producto..." 
                   class="w-full bg-slate-800 text-white border border-slate-700 rounded-xl py-3 pl-12 pr-4 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all shadow-lg placeholder-slate-500">
        </div>
    </div>

    {{-- GRID DE PRODUCTOS --}}
    <div class="flex-1 overflow-y-auto p-6 md:p-8 z-10 custom-scrollbar">
        
        @php $categoriaActual = null; @endphp

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6" id="grid-productos">
            @foreach($productos as $producto)
                
                {{-- Separadores de Categoría --}}
                @if($categoriaActual != $producto->categoria)
                    @php $categoriaActual = $producto->categoria; @endphp
                    <div class="col-span-full mt-4 mb-2 flex items-center gap-4">
                        {{-- Título Emerald --}}
                        <h2 class="text-lg font-bold text-emerald-400 uppercase tracking-widest">{{ $categoriaActual }}</h2>
                        <div class="h-[1px] bg-emerald-500/30 flex-1"></div>
                    </div>
                @endif

                {{-- TARJETA DE PRODUCTO --}}
                <div class="product-card group relative bg-slate-800/40 backdrop-blur-sm border border-white/5 rounded-2xl p-4 transition-all duration-300 hover:bg-slate-800 hover:border-emerald-500/30 hover:-translate-y-1 shadow-lg"
                     id="card-{{ $producto->id_producto }}"
                     data-nombre="{{ strtolower($producto->nombre) }}">
                    
                    <div class="flex items-start justify-between gap-4">
                        {{-- Info e Imagen --}}
                        <div class="flex items-center gap-4">
                            <div class="relative w-14 h-14 rounded-xl overflow-hidden bg-slate-700 shrink-0 border border-white/10">
                                @if($producto->imagen_url)
                                    <img src="{{ asset('imagenes/' . $producto->imagen_url) }}" class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-500">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-slate-500"><i class="fas fa-image"></i></div>
                                @endif
                                
                                {{-- Indicador Estado (Punto) --}}
                                <div id="dot-{{ $producto->id_producto }}" class="absolute bottom-1 right-1 w-3 h-3 rounded-full border-2 border-slate-800 {{ $producto->is_active ? 'bg-emerald-500' : 'bg-red-500' }} transition-colors shadow-sm"></div>
                            </div>
                            
                            <div>
                                <h3 class="font-bold text-white text-sm leading-tight mb-1 group-hover:text-emerald-200 transition-colors">{{ $producto->nombre }}</h3>
                                <p class="text-xs text-slate-500 font-mono">${{ number_format($producto->precio, 2) }}</p>
                            </div>
                        </div>

                        {{-- SWITCH TOGGLE --}}
<label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" 
                                onchange="toggleStock({{ $producto->id_producto }})"
                                {{ $producto->is_active ? 'checked' : '' }}>
                            
                            {{-- 
                                CORRECCIÓN VISUAL:
                                1. Cambiamos translate-x-full por translate-x-5 (medida exacta).
                                2. Aseguramos que el círculo sea blanco al activarse.
                            --}}
                            <div class="w-11 h-6 bg-slate-700 peer-focus:outline-none rounded-full peer 
                                        peer-checked:bg-emerald-600 
                                        
                                        after:content-[''] 
                                        after:absolute 
                                        after:top-[2px] after:left-[2px] 
                                        after:bg-slate-400 after:border-gray-300 after:border after:rounded-full 
                                        after:h-5 after:w-5 
                                        after:transition-all 
                                        
                                        peer-checked:after:translate-x-3 
                                        peer-checked:after:bg-white 
                                        peer-checked:after:border-white 
                                        shadow-inner"></div>
                        </label>
                    </div>

                    {{-- Etiqueta de Estado Texto --}}
                    <div class="mt-3 pt-3 border-t border-white/5 flex justify-between items-center">
                        <span class="text-[10px] text-slate-500 uppercase tracking-wider font-bold">Estado</span>
                        
                        {{-- ID CRÍTICO: status-text-{{ id_producto }} --}}
                        <span id="status-text-{{ $producto->id_producto }}" 
                              class="text-[10px] font-bold px-2 py-0.5 rounded-md transition-colors duration-300
                              {{ $producto->is_active ? 'bg-emerald-500/10 text-emerald-400' : 'bg-red-500/10 text-red-400' }}">
                            {{ $producto->is_active ? 'DISPONIBLE' : 'AGOTADO' }}
                        </span>
                    </div>

                </div>
            @endforeach
        </div>

        {{-- Estado Vacío --}}
        <div id="no-results" class="hidden flex-col items-center justify-center py-20 text-slate-500">
            <i class="fas fa-search text-4xl mb-4 opacity-50"></i>
            <p class="text-lg">No encontramos ese producto.</p>
        </div>

    </div>

    <div id="toast-container" class="fixed bottom-5 right-5 z-50 flex flex-col gap-2 pointer-events-none"></div>
</div>

@vite(['resources/js/employee/stock.js'])
@endsection