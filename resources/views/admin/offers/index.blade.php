@extends('layouts.admin')

@section('titulo', 'Dashboard') {{-- Nota: 'titulo' en español como en el layout --}}

@section('contenido') {{-- Nota: 'contenido' para coincidir --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

    {{-- COLUMNA IZQUIERDA: FORMULARIO DE CREACIÓN --}}
    <div class="lg:col-span-1">
        <div class="bg-slate-800 p-6 rounded-2xl border border-slate-700 shadow-xl sticky top-24">
            <h2 class="text-xl font-bold text-white mb-4 flex items-center gap-2">
                <div class="bg-orange-500 p-2 rounded-lg"><i class="fas fa-tag text-white"></i></div>
                Nueva Oferta
            </h2>

            <form action="{{ route('admin.offers.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                
                <div>
                    <label class="block text-sm font-bold text-slate-400 mb-1">Título de la Promo</label>
                    <input type="text" name="titulo" placeholder="Ej: Martes 2x1" class="w-full bg-slate-900 border border-slate-700 rounded-lg p-2.5 text-white focus:border-orange-500 focus:outline-none" required>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-400 mb-1">Descripción</label>
                    <textarea name="descripcion" rows="3" placeholder="Detalles de la promo..." class="w-full bg-slate-900 border border-slate-700 rounded-lg p-2.5 text-white focus:border-orange-500 focus:outline-none"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-400 mb-1">Descuento (%)</label>
                        <input type="number" name="porcentaje" value="0" min="0" max="100" class="w-full bg-slate-900 border border-slate-700 rounded-lg p-2.5 text-white focus:border-orange-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-400 mb-1">Precio Fijo ($)</label>
                        <input type="number" step="0.50" name="precio_promo" placeholder="Opcional" class="w-full bg-slate-900 border border-slate-700 rounded-lg p-2.5 text-white focus:border-orange-500 focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-400 mb-1">Inicia</label>
                        <input type="date" name="fecha_inicio" value="{{ date('Y-m-d') }}" class="w-full bg-slate-900 border border-slate-700 rounded-lg p-2.5 text-white focus:border-orange-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-400 mb-1">Termina</label>
                        <input type="date" name="fecha_fin" class="w-full bg-slate-900 border border-slate-700 rounded-lg p-2.5 text-white focus:border-orange-500 focus:outline-none" required>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-400 mb-1">Imagen (Banner)</label>
                    <input type="file" name="imagen" class="w-full text-sm text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-bold file:bg-slate-700 file:text-white hover:file:bg-slate-600">
                </div>

                <button type="submit" class="w-full bg-orange-600 hover:bg-orange-500 text-white font-bold py-3 rounded-xl transition shadow-lg shadow-orange-900/20">
                    <i class="fas fa-plus-circle mr-2"></i> Publicar Oferta
                </button>
            </form>
        </div>
    </div>

    {{-- COLUMNA DERECHA: LISTADO DE OFERTAS --}}
    <div class="lg:col-span-2">
        <h2 class="text-2xl font-bold text-white mb-6">Ofertas Activas</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($offers as $offer)
                <div class="bg-slate-800 rounded-2xl overflow-hidden border border-slate-700 shadow-lg group hover:border-orange-500/50 transition-all">
                    
                    {{-- Imagen --}}
                    <div class="h-40 bg-slate-700 relative overflow-hidden">
                        @if($offer->imagen_url)
                            <img src="{{ asset('storage/' . $offer->imagen_url) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-slate-700 text-slate-600">
                                <i class="fas fa-image text-4xl"></i>
                            </div>
                        @endif

                        {{-- Badge de Porcentaje --}}
                        @if($offer->porcentaje > 0)
                            <div class="absolute top-4 right-4 bg-red-600 text-white font-black px-3 py-1 rounded-full shadow-lg transform rotate-3">
                                -{{ $offer->porcentaje }}%
                            </div>
                        @endif
                    </div>

                    {{-- Contenido --}}
                    <div class="p-5">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-xl font-bold text-white">{{ $offer->titulo }}</h3>
                            
                            {{-- Switch Activa/Inactiva (Visual) --}}
                            <div class="relative inline-block w-10 mr-2 align-middle select-none transition duration-200 ease-in">
                                <span class="block w-3 h-3 rounded-full {{ $offer->activa ? 'bg-green-500' : 'bg-red-500' }} shadow-md"></span>
                            </div>
                        </div>
                        
                        <p class="text-slate-400 text-sm mb-4 line-clamp-2">{{ $offer->descripcion }}</p>

                        <div class="flex items-center gap-4 text-xs font-mono text-slate-500 mb-4 border-t border-slate-700 pt-3">
                            <span title="Inicio"><i class="far fa-calendar-alt mr-1"></i> {{ $offer->fecha_inicio->format('d M') }}</span>
                            <i class="fas fa-arrow-right text-slate-600"></i>
                            <span title="Fin" class="{{ $offer->fecha_fin < now() ? 'text-red-400' : '' }}">
                                <i class="far fa-calendar-times mr-1"></i> {{ $offer->fecha_fin->format('d M') }}
                            </span>
                        </div>

                        <div class="flex gap-2">
                            <form action="{{ route('admin.offers.destroy', $offer->id) }}" method="POST" class="w-full" onsubmit="return confirm('¿Borrar oferta?');">
                                @csrf @method('DELETE')
                                <button class="w-full py-2 rounded-lg border border-red-500/30 text-red-400 hover:bg-red-500 hover:text-white transition text-sm font-bold">
                                    <i class="fas fa-trash-alt"></i> Eliminar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection