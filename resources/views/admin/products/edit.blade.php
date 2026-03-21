@extends('layouts.admin')

@section('titulo', __('admin/products/products.title_edit'))

@section('contenido')
<div class="max-w-6xl mx-auto py-8">
    
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-white">
            <i class="fas fa-edit text-blue-500 mr-2"></i> {{ __('admin/products/products.title_edit') }}
        </h1>
        <a href="{{ route('admin.products.index') }}" class="text-gray-400 hover:text-white transition flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> {{ __('admin/products/products.btn_cancel') }}
        </a>
    </div>

    <div class="bg-gray-800 rounded-2xl shadow-2xl border border-gray-700 p-8">
        <form action="{{ route('admin.products.update', $producto->id_producto) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf
            @method('PUT') 
            
            {{-- ZONA SUPERIOR: 2 COLUMNAS --}}
            <div class="grid grid-cols-1 xl:grid-cols-2 gap-8 xl:gap-12">
                
                {{-- COLUMNA IZQUIERDA: Datos Base y Español --}}
                <div class="space-y-6">
                    
                    {{-- BLOQUE: TEXTOS EN ESPAÑOL --}}
                    <div class="bg-gray-900/50 p-5 rounded-xl border border-gray-700 shadow-inner">
                        <div class="flex items-center gap-2 mb-4 pb-2 border-b border-gray-800">
                            <span class="text-2xl">🇲🇽</span>
                            <h3 class="text-white font-bold text-sm tracking-widest uppercase">Textos en Español</h3>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-300 mb-2">{{ __('admin/products/products.label_name') }} <span class="text-blue-500">*</span></label>
                                <input type="text" name="nombre" value="{{ old('nombre', $producto->nombre) }}" required
                                       class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg p-3 focus:outline-none focus:border-blue-500 transition">
                                @error('nombre') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-300 mb-2">{{ __('admin/products/products.label_desc') }}</label>
                                <textarea name="descripcion" rows="3"
                                          class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg p-3 focus:outline-none focus:border-blue-500 transition">{{ old('descripcion', $producto->descripcion) }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- BLOQUE: PRECIO Y CATEGORÍA --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-300 mb-2">{{ __('admin/products/products.label_price') }} <span class="text-blue-500">*</span></label>
                            <div class="relative">
                                <span class="absolute left-3 top-3 text-gray-500 font-bold">MX$</span>
                                <input type="number" id="precio_base" name="precio" step="0.50" value="{{ old('precio', $producto->precio) }}" required
                                       class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg p-3 pl-12 focus:outline-none focus:border-blue-500 transition font-mono">
                            </div>
                            
                            {{-- CONVERSOR DINÁMICO DE DIVISAS --}}
                            <div id="precio_convertido" class="text-[11px] text-blue-400 font-mono mt-2 ml-1 hidden flex flex-col gap-0.5">
                                <span><i class="fas fa-exchange-alt mr-1"></i> <span id="usd_preview">0.00</span> USD</span>
                                <span class="text-emerald-400"><i class="fas fa-exchange-alt mr-1"></i> <span id="brl_preview">0.00</span> BRL</span>
                            </div>

                            @error('precio') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-300 mb-2">{{ __('admin/products/products.label_category') }} <span class="text-blue-500">*</span></label>
                            
                            @php
                                $categoriasBd = \App\Models\Categoria::all();
                            @endphp
                            
                            <select name="categoria" required class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg p-3 focus:outline-none focus:border-blue-500 transition appearance-none cursor-pointer">
                                <option value="" disabled>{{ __('admin/products/products.ph_category') }}</option>
                                @foreach($categoriasBd as $cat)
                                    <option value="{{ $cat->nombre }}" {{ old('categoria', $producto->categoria) == $cat->nombre ? 'selected' : '' }}>
                                        {{ $cat->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('categoria') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- BLOQUE: IMAGEN GIGANTE --}}
                    <div class="flex flex-col">
                        <label class="block text-sm font-bold text-gray-300 mb-2">{{ __('admin/products/products.label_photo_optional') }}</label>
                        <div class="bg-gray-900 border-2 border-dashed border-gray-600 rounded-xl flex items-center justify-center relative overflow-hidden group hover:border-blue-500 transition h-72 shadow-inner" id="image-preview-container">
                            @if($producto->imagen_url)
                                <img id="preview-img" src="{{ asset('imagenes/' . $producto->imagen_url) }}" class="absolute inset-0 w-full h-full object-contain bg-black/50 backdrop-blur-sm p-2">
                                <div class="text-center p-6 hidden opacity-0 group-hover:opacity-100 transition-opacity bg-black/70 absolute inset-0 flex flex-col justify-center items-center" id="placeholder-text">
                            @else
                                <img id="preview-img" src="#" class="absolute inset-0 w-full h-full object-contain hidden bg-black/50 backdrop-blur-sm p-2">
                                <div class="text-center p-6 transition transform group-hover:scale-105" id="placeholder-text">
                            @endif
                                <i class="fas fa-cloud-upload-alt text-5xl text-gray-400 mb-3"></i>
                                <p class="text-white font-bold">{{ __('admin/products/products.change_image') }}</p>
                                <p class="text-gray-400 text-xs mt-2">{{ __('admin/products/products.keep_image_hint') }}</p>
                            </div>
                            <input type="file" name="imagen" id="imagen-input" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                        </div>
                        @error('imagen') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                </div>

                {{-- COLUMNA DERECHA: Traducciones --}}
                <div class="space-y-6">

                    {{-- BLOQUE: TEXTOS EN INGLÉS --}}
                    <div class="bg-blue-900/10 p-5 rounded-xl border border-blue-900/30">
                        <div class="flex items-center gap-2 mb-4 pb-2 border-b border-blue-900/50">
                            <span class="text-2xl">🇺🇸</span>
                            <h3 class="text-blue-300 font-bold text-sm tracking-widest uppercase">English Translation</h3>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-300 mb-2">Dish Name (English)</label>
                                <input type="text" name="nombre_en" value="{{ old('nombre_en', $producto->nombre_en) }}" placeholder="Ex: Double Monster Burger"
                                       class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg p-3 focus:outline-none focus:border-blue-500 transition">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-300 mb-2">Description (English)</label>
                                <textarea name="descripcion_en" rows="2" placeholder="Ingredients, details..."
                                          class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg p-3 focus:outline-none focus:border-blue-500 transition">{{ old('descripcion_en', $producto->descripcion_en) }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- BLOQUE: TEXTOS EN PORTUGUÉS (BRASIL) --}}
                    <div class="bg-emerald-900/10 p-5 rounded-xl border border-emerald-900/30">
                        <div class="flex items-center gap-2 mb-4 pb-2 border-b border-emerald-900/50">
                            <span class="text-2xl">🇧🇷</span>
                            <h3 class="text-emerald-400 font-bold text-sm tracking-widest uppercase">Tradução em Português</h3>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-300 mb-2">Nome do Prato (Português)</label>
                                <input type="text" name="nombre_pt" value="{{ old('nombre_pt', $producto->nombre_pt ?? '') }}" placeholder="Ex: Hambúrguer Monstro"
                                       class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg p-3 focus:outline-none focus:border-emerald-500 transition">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-300 mb-2">Descrição (Português)</label>
                                <textarea name="descripcion_pt" rows="2" placeholder="Ingredientes, detalhes..."
                                          class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg p-3 focus:outline-none focus:border-emerald-500 transition">{{ old('descripcion_pt', $producto->descripcion_pt ?? '') }}</textarea>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            {{-- ZONA INFERIOR: EXTRAS (Ancho Completo) --}}
            <div class="bg-gray-900/30 p-6 rounded-xl border border-gray-700 w-full mt-6">
                <label class="block text-lg font-bold text-white mb-4">
                    <i class="fas fa-list-ul text-blue-500 mr-2"></i> {{ __('admin/products/products.label_modifiers') }}
                </label>
                
                <div class="bg-gray-900 border border-gray-600 rounded-xl p-5 transition min-h-[50px]">
                    {{-- Contenedor de etiquetas creadas --}}
                    <div id="tags-container" class="flex flex-wrap gap-3 empty:hidden mb-6 border-b border-gray-700 pb-5"></div>
                    
                    {{-- Formulario para agregar un nuevo Extra --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 items-start">
                        <div class="lg:col-span-1">
                            <label class="text-[10px] text-gray-400 font-bold uppercase mb-1 block">🇲🇽 Español</label>
                            <input type="text" id="tag-name" placeholder="Ej: Con limón" class="w-full bg-gray-800 text-white border border-gray-700 rounded-lg p-2.5 text-sm outline-none focus:border-blue-500 transition">
                        </div>
                        <div class="lg:col-span-1">
                            <label class="text-[10px] text-gray-400 font-bold uppercase mb-1 block">🇺🇸 Inglés</label>
                            <input type="text" id="tag-name-en" placeholder="Ej: With lemon" class="w-full bg-gray-800 text-white border border-gray-700 rounded-lg p-2.5 text-sm outline-none focus:border-blue-500 transition">
                        </div>
                        <div class="lg:col-span-1">
                            <label class="text-[10px] text-gray-400 font-bold uppercase mb-1 block">🇧🇷 Portugués</label>
                            <input type="text" id="tag-name-pt" placeholder="Ej: Com limão" class="w-full bg-gray-800 text-white border border-gray-700 rounded-lg p-2.5 text-sm outline-none focus:border-emerald-500 transition">
                        </div>
                        <div class="lg:col-span-1">
                            <label class="text-[10px] text-gray-400 font-bold uppercase mb-1 block">Precio (MXN)</label>
                            <div class="relative">
                                <span class="absolute left-3 top-2.5 text-gray-400 text-sm">+$</span>
                                <input type="number" id="tag-price" placeholder="0.00" min="0" step="0.50" class="w-full bg-gray-800 text-white border border-gray-700 rounded-lg p-2.5 pl-8 text-sm outline-none focus:border-blue-500 transition font-mono">
                            </div>
                            
                            {{-- CONVERSOR PARA EL EXTRA --}}
                            <div id="tag_precio_convertido" class="text-[10px] text-blue-400 font-mono mt-1.5 ml-1 hidden flex-col gap-0.5">
                                <span><i class="fas fa-exchange-alt mr-1"></i> <span id="tag_usd_preview">0.00</span> USD</span>
                                <span class="text-emerald-400"><i class="fas fa-exchange-alt mr-1"></i> <span id="tag_brl_preview">0.00</span> BRL</span>
                            </div>
                        </div>
                        <div class="lg:col-span-1 flex items-end h-full">
                            <button type="button" id="btn-add-tag" class="w-full bg-blue-600 hover:bg-blue-500 text-white px-4 py-2.5 rounded-lg text-sm font-bold transition-colors flex items-center justify-center gap-2 mt-auto">
                                <i class="fas fa-plus"></i> Agregar
                            </button>
                        </div>
                    </div>
                </div>

                <input type="hidden" name="opciones_personalizacion" id="hidden-tags" 
                       value="{{ old('opciones_personalizacion', isset($producto) && $producto->opciones_personalizacion ? (is_string($producto->opciones_personalizacion) ? $producto->opciones_personalizacion : json_encode($producto->opciones_personalizacion)) : '[]') }}">
                <p class="text-gray-400 text-xs mt-3 leading-relaxed ml-2"><i class="fas fa-info-circle text-blue-400 mr-1"></i> {{ __('admin/products/products.modifier_hint') }}</p>
            </div>

            <div class="border-t border-gray-700 pt-6 mt-6">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-black py-4 rounded-xl shadow-lg transform transition hover:scale-[1.01] text-lg">
                    <i class="fas fa-sync-alt mr-2"></i> {{ __('admin/products/products.btn_update') }}
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // --- 1. LÓGICA DEL CONVERSOR DE PRECIOS EN TIEMPO REAL ---
        const EXCHANGE_RATES = {
            usd: {{ Cache::get('exchange_rate_usd', 20.00) }},
            brl: {{ Cache::get('exchange_rate_brl', 3.50) }}
        };

        // Conversor Principal
        const inputPrecio = document.getElementById('precio_base');
        const contenedorPreview = document.getElementById('precio_convertido');
        const usdPreview = document.getElementById('usd_preview');
        const brlPreview = document.getElementById('brl_preview');

        // Conversor de Modificadores (NUEVO)
        const tagPrice = document.getElementById('tag-price');
        const tagContenedorPreview = document.getElementById('tag_precio_convertido');
        const tagUsdPreview = document.getElementById('tag_usd_preview');
        const tagBrlPreview = document.getElementById('tag_brl_preview');

        function actualizarConversiones(inputElement, previewContainer, usdEl, brlEl) {
            const mxn = parseFloat(inputElement.value);
            if (!isNaN(mxn) && mxn > 0) {
                usdEl.innerText = '$' + (mxn / EXCHANGE_RATES.usd).toFixed(2);
                brlEl.innerText = 'R$ ' + (mxn / EXCHANGE_RATES.brl).toFixed(2);
                previewContainer.classList.remove('hidden');
                previewContainer.classList.add('flex');
            } else {
                previewContainer.classList.add('hidden');
                previewContainer.classList.remove('flex');
            }
        }

        inputPrecio.addEventListener('input', () => actualizarConversiones(inputPrecio, contenedorPreview, usdPreview, brlPreview));
        tagPrice.addEventListener('input', () => actualizarConversiones(tagPrice, tagContenedorPreview, tagUsdPreview, tagBrlPreview));
        actualizarConversiones(inputPrecio, contenedorPreview, usdPreview, brlPreview);


        // --- 2. LÓGICA DE ETIQUETAS Y MODIFICADORES INTERNACIONALES ---
        const tagName = document.getElementById('tag-name');
        const tagNameEn = document.getElementById('tag-name-en');
        const tagNamePt = document.getElementById('tag-name-pt');
        const btnAddTag = document.getElementById('btn-add-tag');
        const tagsContainer = document.getElementById('tags-container');
        const hiddenTags = document.getElementById('hidden-tags');
        const textGratis = `{{ __('admin/products/products.mod_free') }}`;

        let tags = [];
        try { tags = JSON.parse(hiddenTags.value || '[]'); } catch (e) { tags = []; }

        function renderTags() {
            tagsContainer.innerHTML = '';
            tags.forEach((tag, index) => {
                if (typeof tag === 'string') tag = { nombre: tag, precio: 0 };
                
                const badge = document.createElement('span');
                badge.className = 'bg-slate-800 text-white border border-slate-600 px-4 py-3 rounded-lg text-sm font-bold flex flex-col gap-1.5 shadow-md relative pr-10 min-w-[200px]';
                
                let precioTxt = tag.precio > 0 ? `<span class="text-emerald-400 text-base">+$${parseFloat(tag.precio).toFixed(2)} MXN</span>` : `<span class="text-slate-400 text-base">${textGratis}</span>`;
                
                badge.innerHTML = `
                    <div class="flex justify-between items-center w-full border-b border-slate-700 pb-1 mb-1">
                        <span class="text-orange-400">🇲🇽 ${tag.nombre}</span>
                    </div>
                    <span class="text-blue-300 font-normal text-xs">🇺🇸 ${tag.nombre_en || 'N/A'}</span>
                    <span class="text-emerald-300 font-normal text-xs">🇧🇷 ${tag.nombre_pt || 'N/A'}</span>
                    <div class="text-right mt-2 font-black">${precioTxt}</div>
                    <button type="button" class="absolute top-2 right-2 text-slate-500 hover:text-red-400 transition-colors w-6 h-6 flex items-center justify-center rounded bg-slate-900" onclick="window.removeTag(${index})">
                        <i class="fas fa-times"></i>
                    </button>
                `;
                tagsContainer.appendChild(badge);
            });
            hiddenTags.value = JSON.stringify(tags); 
        }

        window.removeTag = function(index) { tags.splice(index, 1); renderTags(); };

        function addTag() {
            const nombre = tagName.value.trim();
            const nombreEn = tagNameEn.value.trim();
            const nombrePt = tagNamePt.value.trim();
            const precio = parseFloat(tagPrice.value) || 0;
            
            if (nombre) {
                const existe = tags.find(t => (typeof t === 'string' ? t : t.nombre).toLowerCase() === nombre.toLowerCase());
                if (!existe) {
                    tags.push({ 
                        nombre: nombre, 
                        nombre_en: nombreEn, 
                        nombre_pt: nombrePt, 
                        precio: precio 
                    });
                    
                    // Limpiamos las cajitas y ocultamos el preview del precio
                    tagName.value = ''; 
                    tagNameEn.value = ''; 
                    tagNamePt.value = ''; 
                    tagPrice.value = ''; 
                    tagContenedorPreview.classList.add('hidden');
                    tagContenedorPreview.classList.remove('flex');
                    
                    renderTags();
                } else {
                    alert("Ese extra en español ya existe.");
                }
            } else {
                alert("El nombre en Español es obligatorio.");
            }
        }

        btnAddTag.addEventListener('click', addTag);
        tagName.addEventListener('keydown', (e) => { if (e.key === 'Enter') { e.preventDefault(); addTag(); } });
        tagPrice.addEventListener('keydown', (e) => { if (e.key === 'Enter') { e.preventDefault(); addTag(); } });

        renderTags();
    });
</script>

@vite(['resources/js/admin/image-preview.js'])
@endsection