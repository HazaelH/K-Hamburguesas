@extends('layouts.employee')

@section('titulo', __('employee/stock/stock.title'))

@section('contenido')
<div class="h-full flex flex-col bg-slate-950 relative overflow-hidden">
    
    <div class="absolute top-0 left-0 w-full h-full pointer-events-none z-0">
        <div class="absolute top-[-10%] right-[-10%] w-[600px] h-[600px] bg-emerald-600/10 rounded-full blur-[120px]"></div>
    </div>

    <div class="p-6 md:p-8 z-10 flex flex-col md:flex-row justify-between items-end gap-6 border-b border-white/5 bg-slate-900/50 backdrop-blur-md sticky top-0">
        <div>
            <h1 class="text-3xl font-bold text-white flex items-center gap-3 mb-2">
                <span class="w-10 h-10 bg-emerald-600 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-500/30">
                    <i class="fas fa-boxes text-lg text-white"></i>
                </span>
                {{ __('employee/stock/stock.heading') }}
            </h1>
            <p class="text-slate-400 text-sm">{{ __('employee/stock/stock.subtitle') }}</p>
        </div>
        
        <div class="relative w-full md:w-96 group">
            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400 group-focus-within:text-emerald-500 transition-colors">
                <i class="fas fa-search"></i>
            </span>
            {{-- Accesibilidad: Agregado aria-label al buscador --}}
            <input type="text" id="buscador" aria-label="{{ __('employee/stock/stock.search_placeholder') }}" placeholder="{{ __('employee/stock/stock.search_placeholder') }}" 
                   class="w-full bg-slate-800 text-white border border-slate-300 rounded-xl py-3 pl-12 pr-4 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all shadow-lg placeholder-slate-400">
        </div>
    </div>

    <div class="flex-1 overflow-y-auto p-6 md:p-8 z-10 custom-scrollbar">
        @php $categoriaActual = null; @endphp
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6" id="grid-productos">
            
            @foreach($productos as $producto)
                @php 
                    $alertaPendiente = \Illuminate\Support\Facades\Cache::has('alerta_stock_' . $producto->id_producto);
                @endphp
                
                @if($categoriaActual != $producto->categoria)
                    @php $categoriaActual = $producto->categoria; @endphp
                    <div class="col-span-full mt-4 mb-2 flex items-center gap-4">
                        <h2 class="text-lg font-bold text-emerald-400 uppercase tracking-widest">
                            {{ $producto->categoria_traducida }}
                        </h2>
                        <div class="h-[1px] bg-emerald-500/30 flex-1"></div>
                    </div>
                @endif

                <div class="product-card group relative bg-slate-800/40 backdrop-blur-sm border border-white/5 rounded-2xl p-4 transition-all duration-300 hover:bg-slate-800 shadow-lg {{ $alertaPendiente ? 'opacity-70' : '' }}"
                     id="card-{{ $producto->id_producto }}" data-nombre="{{ strtolower($producto->nombre_traducido) }}">
                    
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <div class="relative w-14 h-14 rounded-xl overflow-hidden bg-slate-700 shrink-0 border border-white/10">
                                @if($producto->imagen_url)
                                    {{-- Accesibilidad: Agregado alt --}}
                                    <img src="{{ asset('imagenes/' . $producto->imagen_url) }}" alt="{{ $producto->nombre_traducido }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-slate-400"><i class="fas fa-image"></i></div>
                                @endif
                                <div class="absolute bottom-1 right-1 w-3 h-3 rounded-full border-2 border-slate-300 {{ $producto->is_active ? 'bg-emerald-500' : 'bg-red-500' }}"></div>
                            </div>
                            <div>
                                <h3 class="font-bold text-white text-sm mb-1">{{ $producto->nombre_traducido }}</h3>
                                <p class="text-xs text-slate-400 font-mono">
                                    {{ formatCurrency($producto->precio) }}
                                </p>
                            </div>
                        </div>

                        {{-- Accesibilidad: Etiqueta SR-only para el checkbox visual --}}
                        <label class="relative inline-flex items-center cursor-pointer opacity-80">
                            <span class="sr-only">Estado de {{ $producto->nombre_traducido }}</span>
                            <input type="checkbox" class="sr-only peer" {{ $producto->is_active ? 'checked' : '' }} {{ $alertaPendiente ? 'disabled' : '' }} tabindex="-1">
                            <div class="w-11 h-6 bg-slate-700 rounded-full peer peer-checked:bg-emerald-600 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-slate-300 after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-3 peer-checked:after:bg-white peer-checked:after:border-white shadow-inner"></div>
                        </label>
                    </div>

                    <div class="mt-3 pt-3 border-t border-white/5 flex justify-between items-center">
                        <div class="flex items-center gap-2">
                            <span id="status-text-{{ $producto->id_producto }}" 
                                  class="text-[10px] font-bold px-2 py-0.5 rounded-md transition-colors duration-300 {{ $producto->is_active ? 'bg-emerald-500/10 text-emerald-400' : 'bg-red-500/10 text-red-400' }}">
                                {{ $producto->is_active ? __('employee/stock/stock.status_available') : __('employee/stock/stock.status_sold_out') }}
                            </span>
                        </div>

                        @if($alertaPendiente)
                            <span class="text-[10px] bg-slate-800 text-slate-300 border border-slate-500 px-2 py-1 rounded flex items-center gap-1">
                                <i class="fas fa-clock"></i> {{ __('employee/stock/stock.waiting_admin') }}
                            </span>
                        @else
                            {{-- Accesibilidad: aria-label agregado --}}
                            <button aria-label="Solicitar cambio para {{ $producto->nombre_traducido }}" onclick="solicitarCambio({{ $producto->id_producto }})" class="text-[10px] bg-amber-500/20 hover:bg-amber-500/30 text-amber-400 border border-amber-500/40 px-2 py-1 rounded transition-colors flex items-center gap-1 font-bold">
                                <i class="fas fa-hand-paper pointer-events-none"></i> 
                                <span class="hidden md:inline">{{ $producto->is_active ? __('employee/stock/stock.req_block') : __('employee/stock/stock.req_activate') }}</span>
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div id="no-results" class="hidden flex-col items-center justify-center py-20 text-slate-400">
            <i class="fas fa-search text-4xl mb-4 opacity-50"></i>
            <p class="text-lg font-bold">{{ __('employee/stock/stock.no_results') }}</p>
        </div>
    </div>

    <div id="toast-container" class="fixed bottom-5 right-5 z-50 flex flex-col gap-2 pointer-events-none"></div>
</div>

<script>
    window.STOCK_LANG = {
        conn_error: `{{ __('employee/stock/stock.js_conn_error') }}`
    };
</script>

@vite(['resources/js/employee/stock.js'])
@endsection