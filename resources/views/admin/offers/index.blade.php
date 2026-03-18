@extends('layouts.admin')

@section('titulo', __('admin/offers/offers.title_index'))

@section('contenido')
<div class="relative space-y-6 py-4">

    @if(session('success') || session('error') || $errors->any())
        <div id="toast-notification" class="fixed bottom-5 right-5 z-[200] flex flex-col gap-3 shadow-2xl transform transition-all duration-500 translate-y-0 opacity-100">
            
            {{-- ÉXITO --}}
            @if(session('success'))
                <div class="bg-emerald-950/90 border border-emerald-500 text-emerald-400 px-6 py-4 rounded-2xl flex items-center gap-4 backdrop-blur-md shadow-[0_0_20px_rgba(16,185,129,0.2)]">
                    <i class="fas fa-check-circle text-2xl animate-bounce"></i>
                    <div>
                        <p class="font-bold text-lg leading-tight">{{ __('admin/offers/offers.toast_success') }}</p>
                        <p class="text-xs text-emerald-300 mt-1 max-w-xs">{{ session('success') }}</p>
                    </div>
                    <button aria-label="Cerrar notificación" onclick="this.closest('.bg-emerald-950\\/90').remove()" class="ml-2 text-emerald-600 hover:text-emerald-400 transition">
                        <i class="fas fa-times pointer-events-none"></i>
                    </button>
                </div>
            @endif

            {{-- ERROR DE SESIÓN --}}
            @if(session('error'))
                <div class="bg-red-950/90 border border-red-500 text-red-400 px-6 py-4 rounded-2xl flex items-center gap-4 backdrop-blur-md shadow-[0_0_20px_rgba(239,68,68,0.2)]">
                    <i class="fas fa-shield-alt text-2xl animate-pulse"></i>
                    <div>
                        <p class="font-bold text-lg leading-tight">{{ __('admin/offers/offers.toast_error') }}</p>
                        <p class="text-xs text-red-300 mt-1 max-w-xs">{{ session('error') }}</p>
                    </div>
                    <button aria-label="Cerrar notificación" onclick="this.closest('.bg-red-950\\/90').remove()" class="ml-2 text-red-600 hover:text-red-400 transition">
                        <i class="fas fa-times pointer-events-none"></i>
                    </button>
                </div>
            @endif

            {{-- NUEVO: ERRORES DE VALIDACIÓN (Como el unique del título) --}}
            @if($errors->any())
                <div class="bg-red-950/90 border border-red-500 text-red-400 px-6 py-4 rounded-2xl flex items-center gap-4 backdrop-blur-md shadow-[0_0_20px_rgba(239,68,68,0.2)]">
                    <i class="fas fa-exclamation-triangle text-2xl animate-pulse"></i>
                    <div>
                        <p class="font-bold text-lg leading-tight">{{ __('admin/offers/offers.toast_error') }}</p>
                        <ul class="text-xs text-red-300 mt-1 max-w-xs list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <button aria-label="Cerrar notificación" onclick="this.closest('.bg-red-950\\/90').remove()" class="ml-2 text-red-600 hover:text-red-400 transition">
                        <i class="fas fa-times pointer-events-none"></i>
                    </button>
                </div>
            @endif
        </div>
        
        <script>
            setTimeout(() => {
                const toast = document.getElementById('toast-notification');
                if(toast) {
                    toast.classList.add('translate-y-10', 'opacity-0');
                    setTimeout(() => toast.remove(), 500);
                }
            }, 6000); // 6 Segundos
        </script>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
        
        <div class="xl:col-span-1">
            <div class="bg-slate-800 p-6 rounded-2xl border border-slate-300 shadow-xl sticky top-24">
                <h2 class="text-xl font-black text-white mb-6 flex items-center gap-3">
                    <div class="bg-orange-500/20 p-3 rounded-xl border border-orange-500/30">
                        <i class="fas fa-fire text-orange-500"></i>
                    </div>
                    {{ __('admin/offers/offers.create_promo') }}
                </h2>

                <form id="form-create-offer" action="{{ route('admin.offers.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                    @csrf
                    
                    <div class="space-y-3">
                        <div>
                            <label for="titulo" class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">🇲🇽 {{ __('admin/offers/offers.promo_title') }} *</label>
                            <input type="text" id="titulo" name="titulo" placeholder="{{ __('admin/offers/offers.promo_title_ph') }}" class="w-full bg-slate-900 border border-slate-300 rounded-xl p-3 text-white focus:border-orange-500 focus:ring-1 focus:ring-orange-500 transition-all outline-none" required>
                        </div>

                        <div>
                            <label for="descripcion" class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">🇲🇽 {{ __('admin/offers/offers.short_desc') }}</label>
                            <textarea id="descripcion" name="descripcion" rows="2" class="w-full bg-slate-900 border border-slate-300 rounded-xl p-3 text-white focus:border-orange-500 transition-all outline-none"></textarea>
                        </div>
                    </div>

                    {{-- TRADUCCIONES OCULTAS --}}
                    <details class="bg-slate-900/50 rounded-xl border border-slate-300/50 group">
                        <summary class="p-3 text-xs font-bold text-slate-400 uppercase tracking-wider cursor-pointer flex justify-between items-center outline-none">
                            <span><i class="fas fa-language text-blue-400 mr-2"></i> {{ __('admin/offers/offers.add_translations') }}</span>
                            <i class="fas fa-chevron-down group-open:rotate-180 transition-transform"></i>
                        </summary>
                        <div class="p-4 pt-2 space-y-4 border-t border-slate-300/50 mt-2">
                            <div class="space-y-2 border-l-2 border-blue-500 pl-3">
                                <input type="text" aria-label="Título de promoción en Inglés" name="titulo_en" value="{{ old('titulo_en', $offer->titulo_en ?? '') }}" placeholder="🇺🇸 {{ __('admin/offers/offers.title_en') }}" class="w-full bg-slate-900 border border-slate-300 rounded-lg p-2.5 text-white text-sm focus:border-blue-500 outline-none">
                                <textarea aria-label="Descripción de promoción en Inglés" name="descripcion_en" rows="1" placeholder="🇺🇸 {{ __('admin/offers/offers.desc_en') }}" class="w-full bg-slate-900 border border-slate-300 rounded-lg p-2.5 text-white text-sm focus:border-blue-500 outline-none">{{ old('descripcion_en', $offer->descripcion_en ?? '') }}</textarea>
                            </div>
                            <div class="space-y-2 border-l-2 border-emerald-500 pl-3">
                                <input type="text" aria-label="Título de promoción en Portugués" name="titulo_pt" value="{{ old('titulo_pt', $offer->titulo_pt ?? '') }}" placeholder="🇧🇷 {{ __('admin/offers/offers.title_pt') }}" class="w-full bg-slate-900 border border-slate-300 rounded-lg p-2.5 text-white text-sm focus:border-emerald-500 outline-none">
                                <textarea aria-label="Descripción de promoción en Portugués" name="descripcion_pt" rows="1" placeholder="🇧🇷 {{ __('admin/offers/offers.desc_pt') }}" class="w-full bg-slate-900 border border-slate-300 rounded-lg p-2.5 text-white text-sm focus:border-emerald-500 outline-none">{{ old('descripcion_pt', $offer->descripcion_pt ?? '') }}</textarea>
                            </div>
                        </div>
                    </details>

                    <div class="bg-slate-900/50 p-4 rounded-xl border border-slate-300/50 space-y-4">
                        <div>
                            <label for="tipo_aplicacion" class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">{{ __('admin/offers/offers.applies_to') }}</label>
                            <select id="tipo_aplicacion" name="tipo_aplicacion" class="w-full bg-slate-900 border border-slate-300 rounded-xl p-3 text-white focus:border-orange-500 transition-all outline-none cursor-pointer">
                                <option value="todo">{{ __('admin/offers/offers.apply_all') }}</option>
                                <option value="categoria">{{ __('admin/offers/offers.apply_category') }}</option>
                                <option value="producto">{{ __('admin/offers/offers.apply_product') }}</option>
                            </select>
                        </div>

                        <div id="div_categoria" class="hidden transition-all duration-300">
                            <label for="referencia_categoria" class="block text-xs font-bold text-orange-400 uppercase tracking-wider mb-2">{{ __('admin/offers/offers.select_category') }}</label>
                            
                            @php
                                $categoriasBd = \App\Models\Categoria::all();
                            @endphp

                            <select id="referencia_categoria" name="referencia_categoria" class="w-full bg-slate-900 border border-orange-500/50 rounded-xl p-3 text-white focus:border-orange-500 outline-none">
                                <option value="">{{ __('admin/offers/offers.choose_category') }}</option>
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

                        <div id="div_producto" class="hidden transition-all duration-300">
                            <label for="btn_producto_seleccion" class="block text-xs font-bold text-orange-400 uppercase tracking-wider mb-2">{{ __('admin/offers/offers.select_product') }}</label>
                            
                            <input type="hidden" id="referencia_producto" name="referencia_producto">
                            
                            <button type="button" id="btn_producto_seleccion" onclick="window.abrirModalProductos()" class="w-full bg-slate-900 border border-orange-500/50 hover:bg-slate-800 rounded-xl p-3 text-white focus:border-orange-500 outline-none flex justify-between items-center transition-colors">
                                <span id="texto_producto_seleccionado" class="text-slate-400">{{ __('admin/offers/offers.click_choose_product') }}</span>
                                <i class="fas fa-search text-orange-500 pointer-events-none"></i>
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="porcentaje" class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">{{ __('admin/offers/offers.discount_percent') }}</label>
                            <div class="relative">
                                <input type="number" id="porcentaje" name="porcentaje" value="0" min="0" max="100" class="w-full bg-slate-900 border border-slate-300 rounded-xl p-3 pl-10 text-white focus:border-orange-500 outline-none">
                                <i class="fas fa-percentage absolute left-4 top-3.5 text-slate-500 pointer-events-none"></i>
                            </div>
                        </div>
                        <div>
                            <label for="precio_promo" class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">{{ __('admin/offers/offers.fixed_price') }}</label>
                            <div class="relative">
                                <input type="number" id="precio_promo" step="0.50" name="precio_promo" placeholder="{{ __('admin/offers/offers.optional') }}" class="w-full bg-slate-900 border border-slate-300 rounded-xl p-3 pl-10 text-white focus:border-orange-500 outline-none">
                                <i class="fas fa-dollar-sign absolute left-4 top-3.5 text-slate-500 pointer-events-none"></i>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="fecha_inicio" class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">{{ __('admin/offers/offers.start_date') }}</label>
                            <input type="text" id="fecha_inicio" name="fecha_inicio" value="{{ date('Y-m-d') }}" class="flatpickr-date w-full bg-slate-900 border border-slate-300 rounded-xl p-3 text-white focus:border-orange-500 outline-none text-sm" required>
                        </div>
                        <div>
                            <label for="fecha_fin" class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">{{ __('admin/offers/offers.end_date') }}</label>
                            <input type="text" id="fecha_fin" name="fecha_fin" class="flatpickr-date w-full bg-slate-900 border border-slate-300 rounded-xl p-3 text-white focus:border-orange-500 outline-none text-sm" required>
                        </div>
                    </div>

                    <div>
                        <label for="imagen" class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">{{ __('admin/offers/offers.visual_banner') }}</label>
                        <input type="file" id="imagen" name="imagen" class="w-full text-sm text-slate-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-700 file:text-white hover:file:bg-slate-600 transition-colors cursor-pointer border border-slate-300 rounded-xl bg-slate-900">
                    </div>

                    <button type="submit" id="btn-submit-offer" class="w-full bg-orange-700 hover:bg-orange-500 text-white font-black py-3.5 rounded-xl transition-all shadow-[0_0_15px_rgba(234,88,12,0.3)] hover:shadow-[0_0_25px_rgba(234,88,12,0.5)] transform hover:-translate-y-0.5">
                        <i class="fas fa-rocket mr-2 pointer-events-none"></i> {{ __('admin/offers/offers.btn_launch') }}
                    </button>
                </form>
            </div>
        </div>

        <div class="xl:col-span-2">
            <h2 class="text-2xl font-black text-white mb-6">{{ __('admin/offers/offers.existing_promos') }}</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @forelse($offers as $offer)
                    @php
                        $isExpired = \Carbon\Carbon::parse($offer->fecha_fin)->isPast() && !\Carbon\Carbon::parse($offer->fecha_fin)->isToday();
                        $isScheduled = \Carbon\Carbon::parse($offer->fecha_inicio)->isFuture();

                        // Traducir el nombre de la categoría en la etiqueta si aplica
                        $badgeRef = $offer->referencia;
                        if ($offer->tipo_aplicacion == 'categoria') {
                            $catModel = \App\Models\Categoria::where('nombre', $offer->referencia)->first();
                            if ($catModel) {
                                if (app()->getLocale() == 'en' && !empty($catModel->nombre_en)) $badgeRef = $catModel->nombre_en;
                                if (app()->getLocale() == 'pt' && !empty($catModel->nombre_pt)) $badgeRef = $catModel->nombre_pt;
                            }
                        }
                    @endphp

                    <div class="bg-slate-800 rounded-2xl overflow-hidden border {{ $isExpired ? 'border-red-500/30 opacity-75' : 'border-slate-300' }} shadow-lg flex flex-col transition-all hover:border-orange-500/50">
                        
                        <div class="h-32 bg-slate-900 relative overflow-hidden group">
                            @if($offer->imagen_url)
                                <img src="{{ asset('storage/' . $offer->imagen_url) }}" alt="{{ $offer->titulo_traducido }}" class="w-full h-full object-cover opacity-80 group-hover:scale-105 transition-transform duration-500">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-slate-900 text-slate-700">
                                    <i class="fas fa-tags text-5xl"></i>
                                </div>
                            @endif

                            <div class="absolute top-3 left-3 flex gap-2">
                                @if($offer->tipo_aplicacion == 'categoria')
                                    <span class="bg-purple-600/90 backdrop-blur text-white text-[10px] font-black px-2.5 py-1 rounded-lg uppercase tracking-wider shadow-lg border border-purple-400/30">
                                        <i class="fas fa-layer-group mr-1"></i> {{ __('admin/offers/offers.badge_cat') }} {{ $badgeRef }}
                                    </span>
                                @elseif($offer->tipo_aplicacion == 'producto')
                                    <span class="bg-blue-600/90 backdrop-blur text-white text-[10px] font-black px-2.5 py-1 rounded-lg uppercase tracking-wider shadow-lg border border-blue-400/30">
                                        <i class="fas fa-hamburger mr-1"></i> {{ __('admin/offers/offers.badge_prod_id') }} {{ $offer->referencia }}
                                    </span>
                                @else
                                    <span class="bg-emerald-600/90 backdrop-blur text-white text-[10px] font-black px-2.5 py-1 rounded-lg uppercase tracking-wider shadow-lg border border-emerald-400/30">
                                        <i class="fas fa-globe mr-1"></i> {{ __('admin/offers/offers.badge_all_menu') }}
                                    </span>
                                @endif
                            </div>

                            @if($offer->porcentaje > 0)
                                <div class="absolute top-3 right-3 bg-red-600 text-white font-black px-3 py-1 rounded-xl shadow-lg transform rotate-3 border border-red-400/30">
                                    -{{ $offer->porcentaje }}%
                                </div>
                            @elseif($offer->precio_promo > 0)
                                <div class="absolute top-3 right-3 bg-green-600 text-white font-black px-3 py-1 rounded-xl shadow-lg transform -rotate-3 border border-green-400/30">
                                    {{ formatCurrency($offer->precio_promo) }}
                                </div>
                            @endif
                        </div>

                        <div class="p-5 flex-1 flex flex-col">
                            <div class="flex justify-between items-start mb-2 gap-4">
                                <h3 class="text-lg font-bold text-white leading-tight">{{ $offer->titulo_traducido }}</h3>
                                
                                <button aria-label="Activar o desactivar oferta" type="button" onclick="window.toggleOferta({{ $offer->id }}, this, '{{ route('admin.offers.toggle', $offer->id) }}')" 
                                    class="shrink-0 flex items-center justify-center w-12 h-6 rounded-full transition-colors duration-300 focus:outline-none {{ $offer->activa ? 'bg-emerald-500' : 'bg-slate-600' }}"
                                    title="Activar / Desactivar">
                                    <span class="w-4 h-4 rounded-full bg-white shadow transform transition-transform duration-300 {{ $offer->activa ? 'translate-x-2.5' : '-translate-x-2.5' }}"></span>
                                </button>
                            </div>

                            <div class="mb-3">
                                @if($isExpired)
                                    <span class="text-red-400 text-xs font-bold uppercase"><i class="fas fa-clock mr-1"></i> {{ __('admin/offers/offers.status_expired') }}</span>
                                @elseif($isScheduled)
                                    <span class="text-amber-400 text-xs font-bold uppercase"><i class="fas fa-calendar-day mr-1"></i> {{ __('admin/offers/offers.status_scheduled') }}</span>
                                @else
                                    <span class="text-emerald-400 text-xs font-bold uppercase"><i class="fas fa-broadcast-tower mr-1"></i> {{ __('admin/offers/offers.status_active') }}</span>
                                @endif
                            </div>

                            <div class="flex items-center gap-3 text-xs font-mono text-slate-400 mt-auto pt-4 border-t border-slate-300/50">
                                @php
                                    $formatoFecha = app()->getLocale() == 'en' ? 'M d, Y' : 'd M, Y';
                                @endphp
                                
                                <span title="Inicio"><i class="far fa-calendar-alt text-slate-500 mr-1"></i> {{ \Carbon\Carbon::parse($offer->fecha_inicio)->translatedFormat($formatoFecha) }}</span>
                                <i class="fas fa-long-arrow-alt-right text-slate-600"></i>
                                <span title="Fin" class="{{ $isExpired ? 'text-red-400 font-bold' : '' }}">
                                    <i class="far fa-calendar-times text-slate-500 mr-1"></i> {{ \Carbon\Carbon::parse($offer->fecha_fin)->translatedFormat($formatoFecha) }}
                                </span>
                            </div>

                            <div class="mt-4 pt-4 border-t border-slate-300/50 flex gap-2">
                                
                                <a href="{{ route('admin.offers.edit', $offer->id) }}" class="flex-1 py-2 rounded-xl border border-blue-500/20 text-blue-400 hover:bg-blue-500/10 hover:border-blue-500/50 transition-colors text-sm font-bold flex items-center justify-center gap-2">
                                    <i class="fas fa-edit"></i> {{ __('admin/offers/offers.btn_edit') }}
                                </a>

                                <form action="{{ route('admin.offers.destroy', $offer->id) }}" method="POST" id="form-delete-offer-{{ $offer->id }}" class="flex-1">
                                    @csrf @method('DELETE')
                                    <button type="button" onclick="window.confirmarBorradoOferta('form-delete-offer-{{ $offer->id }}')" class="w-full py-2 rounded-xl border border-red-500/20 text-red-400 hover:bg-red-500/10 hover:border-red-500/50 transition-colors text-sm font-bold flex items-center justify-center gap-2">
                                        <i class="fas fa-trash-alt pointer-events-none"></i> {{ __('admin/offers/offers.btn_delete') }}
                                    </button>
                                </form>

                            </div>
                        </div>
                    </div>
                @empty
                    <div class="md:col-span-2 py-16 text-center border-2 border-dashed border-slate-300 rounded-3xl">
                        <i class="fas fa-ticket-alt text-5xl text-slate-600 mb-4"></i>
                        <h3 class="text-xl font-bold text-white mb-2">{{ __('admin/offers/offers.no_active_offers') }}</h3>
                        <p class="text-slate-400">{{ __('admin/offers/offers.no_offers_desc') }}</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- MODAL INTERACTIVO DE PRODUCTOS --}}
<div id="modal-productos" class="fixed inset-0 z-[200] hidden flex items-center justify-center bg-black/80 backdrop-blur-sm transition-opacity opacity-0">
    <div id="modal-productos-panel" class="bg-slate-900 border border-slate-300 p-6 rounded-3xl shadow-2xl w-full max-w-3xl max-h-[90vh] flex flex-col transform scale-95 transition-all">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-black text-white"><i class="fas fa-hamburger text-orange-500 mr-2"></i> {{ __('admin/offers/offers.modal_prod_title') }}</h3>
            <button aria-label="Cerrar selección de productos" type="button" onclick="window.cerrarModalProductos()" class="text-slate-400 hover:text-red-400 transition-colors bg-slate-800 hover:bg-slate-700 rounded-full w-8 h-8 flex items-center justify-center">
                <i class="fas fa-times pointer-events-none"></i>
            </button>
        </div>
        <div class="flex flex-col sm:flex-row gap-4 mb-6">
            <div class="relative flex-1">
                <i class="fas fa-search absolute left-4 top-3 text-slate-500"></i>
                <input type="text" id="buscador_modal" aria-label="{{ __('admin/offers/offers.search_dish') }}" placeholder="{{ __('admin/offers/offers.search_dish') }}" class="w-full bg-slate-800 border border-slate-300 rounded-xl py-2.5 pl-11 pr-4 text-white focus:border-orange-500 outline-none text-sm placeholder-slate-500">
            </div>
            <select id="filtro_categoria_modal" aria-label="{{ __('admin/offers/offers.all_categories') }}" class="w-full sm:w-48 bg-slate-800 border border-slate-300 rounded-xl px-4 py-2.5 text-white focus:border-orange-500 outline-none text-sm cursor-pointer">
                <option value="">{{ __('admin/offers/offers.all_categories') }}</option>
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
        <div class="flex-1 overflow-y-auto pr-2 custom-scrollbar">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3" id="grid_productos_modal">
                @foreach($productos as $prod)
                    <div class="producto-item bg-slate-800 border border-slate-300 rounded-xl p-3 flex items-center gap-4 cursor-pointer hover:border-orange-500 hover:bg-slate-750 transition-colors group"
                         data-id="{{ $prod->id_producto }}" data-nombre="{{ strtolower($prod->nombre_traducido) }}" data-categoria="{{ $prod->categoria }}"
                         onclick="window.seleccionarProducto({{ $prod->id_producto }}, '{{ addslashes($prod->nombre_traducido) }}')">
                         
                        @if($prod->imagen_url)
                            <img src="{{ asset('imagenes/' . $prod->imagen_url) }}" alt="{{ $prod->nombre_traducido }}" class="w-14 h-14 rounded-lg object-cover border border-slate-600 group-hover:border-orange-500/50">
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
                        
                        <div class="text-slate-600 group-hover:text-orange-500 transition-colors">
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

