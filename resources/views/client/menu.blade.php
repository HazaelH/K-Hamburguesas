@extends('layouts.app')

@section('titulo', __('client/menu.title'))

@section('contenido')

<div class="pb-20 min-h-screen bg-gray-900">

    <div class="text-center py-6 sm:py-10 animate-fade-in-down px-4">
        <h1 class="text-3xl sm:text-4xl font-extrabold text-white mb-2">{{ $tituloRecomendacion }}</h1>
        <p class="text-gray-400 text-sm sm:text-base">{{ __('client/menu.subtitle') }}</p>
    </div>

    {{-- BARRA DE CONTROLES (Sticky) --}}
    <div class="sticky top-0 z-30 bg-gray-900/95 backdrop-blur-md border-b border-gray-300 py-4 mb-6 shadow-sm">
        <div class="container mx-auto px-4 space-y-5">
            
            <div class="flex flex-col sm:flex-row gap-4 justify-between items-center">
                <div class="relative w-full sm:w-1/2 lg:w-1/3">
                    <i class="fas fa-search absolute left-4 top-3.5 text-gray-500"></i>
                    <input type="text" id="search-input" placeholder="{{ __('client/menu.search_placeholder') }}" 
                           class="w-full bg-gray-800 text-white rounded-xl pl-11 pr-4 py-3 border border-gray-300 focus:ring-2 focus:ring-orange-500 transition-all text-sm shadow-inner">
                </div>

                <div class="flex gap-3 w-full sm:w-auto justify-between sm:justify-end">
                    <div class="w-full sm:w-48">
                        <select id="sort-select" aria-label="Ordenar productos" class="w-full bg-gray-800 text-white rounded-xl px-4 py-3 border border-gray-300 focus:ring-2 focus:ring-orange-500 outline-none cursor-pointer text-sm shadow-sm">
                            <option value="default">{{ __('client/menu.sort_recommended') }}</option>
                            <option value="price-asc">{{ __('client/menu.sort_price_low') }}</option>
                            <option value="price-desc">{{ __('client/menu.sort_price_high') }}</option>
                            <option value="name-asc">{{ __('client/menu.sort_az') }}</option>
                        </select>
                    </div>

                    <div class="flex bg-gray-800 rounded-xl p-1.5 border border-gray-300 flex-shrink-0 shadow-sm">
                        <button onclick="setGlobalView('grid')" id="btn-view-grid" class="p-2 w-10 h-10 flex items-center justify-center text-orange-500 bg-gray-900 rounded-lg transition-all" title="{{ __('client/menu.view_grid') }}">
                            <i class="fas fa-th-large"></i>
                        </button>
                        <button onclick="setGlobalView('list')" id="btn-view-list" class="p-2 w-10 h-10 flex items-center justify-center text-gray-500 hover:text-white transition-all" title="{{ __('client/menu.view_list') }}">
                            <i class="fas fa-list"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div>
                @php
                    // Obtenemos todas las categorías de la BD directamente para los filtros
                    $categoriasBd = \App\Models\Categoria::all();
                @endphp

                <div class="block md:hidden">
                    <select id="mobile-category-select" aria-label="Filtrar por categoría" onchange="cambiarCategoria(this.value, null)" class="w-full bg-gray-800 text-white rounded-xl px-4 py-3 border border-gray-300 focus:ring-2 focus:ring-orange-500 font-bold text-sm shadow-sm">
                        <option value="Todas">{{ __('client/menu.all_catalog') }}</option>
                        @foreach($categoriasBd as $cat)
                            @php
                                $nombreMostrar = $cat->nombre;
                                if (app()->getLocale() == 'en' && !empty($cat->nombre_en)) $nombreMostrar = $cat->nombre_en;
                                if (app()->getLocale() == 'pt' && !empty($cat->nombre_pt)) $nombreMostrar = $cat->nombre_pt;
                            @endphp
                            <option value="{{ $cat->nombre }}">{{ $nombreMostrar }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="hidden md:flex flex-wrap gap-3 pb-2 items-center justify-center">
                    <button onclick="cambiarCategoria('Todas', this)" 
                            class="filter-btn active px-5 py-2.5 rounded-xl font-bold text-sm transition-all bg-orange-700 text-white shadow-[0_4px_15px_rgba(234,88,12,0.3)] border border-orange-500 transform hover:-translate-y-1"
                            data-category="Todas">
                        <i class="fas fa-star text-orange-200 mr-1"></i> {{ __('client/menu.all_menu') }}
                    </button>

                    @foreach($categoriasBd as $cat)
                        @php
                            $nombreMostrar = $cat->nombre;
                            if (app()->getLocale() == 'en' && !empty($cat->nombre_en)) $nombreMostrar = $cat->nombre_en;
                            if (app()->getLocale() == 'pt' && !empty($cat->nombre_pt)) $nombreMostrar = $cat->nombre_pt;
                        @endphp
                        <button onclick="cambiarCategoria('{{ $cat->nombre }}', this)" 
                                class="filter-btn px-5 py-2.5 rounded-xl font-bold text-sm transition-all bg-gray-800 text-gray-400 hover:bg-gray-700 hover:text-white border border-gray-300 hover:shadow-lg transform hover:-translate-y-1"
                                data-category="{{ $cat->nombre }}">
                            {{ $nombreMostrar }}
                        </button>
                    @endforeach
                </div>
            </div>
            
        </div>
    </div>

    {{-- ZONA DE RESULTADOS --}}
    <div class="container mx-auto px-4">
        
        <div id="no-results-msg" class="hidden text-center py-16">
            <i class="fas fa-ghost text-5xl text-gray-600 mb-4"></i>
            <h2 class="text-xl font-bold text-white">{{ __('client/menu.no_results') }}</h2>
            <p class="text-gray-400 mt-2 text-sm">{{ __('client/menu.no_results_desc') }}</p>
        </div>

        {{-- GRID PRINCIPAL --}}
        <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 sm:gap-6 view-grid" id="products-grid">
            @foreach($productosIniciales as $producto)
                
                {{-- Data attributes calculados en PHP para que el sort-select funcione con dólares --}}
                @php
                    $precioFiltro = convertCurrencyValue($producto->precio_final ?? $producto->precio);
                @endphp

                <div class="product-card flex flex-col h-full transition-all duration-500 transform scale-100 bg-gray-800 rounded-2xl sm:rounded-3xl overflow-hidden shadow-lg border border-gray-300 relative {{ $producto->is_active ? 'hover:border-orange-500/50 hover:-translate-y-1 hover:shadow-2xl' : 'opacity-60 grayscale' }}"
                     data-categoria="{{ $producto->categoria }}"
                     data-precio="{{ $precioFiltro }}"
                     data-nombre="{{ strtolower($producto->nombre_traducido) }}"
                     data-desc="{{ strtolower($producto->descripcion_traducida) }}">
                    
                    <div class="img-container w-full h-40 sm:h-56 relative flex-shrink-0 {{ $producto->is_active ? 'cursor-pointer' : 'cursor-not-allowed' }} overflow-hidden group" 
                         onclick="{{ $producto->is_active ? 'abrirModal('.json_encode($producto).', \''.app()->getLocale().'\')' : '' }}">
                        
                        <img src="{{ asset('imagenes/' . $producto->imagen_url) }}" 
                             alt="{{ $producto->nombre_traducido }}" 
                             class="w-full h-full object-cover transform {{ $producto->is_active ? 'group-hover:scale-110' : '' }} transition duration-700">
                        
                        <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-transparent to-transparent opacity-80"></div>
                        
                        @if($producto->is_active)
                            <div class="absolute bottom-2 right-2 sm:bottom-4 sm:right-4 flex flex-col items-end">
                                @if($producto->precio_final < $producto->precio)
                                    <span class="bg-red-600 text-white text-[10px] sm:text-xs font-black px-3 py-0.5 rounded-t-md shadow-md animate-pulse">{{ __('client/menu.sale_badge') }}</span>
                                    <div class="bg-orange-700 text-white font-black px-2 py-1 sm:px-4 sm:py-1.5 rounded-b-md rounded-tl-md text-xs sm:text-lg shadow-md flex items-center gap-2">
                                        <span class="line-through text-orange-300 text-[10px] sm:text-xs opacity-80">{{ formatCurrency($producto->precio) }}</span>
                                        <span>{{ formatCurrency($producto->precio_final) }}</span>
                                    </div>
                                @else
                                    <div class="bg-orange-700 text-white font-black px-3 py-1 sm:px-4 sm:py-1.5 rounded-xl text-xs sm:text-lg shadow-md border border-orange-500/50">
                                        {{ formatCurrency($producto->precio) }}
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="absolute inset-0 bg-black/60 flex items-center justify-center z-10 backdrop-blur-[2px]">
                                <span class="bg-red-600/90 text-white font-black uppercase text-[10px] sm:text-lg transform -rotate-12 border border-white px-3 py-1 sm:px-4 sm:py-2 rounded shadow-2xl">
                                    {{ __('client/menu.sold_out') }}
                                </span>
                            </div>
                        @endif
                    </div>

                    <div class="card-content p-4 sm:p-6 z-20 relative bg-gray-800 flex-1 flex flex-col justify-between">
                        <div>
                            <div class="cat-badge flex justify-start mb-3">
                                <span class="text-[9px] sm:text-[10px] font-black text-orange-400 uppercase tracking-widest bg-orange-500/10 px-3 py-1.5 rounded-full border border-orange-500/20 shadow-sm">
                                    {{ $producto->categoria_traducida }}
                                </span>
                            </div>

                            <h2 class="card-title text-base sm:text-xl font-black text-white mb-2 {{ $producto->is_active ? 'cursor-pointer hover:text-orange-500 transition-colors' : '' }} leading-tight" 
                                onclick="{{ $producto->is_active ? 'abrirModal('.json_encode($producto).', \''.app()->getLocale().'\')' : '' }}">
                                {{ $producto->nombre_traducido }}
                            </h2>
                            
                            <p class="card-desc text-gray-400 text-xs sm:text-sm mb-4 sm:mb-6 line-clamp-2 leading-relaxed">{{ $producto->descripcion_traducida }}</p>
                        </div>
                        
                        @if($producto->is_active)
                            <button onclick="abrirModal({{ json_encode($producto) }}, '{{ app()->getLocale() }}')" 
                                    class="w-full bg-gray-700 hover:bg-orange-700 text-white font-bold py-2.5 sm:py-3.5 rounded-xl transition-all duration-300 flex items-center justify-center group hover:shadow-[0_0_15px_rgba(234,88,12,0.4)] mt-auto border border-gray-300 hover:border-orange-500">
                                <i class="fas fa-plus sm:mr-2 transform group-hover:rotate-90 transition-transform"></i> 
                                <span class="btn-text ml-1 text-xs sm:text-base tracking-wide">{{ __('client/menu.add_btn') }}</span>
                            </button>
                        @else
                            <button disabled class="w-full bg-gray-900 text-gray-600 font-bold py-2.5 sm:py-3.5 rounded-xl border border-gray-300 cursor-not-allowed mt-auto text-xs sm:text-base flex items-center justify-center gap-2 shadow-inner">
                                <i class="fas fa-ban"></i> {{ __('client/menu.not_available') }}
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

{{-- MODAL DEL PRODUCTO --}}
<div id="product-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 sm:p-6" aria-modal="true"
     data-msg-success-title="{{ __('client/messages.js_success_title') }}"
     data-msg-success-desc="{{ __('client/messages.js_success_desc') }}"
     data-msg-preparing="{{ __('client/messages.js_preparing') }}"
     data-msg-denied="{{ __('client/messages.js_denied') }}"
     data-msg-error="{{ __('client/messages.js_db_error') }}">
    <div class="fixed inset-0 bg-black/80 backdrop-blur-sm transition-opacity" onclick="cerrarModal()"></div>

    <div class="relative w-full max-w-2xl bg-slate-800 rounded-3xl shadow-2xl border border-slate-700 overflow-hidden transform transition-all translate-y-8 opacity-0 scale-95 duration-300 flex flex-col max-h-[90vh]" id="modal-container">
        
        <button onclick="cerrarModal()" class="absolute top-4 right-4 z-50 w-10 h-10 bg-black/50 hover:bg-red-500 text-white rounded-full flex items-center justify-center backdrop-blur-sm transition-colors border border-white/10">
            <i class="fas fa-times text-lg"></i>
        </button>

        <div class="h-56 sm:h-72 w-full relative bg-slate-900 shrink-0 p-4">
            <img id="modal-img" src="" class="w-full h-full object-contain relative z-10 drop-shadow-2xl">
            <div class="absolute inset-0 bg-gradient-to-t from-slate-800 via-slate-800/30 to-transparent opacity-90 z-20 pointer-events-none"></div>
        </div>

        <div class="p-6 sm:p-8 overflow-y-auto custom-scrollbar">
            
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-end mb-4 gap-2">
                <h2 class="text-2xl sm:text-3xl font-black text-white leading-tight drop-shadow-lg" id="modal-title">Platillo</h2>
                <p class="text-2xl font-black text-orange-500 bg-slate-900 px-4 py-1.5 rounded-xl shadow-inner border border-slate-700 inline-block w-max" id="modal-price">$0.00</p>
            </div>
            
            <p class="text-slate-400 text-sm mb-6 leading-relaxed" id="modal-desc">Detalles...</p>

            <div class="space-y-6">
                
                <div id="opciones-contenedor" class="hidden bg-slate-900/50 p-4 rounded-2xl border border-slate-700/50">
                    <label class="block text-xs font-bold text-orange-400 mb-3 uppercase tracking-wider"><i class="fas fa-sliders-h mr-1"></i> {{ __('client/menu.customize_dish') }}</label>
                    <div id="modal-opciones-dinamicas" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 items-end">
                    
                    <div class="space-y-4">
                        <div class="flex items-center justify-between bg-slate-900 p-2 border border-slate-700 rounded-2xl shadow-inner">
                            <span class="text-slate-400 font-bold ml-4 uppercase tracking-wider text-xs flex items-center gap-2"><i class="fas fa-utensils text-slate-500"></i> {{ __('client/menu.quantity') }}</span>
                            <div class="flex items-center space-x-2">
                                <button onclick="cambiarCantidad(-1)" class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-slate-800 border border-slate-700 text-white hover:bg-orange-500 hover:border-orange-500 transition font-bold text-xl active:scale-95 shadow-sm">-</button>
                                <span id="cantidad-span" class="text-white font-black text-2xl w-10 text-center transition-transform">1</span>
                                <button onclick="cambiarCantidad(1)" class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-orange-700 text-white hover:bg-orange-500 transition font-bold text-xl active:scale-95 shadow-[0_0_10px_rgba(234,88,12,0.3)]">+</button>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-400 mb-2 uppercase tracking-wider">{{ __('client/menu.additional_notes') }}</label>
                            <textarea id="modal-notas" rows="1" class="w-full bg-slate-900 text-white border border-slate-700 rounded-xl p-3 focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all text-sm placeholder-slate-600 resize-none shadow-inner" placeholder="{{ __('client/menu.placeholder_notes') }}"></textarea>
                        </div>
                    </div>

                    <button onclick="agregarAlCarrito()" class="w-full bg-orange-700 hover:bg-orange-500 text-white font-black py-4 rounded-2xl shadow-[0_0_15px_rgba(234,88,12,0.4)] hover:shadow-[0_0_25px_rgba(234,88,12,0.6)] transform transition hover:-translate-y-0.5 active:scale-[0.98] flex flex-col items-center justify-center gap-1 group h-full max-h-[110px]">
                        <span id="btn-add-text" class="text-sm sm:text-base flex items-center gap-2 uppercase tracking-wider"><i class="fas fa-shopping-cart group-hover:animate-bounce"></i> {{ __('client/menu.add_to_my_order') }}</span>
                        <span id="modal-total" class="bg-black/20 px-4 py-1 rounded-xl text-xl sm:text-2xl font-black tracking-wide border border-white/10 w-3/4 text-center mt-1">$0.00</span>
                    </button>
                    
                </div>
            </div>
        </div>
    </div>
</div>

<div id="toast-container" class="fixed bottom-5 right-5 z-50 flex flex-col gap-3 pointer-events-none"></div>

{{-- Inyectamos variables globales de idioma y moneda para JavaScript --}}
<script>
    window.MENU_LANG = {
        locale: '{{ app()->getLocale() }}',
        currencySymbol: '{{ app()->getLocale() == "en" ? "$" : (app()->getLocale() == "pt" ? "R$" : "$") }}',
        currencyCode: '{{ app()->getLocale() == "en" ? " USD" : (app()->getLocale() == "pt" ? "" : " MXN") }}',
        exchangeRate: {{ app()->getLocale() == "en" ? Cache::get('exchange_rate_usd', 20.00) : (app()->getLocale() == "pt" ? Cache::get('exchange_rate_brl', 3.50) : 1) }}
    };
</script>

<style>
    @media (min-width: 768px) {
        .view-list {
            max-width: 900px;
            margin-left: auto;
            margin-right: auto;
        }
        .view-list .product-card {
            flex-direction: row !important;
            height: 180px; 
        }
        .view-list .img-container {
            width: 280px !important;
            height: 100% !important;
        }
        .view-list .card-content {
            display: flex;
            flex-direction: row !important;
            align-items: center;
            padding: 1.5rem 2rem !important;
        }
        .view-list .card-content > div:first-child {
            flex: 1;
            padding-right: 2rem;
        }
        .view-list .card-content button {
            width: auto !important;
            padding-left: 2rem;
            padding-right: 2rem;
            margin-top: 0 !important;
        }
    }
</style>
@vite(['resources/css/menu.css', 'resources/js/client/menu-interaction.js'])
@endsection