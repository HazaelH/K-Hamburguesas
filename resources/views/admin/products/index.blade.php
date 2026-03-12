@extends('layouts.admin')

@section('titulo', __('admin/products/products.title_index'))

@section('contenido')
<div class="py-6 space-y-6 relative">
    
    <div class="flex flex-col md:flex-row justify-between items-end md:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-white"><i class="fas fa-hamburger text-orange-500 mr-2"></i> {{ __('admin/products/products.header_title') }}</h1>
            <p class="text-slate-400">{{ __('admin/products/products.header_subtitle') }}</p>
        </div>
        <a href="{{ route('admin.products.create') }}" 
           class="bg-orange-700 hover:bg-orange-500 text-white font-bold py-2.5 px-6 rounded-xl shadow-lg shadow-orange-900/20 transition transform hover:-translate-y-0.5 flex items-center gap-2">
            <i class="fas fa-plus"></i> {{ __('admin/products/products.btn_new_product') }}
        </a>
    </div>

    @if(session('success') || session('error'))
        <div id="toast-notification" class="fixed bottom-5 right-5 z-[200] flex flex-col gap-3 shadow-2xl transform transition-all duration-500 translate-y-0 opacity-100">
            @if(session('success'))
                <div class="bg-emerald-950/90 border border-emerald-500 text-emerald-400 px-6 py-4 rounded-2xl flex items-center gap-4 backdrop-blur-md shadow-[0_0_20px_rgba(16,185,129,0.2)]">
                    <i class="fas fa-check-circle text-2xl animate-bounce"></i>
                    <div>
                        <p class="font-bold text-lg leading-tight">¡Éxito!</p>
                        <p class="text-xs text-emerald-300 mt-1 max-w-xs">{{ session('success') }}</p>
                    </div>
                    <button aria-label="Cerrar notificación" onclick="document.getElementById('toast-notification').remove()" class="ml-2 text-emerald-600 hover:text-emerald-400 transition">
                        <i class="fas fa-times pointer-events-none"></i>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-950/90 border border-red-500 text-red-400 px-6 py-4 rounded-2xl flex items-center gap-4 backdrop-blur-md shadow-[0_0_20px_rgba(239,68,68,0.2)]">
                    <i class="fas fa-shield-alt text-2xl animate-pulse"></i>
                    <div>
                        <p class="font-bold text-lg leading-tight">Acción Denegada</p>
                        <p class="text-xs text-red-300 mt-1 max-w-xs">{{ session('error') }}</p>
                    </div>
                    <button aria-label="Cerrar notificación" onclick="document.getElementById('toast-notification').remove()" class="ml-2 text-red-600 hover:text-red-400 transition">
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
            }, 6000);
        </script>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-slate-800 p-5 rounded-2xl border border-slate-300 shadow-lg flex items-center gap-4">
            <div class="h-12 w-12 rounded-full bg-blue-500/20 flex items-center justify-center text-blue-400 text-xl border border-blue-500/30">
                <i class="fas fa-utensils"></i>
            </div>
            <div>
                <p class="text-slate-400 text-xs font-bold uppercase tracking-wider">{{ __('admin/products/products.stats_total') }}</p>
                <p class="text-2xl font-black text-white">{{ $stats['total'] }}</p>
            </div>
        </div>
        <div class="bg-slate-800 p-5 rounded-2xl border border-slate-300 shadow-lg flex items-center gap-4">
            <div class="h-12 w-12 rounded-full bg-purple-500/20 flex items-center justify-center text-purple-400 text-xl border border-purple-500/30">
                <i class="fas fa-tags"></i>
            </div>
            <div>
                <p class="text-slate-400 text-xs font-bold uppercase tracking-wider">{{ __('admin/products/products.stats_categories') }}</p>
                <p class="text-2xl font-black text-white">{{ $stats['categorias'] }}</p>
            </div>
        </div>
        <div class="bg-slate-800 p-5 rounded-2xl border border-slate-300 shadow-lg flex items-center gap-4">
            <div class="h-12 w-12 rounded-full bg-emerald-500/20 flex items-center justify-center text-emerald-400 text-xl border border-emerald-500/30">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div>
                <p class="text-slate-400 text-xs font-bold uppercase tracking-wider">{{ __('admin/products/products.stats_avg_price') }}</p>
                <p class="text-2xl font-black text-white">{{ formatCurrency($stats['precio_promedio']) }}</p>
            </div>
        </div>
    </div>

    <div class="bg-slate-800 p-5 rounded-2xl border border-slate-300 shadow-lg mb-8">
        <form id="filtro-productos" action="{{ route('admin.products.index') }}" method="GET">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                
                <div class="relative lg:col-span-2">
                    <span class="absolute left-4 top-3 text-slate-500"><i class="fas fa-search"></i></span>
                    {{-- Agregado aria-label --}}
                    <input type="text" name="search" aria-label="Buscar producto" value="{{ request('search') }}" placeholder="{{ __('admin/products/products.search_placeholder') }}" 
                           class="w-full bg-slate-900 border border-slate-300 text-white rounded-xl py-2.5 pl-11 pr-4 focus:outline-none focus:border-orange-500 transition-colors text-sm placeholder-slate-500">
                </div>

                <div class="relative">
                    @php
                        // Traemos las categorías directo de la BD para el filtro
                        $categoriasFiltro = \App\Models\Categoria::all();
                    @endphp
                    {{-- Agregado aria-label --}}
                    <select name="categoria" aria-label="Filtrar por categoría" class="w-full bg-slate-900 border border-slate-300 text-white rounded-xl py-2.5 px-4 appearance-none focus:outline-none focus:border-orange-500 transition-colors text-sm cursor-pointer filter-dropdown">
                        <option value="">{{ __('admin/products/products.all_categories') }}</option>
                        @foreach($categoriasFiltro as $cat)
                            @php
                                $nombreMostrar = $cat->nombre;
                                if (app()->getLocale() == 'en' && !empty($cat->nombre_en)) $nombreMostrar = $cat->nombre_en;
                                if (app()->getLocale() == 'pt' && !empty($cat->nombre_pt)) $nombreMostrar = $cat->nombre_pt;
                            @endphp
                            <option value="{{ $cat->nombre }}" {{ request('categoria') == $cat->nombre ? 'selected' : '' }}>{{ $nombreMostrar }}</option>
                        @endforeach
                    </select>
                    <i class="fas fa-chevron-down absolute right-4 top-3.5 text-slate-500 text-xs pointer-events-none"></i>
                </div>

                <div class="relative">
                    {{-- Agregado aria-label --}}
                    <select name="estado" aria-label="Filtrar por estado" class="w-full bg-slate-900 border border-slate-300 text-white rounded-xl py-2.5 px-4 appearance-none focus:outline-none focus:border-orange-500 transition-colors text-sm cursor-pointer filter-dropdown">
                        <option value="">{{ __('admin/products/products.any_status') }}</option>
                        <option value="disponible" {{ request('estado') == 'disponible' ? 'selected' : '' }}>{{ __('admin/products/products.status_available_filter') }}</option>
                        <option value="agotado" {{ request('estado') == 'agotado' ? 'selected' : '' }}>{{ __('admin/products/products.status_out_filter') }}</option>
                    </select>
                    <i class="fas fa-chevron-down absolute right-4 top-3.5 text-slate-500 text-xs pointer-events-none"></i>
                </div>

                <div class="flex gap-2 lg:col-span-2">
                    <div class="relative flex-1">
                        <span class="absolute left-3 top-2.5 text-slate-500 font-bold">$</span>
                        {{-- Agregado aria-label --}}
                        <input type="number" aria-label="Precio mínimo" name="precio_min" value="{{ request('precio_min') }}" placeholder="{{ __('admin/products/products.price_min') }}" min="0" step="0.5"
                               class="w-full bg-slate-900 border border-slate-300 text-white rounded-xl py-2.5 pl-7 pr-2 focus:outline-none focus:border-orange-500 transition-colors text-sm placeholder-slate-500">
                    </div>
                    <span class="text-slate-600 flex items-center">-</span>
                    <div class="relative flex-1">
                        <span class="absolute left-3 top-2.5 text-slate-500 font-bold">$</span>
                        {{-- Agregado aria-label --}}
                        <input type="number" aria-label="Precio máximo" name="precio_max" value="{{ request('precio_max') }}" placeholder="{{ __('admin/products/products.price_max') }}" min="0" step="0.5"
                               class="w-full bg-slate-900 border border-slate-300 text-white rounded-xl py-2.5 pl-7 pr-2 focus:outline-none focus:border-orange-500 transition-colors text-sm placeholder-slate-500">
                    </div>
                </div>

                <div class="relative">
                    {{-- Agregado aria-label --}}
                    <select name="ordenar" aria-label="Ordenar productos" class="w-full bg-slate-900 border border-slate-300 text-white rounded-xl py-2.5 px-4 appearance-none focus:outline-none focus:border-orange-500 transition-colors text-sm cursor-pointer filter-dropdown">
                        <option value="recientes" {{ request('ordenar') == 'recientes' ? 'selected' : '' }}>{{ __('admin/products/products.sort_recent') }}</option>
                        <option value="precio_asc" {{ request('ordenar') == 'precio_asc' ? 'selected' : '' }}>{{ __('admin/products/products.sort_price_asc') }}</option>
                        <option value="precio_desc" {{ request('ordenar') == 'precio_desc' ? 'selected' : '' }}>{{ __('admin/products/products.sort_price_desc') }}</option>
                    </select>
                    <i class="fas fa-chevron-down absolute right-4 top-3.5 text-slate-500 text-xs pointer-events-none"></i>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="flex-1 bg-slate-700 hover:bg-slate-600 text-white font-bold py-2.5 rounded-xl border border-slate-600 transition flex justify-center items-center gap-2 text-sm">
                        <i class="fas fa-filter"></i> {{ __('admin/products/products.btn_filter') }}
                    </button>

                    <a href="{{ route('admin.products.index', ['ver_bajas' => 1]) }}" aria-label="Ver productos eliminados" class="flex items-center justify-center bg-slate-800 text-slate-400 hover:bg-slate-700 hover:text-white px-4 rounded-xl transition-colors border border-slate-300">
                        <i class="fas fa-trash-alt"></i>
                    </a>
                    
                    @if(request('search') || request('categoria') || request('estado') || request('precio_min') || request('precio_max') || request('ordenar'))
                        <a href="{{ route('admin.products.index') }}" aria-label="Limpiar filtros" class="flex items-center justify-center bg-red-500/10 text-red-400 hover:bg-red-500 hover:text-white px-4 rounded-xl transition-colors border border-red-500/30">
                            <i class="fas fa-eraser"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <div class="bg-slate-800 rounded-2xl border border-slate-300 overflow-hidden shadow-2xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-400">
                <thead class="bg-slate-900 text-slate-300 uppercase text-[10px] font-black tracking-widest">
                    <tr>
                        <th class="px-5 py-4 w-20">{{ __('admin/products/products.col_image') }}</th>
                        <th class="px-5 py-4">{{ __('admin/products/products.col_info') }}</th>
                        <th class="px-5 py-4">{{ __('admin/products/products.col_category') }}</th>
                        <th class="px-5 py-4">{{ __('admin/products/products.col_price') }}</th>
                        <th class="px-5 py-4 text-center">{{ __('admin/products/products.col_status') }}</th>
                        <th class="px-5 py-4 text-right">{{ __('admin/products/products.col_actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/50">
                    @forelse($productos as $producto)
                    <tr class="hover:bg-slate-700/30 transition-colors">
                        <td class="px-5 py-3">
                            @if($producto->imagen_url)
                                {{-- Agregado ALT a la imagen --}}
                                <img src="{{ asset('imagenes/' . $producto->imagen_url) }}" alt="{{ $producto->nombre_traducido }}" class="h-12 w-12 object-cover rounded-lg border border-slate-600 shadow-sm">
                            @else
                                <div class="h-12 w-12 bg-slate-900 rounded-lg flex items-center justify-center text-slate-600 text-[10px] border border-slate-300 font-bold">
                                    {{ __('admin/products/products.no_image') }}
                                </div>
                            @endif
                        </td>
                        
                        <td class="px-5 py-3">
                            <p class="text-white font-bold">{{ $producto->nombre_traducido }}</p>
                            <p class="text-[11px] text-slate-500 truncate max-w-xs">{{ $producto->descripcion_traducida }}</p>
                        </td>

                        <td class="px-5 py-3">
                            <span class="bg-slate-900 border border-slate-600 text-slate-300 text-[10px] px-2.5 py-1 rounded-md uppercase font-bold tracking-wider">
                                {{ $producto->categoria_traducida }}
                            </span>
                        </td>

                        <td class="px-5 py-3 font-mono font-black text-emerald-400 text-base">
                            {{ formatCurrency($producto->precio) }}
                        </td>

                        <td class="px-5 py-3 text-center">
                            {{-- Agregado aria-label al botón de estado --}}
                            <button aria-label="Cambiar disponibilidad de {{ $producto->nombre_traducido }}" type="button" onclick="window.cambiarEstadoProducto('{{ route('admin.products.toggle-status', $producto->id_producto) }}', this)" 
                                    class="border text-[10px] font-bold px-2 py-1 rounded uppercase tracking-wider transition-all duration-300 hover:scale-105 {{ $producto->is_active ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20 hover:bg-emerald-500/30' : 'bg-red-500/10 text-red-400 border-red-500/20 hover:bg-red-500/30' }}">
                                @if($producto->is_active)
                                    <i class="fas fa-check-circle text-[10px] mr-1 pointer-events-none"></i> <span>{{ __('admin/products/products.badge_stock') }}</span>
                                @else
                                    <i class="fas fa-times-circle text-[10px] mr-1 pointer-events-none"></i> <span>{{ __('admin/products/products.badge_out') }}</span>
                                @endif
                            </button>
                        </td>

                        <td class="px-5 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                @if($producto->trashed())
                                    <form action="{{ route('admin.products.restore', $producto->id_producto) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="bg-emerald-600/20 text-emerald-400 hover:bg-emerald-600 hover:text-white px-2 py-1.5 rounded transition text-xs font-bold border border-emerald-500/30">
                                            <i class="fas fa-trash-restore"></i> <span class="sr-only">Restaurar {{ $producto->nombre_traducido }}</span> {{ __('admin/products/products.action_restore') }}
                                        </button>
                                    </form>
                                    
                                    <form action="{{ route('admin.products.force-delete', $producto->id_producto) }}" method="POST" id="form-force-{{ $producto->id_producto }}">
                                        @csrf @method('DELETE')
                                        <button type="button" aria-label="Eliminar permanentemente {{ $producto->nombre_traducido }}" onclick="confirmarBorradoProducto('form-force-{{ $producto->id_producto }}')" class="bg-red-600/20 text-red-400 hover:bg-red-600 hover:text-white px-2 py-1.5 rounded transition text-xs font-bold border border-red-500/30">
                                            <i class="fas fa-skull pointer-events-none"></i>
                                        </button>
                                    </form>
                                @else
                                    {{-- Agregado aria-label a enlaces idénticos y reemplazado el title --}}
                                    <a href="{{ route('admin.products.edit', $producto->id_producto) }}" aria-label="Editar {{ $producto->nombre_traducido }}" class="bg-blue-600/20 text-blue-400 hover:bg-blue-600 hover:text-white h-8 w-8 rounded flex items-center justify-center transition-colors border border-blue-500/30">
                                        <i class="fas fa-edit text-xs"></i>
                                    </a>

                                    <form action="{{ route('admin.products.destroy', $producto->id_producto) }}" method="POST" id="form-delete-{{ $producto->id_producto }}">
                                        @csrf @method('DELETE')
                                        <button type="button" aria-label="Enviar {{ $producto->nombre_traducido }} a papelera" onclick="confirmarBorradoProducto('form-delete-{{ $producto->id_producto }}')" class="bg-red-600/20 text-red-400 hover:bg-red-600 hover:text-white h-8 w-8 rounded flex items-center justify-center transition-colors border border-red-500/30">
                                            <i class="fas fa-trash-alt text-xs pointer-events-none"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-16 text-center text-slate-500">
                            <div class="bg-slate-900 h-20 w-20 rounded-full flex items-center justify-center mx-auto mb-4 border border-slate-300">
                                <i class="fas fa-hamburger text-3xl opacity-50"></i>
                            </div>
                            <p class="text-white font-bold text-lg">{{ __('admin/products/products.empty_title') }}</p>
                            <p class="text-sm mt-1">{{ __('admin/products/products.empty_desc') }}</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($productos->hasPages())
        <div class="px-5 py-3 border-t border-slate-300 bg-slate-900">
            {{ $productos->links() }} 
        </div>
        @endif
    </div>
</div>

<div id="delete-modal-product" class="fixed inset-0 z-[100] hidden flex items-center justify-center bg-black/80 backdrop-blur-sm transition-opacity opacity-0">
    <div id="delete-modal-panel" class="bg-slate-900 border border-slate-300 p-6 rounded-3xl shadow-2xl w-full max-w-sm transform scale-95 transition-all text-center">
        
        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full mb-4 bg-red-500/20 text-red-500 border border-red-500/50">
            <i class="fas fa-fire text-2xl animate-pulse"></i>
        </div>
        
        <h3 class="text-xl font-black text-white mb-2">{{ __('admin/products/products.modal_del_title') }}</h3>
        <p class="text-xs text-slate-400 mb-6 px-2">{{ __('admin/products/products.modal_del_desc') }}</p>
        
        <div class="flex gap-3">
            <button onclick="cerrarModalBorradoProducto()" class="flex-1 bg-slate-800 hover:bg-slate-700 text-white font-bold py-3 rounded-xl transition-all border border-slate-600 text-sm">
                {{ __('admin/products/products.btn_modal_cancel') }}
            </button>
            <button id="modal-confirm-btn" class="flex-1 bg-red-600 hover:bg-red-500 text-white font-bold py-3 rounded-xl shadow-lg transition-all shadow-red-900/50 text-sm">
                {{ __('admin/products/products.btn_modal_delete') }}
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
    @vite(['resources/js/admin/products.js'])
@endpush