{{-- MODAL DE ELIMINACIÓN DE OFERTA --}}
<div id="delete-modal-offer" class="fixed inset-0 z-[300] hidden flex items-center justify-center bg-black/80 backdrop-blur-sm transition-opacity opacity-0">
    <div id="delete-modal-offer-panel" class="bg-slate-900 border border-slate-300 p-6 rounded-3xl shadow-2xl w-full max-w-sm transform scale-95 transition-all text-center">
        
        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full mb-4 bg-red-500/20 text-red-500 border border-red-500/50">
            <i class="fas fa-bomb text-2xl animate-pulse"></i>
        </div>
        
        <h3 class="text-xl font-black text-white mb-2">{{ __('admin/offers/offers.modal_del_title') }}</h3>
        <p class="text-xs text-slate-400 mb-6 px-2">{{ __('admin/offers/offers.modal_del_desc') }}</p>
        
        <div class="flex gap-3">
            <button onclick="window.cerrarModalBorradoOferta()" class="flex-1 bg-slate-800 hover:bg-slate-700 text-white font-bold py-3 rounded-xl transition-all border border-slate-600 text-sm">
                {{ __('admin/offers/offers.btn_cancel') }}
            </button>
            <button id="modal-confirm-offer-btn" class="flex-1 bg-red-600 hover:bg-red-500 text-white font-bold py-3 rounded-xl shadow-lg transition-all shadow-red-900/50 text-sm">
                {{ __('admin/offers/offers.btn_confirm_delete') }}
            </button>
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