@extends('layouts.admin')

@section('titulo', __('admin/offers/offers.title_edit'))

@section('contenido')
<div class="relative space-y-6 py-4 max-w-4xl mx-auto">

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-black text-white flex items-center gap-3">
            <i class="fas fa-edit text-blue-500"></i>
            {{ __('admin/offers/offers.edit_promo') }}
        </h1>
        <a href="{{ route('admin.offers.index') }}" class="bg-slate-800 hover:bg-slate-700 text-slate-300 px-4 py-2 rounded-xl transition-colors border border-slate-300 font-bold text-sm flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> {{ __('admin/offers/offers.back') }}
        </a>
    </div>

    <div class="bg-slate-800 p-8 rounded-3xl border border-slate-300 shadow-2xl relative overflow-hidden">
        <div class="absolute -top-24 -right-24 w-64 h-64 bg-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <form id="form-edit-offer" action="{{ route('admin.offers.update', $offer->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6 relative z-10">
            @csrf
            @method('PUT')
            
            <div class="space-y-3">
                <div>
                    {{-- Agregado for e id --}}
                    <label for="titulo" class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">🇲🇽 {{ __('admin/offers/offers.promo_title') }} *</label>
                    <input type="text" id="titulo" name="titulo" value="{{ old('titulo', $offer->titulo) }}" class="w-full bg-slate-900 border border-slate-300 rounded-xl p-3 text-white focus:border-blue-500 transition-all outline-none" required>
                </div>

                <div>
                    {{-- Agregado for e id --}}
                    <label for="descripcion" class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">🇲🇽 {{ __('admin/offers/offers.short_desc') }}</label>
                    <textarea id="descripcion" name="descripcion" rows="2" class="w-full bg-slate-900 border border-slate-300 rounded-xl p-3 text-white focus:border-blue-500 transition-all outline-none">{{ old('descripcion', $offer->descripcion) }}</textarea>
                </div>
            </div>

            {{-- TRADUCCIONES OCULTAS --}}
            <details class="bg-slate-900/50 rounded-xl border border-slate-300/50 group">
                <summary class="p-3 text-xs font-bold text-slate-400 uppercase tracking-wider cursor-pointer flex justify-between items-center outline-none">
                    <span><i class="fas fa-language text-blue-400 mr-2"></i> {{ __('admin/offers/offers.add_translations') }}</span>
                    <i class="fas fa-chevron-down group-open:rotate-180 transition-transform"></i>
                </summary>
                <div class="p-4 pt-2 space-y-4 border-t border-slate-300/50 mt-2">
                    {{-- Agregados aria-label a los inputs de traducción --}}
                    <div class="space-y-2 border-l-2 border-blue-500 pl-3">
                        <input type="text" aria-label="Título en Inglés" name="titulo_en" value="{{ old('titulo_en', $offer->titulo_en ?? '') }}" placeholder="🇺🇸 {{ __('admin/offers/offers.title_en') }}" class="w-full bg-slate-900 border border-slate-300 rounded-lg p-2.5 text-white text-sm focus:border-blue-500 outline-none">
                        <textarea aria-label="Descripción en Inglés" name="descripcion_en" rows="1" placeholder="🇺🇸 {{ __('admin/offers/offers.desc_en') }}" class="w-full bg-slate-900 border border-slate-300 rounded-lg p-2.5 text-white text-sm focus:border-blue-500 outline-none">{{ old('descripcion_en', $offer->descripcion_en ?? '') }}</textarea>
                    </div>
                    <div class="space-y-2 border-l-2 border-emerald-500 pl-3">
                        <input type="text" aria-label="Título en Portugués" name="titulo_pt" value="{{ old('titulo_pt', $offer->titulo_pt ?? '') }}" placeholder="🇧🇷 {{ __('admin/offers/offers.title_pt') }}" class="w-full bg-slate-900 border border-slate-300 rounded-lg p-2.5 text-white text-sm focus:border-emerald-500 outline-none">
                        <textarea aria-label="Descripción en Portugués" name="descripcion_pt" rows="1" placeholder="🇧🇷 {{ __('admin/offers/offers.desc_pt') }}" class="w-full bg-slate-900 border border-slate-300 rounded-lg p-2.5 text-white text-sm focus:border-emerald-500 outline-none">{{ old('descripcion_pt', $offer->descripcion_pt ?? '') }}</textarea>
                    </div>
                </div>
            </details>

            <div class="bg-slate-900/50 p-5 rounded-xl border border-slate-300/50 space-y-4">
                <div>
                    {{-- Agregado for para tipo_aplicacion --}}
                    <label for="tipo_aplicacion" class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">{{ __('admin/offers/offers.applies_to') }}</label>
                    <select id="tipo_aplicacion" name="tipo_aplicacion" class="w-full bg-slate-900 border border-slate-300 rounded-xl p-3 text-white focus:border-blue-500 transition-all outline-none cursor-pointer">
                        <option value="todo" {{ $offer->tipo_aplicacion == 'todo' ? 'selected' : '' }}>{{ __('admin/offers/offers.apply_all') }}</option>
                        <option value="categoria" {{ $offer->tipo_aplicacion == 'categoria' ? 'selected' : '' }}>{{ __('admin/offers/offers.apply_category') }}</option>
                        <option value="producto" {{ $offer->tipo_aplicacion == 'producto' ? 'selected' : '' }}>{{ __('admin/offers/offers.apply_product') }}</option>
                    </select>
                </div>

                <div id="div_categoria" class="hidden transition-all duration-300">
                    {{-- Agregado for para referencia_categoria --}}
                    <label for="referencia_categoria" class="block text-xs font-bold text-blue-400 uppercase tracking-wider mb-2">{{ __('admin/offers/offers.select_category') }}</label>
                    
                    @php
                        $categoriasBd = \App\Models\Categoria::all();
                    @endphp

                    <select id="referencia_categoria" name="referencia_categoria" class="w-full bg-slate-900 border border-blue-500/50 rounded-xl p-3 text-white focus:border-blue-500 outline-none">
                        <option value="">{{ __('admin/offers/offers.choose_category') }}</option>
                        @foreach($categoriasBd as $cat)
                            @php
                                $nombreMostrar = $cat->nombre;
                                if (app()->getLocale() == 'en' && !empty($cat->nombre_en)) $nombreMostrar = $cat->nombre_en;
                                if (app()->getLocale() == 'pt' && !empty($cat->nombre_pt)) $nombreMostrar = $cat->nombre_pt;
                            @endphp
                            <option value="{{ $cat->nombre }}" {{ ($offer->tipo_aplicacion == 'categoria' && $offer->referencia == $cat->nombre) ? 'selected' : '' }}>{{ $nombreMostrar }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="div_producto" class="hidden transition-all duration-300">
                    {{-- Agregado for e id para el botón de selección de producto --}}
                    <label for="btn_producto_seleccion" class="block text-xs font-bold text-blue-400 uppercase tracking-wider mb-2">{{ __('admin/offers/offers.select_product') }}</label>
                    
                    <input type="hidden" id="referencia_producto" name="referencia_producto" value="{{ $offer->tipo_aplicacion == 'producto' ? $offer->referencia : '' }}">
                    
                    <button type="button" id="btn_producto_seleccion" onclick="window.abrirModalProductos()" class="w-full bg-slate-900 border border-blue-500/50 hover:bg-slate-800 rounded-xl p-3 text-white focus:border-blue-500 outline-none flex justify-between items-center transition-colors">
                        <span id="texto_producto_seleccionado" class="{{ $productoSeleccionado ? 'text-white' : 'text-slate-400' }}">
                            @if($productoSeleccionado)
                                <span class="text-orange-400 font-bold"><i class="fas fa-check mr-1"></i> {{ $productoSeleccionado->nombre_traducido }}</span>
                            @else
                                {{ __('admin/offers/offers.click_choose_product') }}
                            @endif
                        </span>
                        <i class="fas fa-search text-blue-500 pointer-events-none"></i>
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    {{-- Agregado for e id --}}
                    <label for="porcentaje" class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">{{ __('admin/offers/offers.discount_percent') }}</label>
                    <div class="relative">
                        <input type="number" id="porcentaje" name="porcentaje" value="{{ old('porcentaje', $offer->porcentaje) }}" min="0" max="100" class="w-full bg-slate-900 border border-slate-300 rounded-xl p-3 pl-10 text-white focus:border-blue-500 outline-none">
                        <i class="fas fa-percentage absolute left-4 top-3.5 text-slate-500 pointer-events-none"></i>
                    </div>
                </div>
                <div>
                    {{-- Agregado for e id --}}
                    <label for="precio_promo" class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">{{ __('admin/offers/offers.fixed_price') }}</label>
                    <div class="relative">
                        <input type="number" id="precio_promo" step="0.50" name="precio_promo" value="{{ old('precio_promo', $offer->precio_promo) }}" placeholder="{{ __('admin/offers/offers.optional') }}" class="w-full bg-slate-900 border border-slate-300 rounded-xl p-3 pl-10 text-white focus:border-blue-500 outline-none">
                        <i class="fas fa-dollar-sign absolute left-4 top-3.5 text-slate-500 pointer-events-none"></i>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    {{-- Agregado for e id --}}
                    <label for="fecha_inicio" class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">{{ __('admin/offers/offers.start_date') }}</label>
                    <input type="text" id="fecha_inicio" name="fecha_inicio" value="{{ old('fecha_inicio', \Carbon\Carbon::parse($offer->fecha_inicio)->format('Y-m-d')) }}" class="flatpickr-date w-full bg-slate-900 border border-slate-300 rounded-xl p-3 text-white focus:border-blue-500 outline-none" required>
                </div>
                <div>
                    {{-- Agregado for e id --}}
                    <label for="fecha_fin" class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">{{ __('admin/offers/offers.end_date') }}</label>
                    <input type="text" id="fecha_fin" name="fecha_fin" value="{{ old('fecha_fin', \Carbon\Carbon::parse($offer->fecha_fin)->format('Y-m-d')) }}" class="flatpickr-date w-full bg-slate-900 border border-slate-300 rounded-xl p-3 text-white focus:border-blue-500 outline-none" required>
                </div>
            </div>

            <div class="p-4 bg-slate-900/50 rounded-xl border border-slate-300/50 flex items-center gap-6">
                @if($offer->imagen_url)
                    {{-- Agregado ALT a la imagen principal --}}
                    <img src="{{ asset('storage/' . $offer->imagen_url) }}" alt="Banner actual de la oferta" class="w-24 h-24 object-cover rounded-lg border border-slate-600 shadow-md">
                @else
                    <div class="w-24 h-24 bg-slate-800 rounded-lg border border-slate-300 flex items-center justify-center text-slate-600">
                        <i class="fas fa-image text-2xl"></i>
                    </div>
                @endif
                <div class="flex-1">
                    {{-- Agregado for e id --}}
                    <label for="imagen" class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">{{ __('admin/offers/offers.change_banner') }}</label>
                    <input type="file" id="imagen" name="imagen" class="w-full text-sm text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-700 file:text-white hover:file:bg-slate-600 transition-colors cursor-pointer border border-slate-300 rounded-xl bg-slate-900">
                    <p class="text-[10px] text-slate-500 mt-2">{{ __('admin/offers/offers.banner_hint') }}</p>
                </div>
            </div>

            <button type="submit" id="btn-update-offer" class="w-full bg-blue-600 hover:bg-blue-500 text-white font-black py-4 rounded-xl transition-all shadow-[0_0_15px_rgba(59,130,246,0.3)] hover:shadow-[0_0_25px_rgba(59,130,246,0.5)] transform hover:-translate-y-0.5">
                <i class="fas fa-save mr-2 pointer-events-none"></i> {{ __('admin/offers/offers.btn_save') }}
            </button>
        </form>
    </div>
</div>

{{-- MODAL INTERACTIVO DE PRODUCTOS --}}
<div id="modal-productos" class="fixed inset-0 z-[200] hidden flex items-center justify-center bg-black/80 backdrop-blur-sm transition-opacity opacity-0">
    <div id="modal-productos-panel" class="bg-slate-900 border border-slate-300 p-6 rounded-3xl shadow-2xl w-full max-w-3xl max-h-[90vh] flex flex-col transform scale-95 transition-all">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-black text-white"><i class="fas fa-hamburger text-blue-500 mr-2"></i> {{ __('admin/offers/offers.modal_prod_title') }}</h3>
            {{-- Agregado aria-label al botón de cerrar --}}
            <button aria-label="Cerrar selección de productos" type="button" onclick="window.cerrarModalProductos()" class="text-slate-400 hover:text-red-400 transition-colors bg-slate-800 hover:bg-slate-700 rounded-full w-8 h-8 flex items-center justify-center">
                <i class="fas fa-times pointer-events-none"></i>
            </button>
        </div>
        <div class="flex flex-col sm:flex-row gap-4 mb-6">
            <div class="relative flex-1">
                <i class="fas fa-search absolute left-4 top-3 text-slate-500"></i>
                {{-- Agregado aria-label --}}
                <input type="text" id="buscador_modal" aria-label="{{ __('admin/offers/offers.search_dish') }}" placeholder="{{ __('admin/offers/offers.search_dish') }}" class="w-full bg-slate-800 border border-slate-300 rounded-xl py-2.5 pl-11 pr-4 text-white focus:border-blue-500 outline-none text-sm placeholder-slate-500">
            </div>
            
            @php
                $categoriasBdModal = \App\Models\Categoria::all();
            @endphp
            
            {{-- Agregado aria-label --}}
            <select id="filtro_categoria_modal" aria-label="{{ __('admin/offers/offers.all_categories') }}" class="w-full sm:w-48 bg-slate-800 border border-slate-300 rounded-xl px-4 py-2.5 text-white focus:border-blue-500 outline-none text-sm cursor-pointer">
                <option value="">{{ __('admin/offers/offers.all_categories') }}</option>
                @foreach($categoriasBdModal as $cat)
                    @php
                        $nombreMostrar = $cat->nombre;
                        if (app()->getLocale() == 'en' && !empty($cat->nombre_en)) $nombreMostrar = $cat->nombre_en;
                        if (app()->getLocale() == 'pt' && !empty($cat->nombre_pt)) $nombreMostrar = $cat->nombre_pt;
                    @endphp
                    <option value="{{ $cat->nombre }}">{{ $nombreMostrar }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex-1 overflow-y-auto pr-2 custom-scrollbar">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3" id="grid_productos_modal">
                @foreach($productos as $prod)
                    <div class="producto-item bg-slate-800 border border-slate-300 rounded-xl p-3 flex items-center gap-4 cursor-pointer hover:border-blue-500 hover:bg-slate-750 transition-colors group"
                         data-id="{{ $prod->id_producto }}" data-nombre="{{ strtolower($prod->nombre_traducido) }}" data-categoria="{{ $prod->categoria }}"
                         onclick="window.seleccionarProducto({{ $prod->id_producto }}, '{{ addslashes($prod->nombre_traducido) }}')">
                         
                        @if($prod->imagen_url)
                            {{-- Agregado ALT a las imágenes de los productos --}}
                            <img src="{{ asset('imagenes/' . $prod->imagen_url) }}" alt="{{ $prod->nombre_traducido }}" class="w-14 h-14 rounded-lg object-cover border border-slate-600 group-hover:border-blue-500/50">
                        @else
                            <div class="w-14 h-14 bg-slate-900 rounded-lg flex items-center justify-center border border-slate-300"><i class="fas fa-image text-slate-600"></i></div>
                        @endif
                        
                        <div class="flex-1">
                            <h4 class="text-white font-bold text-sm">{{ $prod->nombre_traducido }}</h4>
                            <p class="text-xs text-slate-400 mt-0.5">
                                <span class="bg-slate-900 px-2 py-0.5 rounded text-[10px]">{{ $prod->categoria_traducida }}</span> 
                                <span class="text-emerald-400 font-mono ml-1">{{ formatCurrency($prod->precio) }}</span>
                            </p>
                        </div>
                        
                        <div class="text-slate-600 group-hover:text-blue-500 transition-colors">
                            <i class="fas fa-chevron-right text-xs pointer-events-none"></i>
                        </div>
                    </div>
                @endforeach
            </div>
            <div id="no_results_modal" class="hidden text-center py-12 text-slate-500">
                <div class="w-16 h-16 bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-search text-2xl opacity-50"></i>
                </div>
                <p class="font-bold text-white">{{ __('admin/offers/offers.no_results_title') }}</p>
                <p class="text-sm mt-1">{{ __('admin/offers/offers.no_results_desc') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/dark.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        window.OFFERS_LANG = {
            csrf_error: `{{ __('admin/offers/offers.js_csrf_error') }}`,
            server_error: `{{ __('admin/offers/offers.js_server_error') }}`,
            conn_error: `{{ __('admin/offers/offers.js_conn_error') }}`
        };
        
        document.addEventListener('DOMContentLoaded', () => {
            const selectAplicacion = document.getElementById('tipo_aplicacion');
            if(selectAplicacion) {
                const event = new Event('change');
                selectAplicacion.dispatchEvent(event);
            }

            // INICIALIZACIÓN DE FLAT PICKR
            const idiomaActual = '{{ app()->getLocale() }}';
            const formatoVisual = idiomaActual === 'en' ? 'm/d/Y' : 'd/m/Y';

            flatpickr(".flatpickr-date", {
                dateFormat: "Y-m-d", 
                altInput: true,      
                altFormat: formatoVisual, 
                theme: "dark"
            });
        });
    </script>
    @vite(['resources/js/admin/offers.js'])
@endpush