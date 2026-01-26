@extends('layouts.app')

@section('titulo', 'Ofertas y Promociones')

@section('contenido')
<div class="pb-20 min-h-screen">

    {{-- Header --}}
    <div class="text-center py-10 animate-fade-in-down">
        <span class="bg-orange-500/20 text-orange-400 border border-orange-500/30 px-4 py-1 rounded-full text-xs font-bold uppercase tracking-widest mb-4 inline-block">
            Tiempo Limitado
        </span>
        <h1 class="text-4xl md:text-5xl font-extrabold text-white mb-4">
            Ofertas <span class="text-transparent bg-clip-text bg-gradient-to-r from-orange-400 to-red-500">Explosivas</span>
        </h1>
    </div>

    <div class="container mx-auto px-4">
        
        @if($ofertas->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($ofertas as $oferta)
                    <div class="relative bg-slate-800 rounded-2xl overflow-hidden group hover:-translate-y-2 transition-transform duration-300 shadow-xl shadow-black/50 border border-slate-700">
                        
                        {{-- Imagen --}}
                        <div class="h-52 overflow-hidden relative bg-slate-700">
                            @if($oferta->imagen_url)
                                <img src="{{ asset('storage/' . $oferta->imagen_url) }}" 
                                     alt="{{ $oferta->titulo }}" 
                                     class="w-full h-full object-cover group-hover:scale-110 transition duration-700 opacity-90 group-hover:opacity-100">
                            @else
                                {{-- Imagen por defecto si el admin no subió una --}}
                                <div class="w-full h-full flex items-center justify-center">
                                    <i class="fas fa-hamburger text-6xl text-slate-600"></i>
                                </div>
                            @endif
                            
                            {{-- Badge de Descuento --}}
                            @if($oferta->porcentaje > 0)
                                <div class="absolute top-4 left-4 bg-red-600 text-white font-black px-3 py-1 rounded-lg shadow-lg rotate-[-5deg] z-10">
                                    -{{ $oferta->porcentaje }}%
                                </div>
                            @endif
                        </div>

                        {{-- Cuerpo del Cupón --}}
                        <div class="relative bg-slate-800 px-6 pt-6 pb-8">
                            {{-- Decoración Punteada --}}
                            <div class="absolute top-0 left-4 right-4 border-t-2 border-dashed border-slate-600"></div>
                            
                            {{-- Muescas laterales --}}
                            <div class="absolute -top-3 -left-3 w-6 h-6 bg-[#0f172a] rounded-full z-10"></div>
                            <div class="absolute -top-3 -right-3 w-6 h-6 bg-[#0f172a] rounded-full z-10"></div>

                            <div class="mb-4 mt-2">
                                <h3 class="text-2xl font-bold text-white mb-2 leading-tight">{{ $oferta->titulo }}</h3>
                                <p class="text-slate-400 text-sm line-clamp-3">{{ $oferta->descripcion }}</p>
                            </div>

                            <div class="flex justify-between items-end mb-6 bg-slate-900/50 p-3 rounded-lg">
                                <div>
                                    <p class="text-xs text-slate-500 mb-1">Válido hasta</p>
                                    <p class="text-white font-mono text-sm">
                                        <i class="far fa-calendar-alt text-orange-500 mr-1"></i>
                                        {{ $oferta->fecha_fin->format('d/m/Y') }}
                                    </p>
                                </div>
                                
                                @if($oferta->precio_promo)
                                    <div class="text-right">
                                        <p class="text-xs text-orange-400 mb-1 font-bold">SOLO</p>
                                        <p class="text-3xl font-extrabold text-white leading-none">${{ number_format($oferta->precio_promo, 2) }}</p>
                                    </div>
                                @endif
                            </div>

                            <a href="{{ route('menu') }}" class="block w-full text-center bg-orange-600 hover:bg-orange-500 text-white font-bold py-3 rounded-xl transition shadow-lg shadow-orange-900/40 group-hover:scale-[1.02]">
                                ¡Aprovechar ahora!
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            {{-- Estado Vacío --}}
            <div class="text-center py-20 bg-slate-800/50 rounded-3xl border border-dashed border-slate-700 max-w-2xl mx-auto">
                <i class="fas fa-search-dollar text-6xl text-slate-600 mb-4"></i>
                <h2 class="text-2xl font-bold text-white mb-2">No hay ofertas activas hoy</h2>
                <p class="text-slate-400 mb-6">Estamos horneando nuevas promociones. Revisa nuestro menú regular.</p>
                <a href="{{ route('menu') }}" class="bg-slate-700 hover:bg-slate-600 text-white px-6 py-3 rounded-lg font-bold transition">
                    Ir al Menú
                </a>
            </div>
        @endif
    </div>
</div>
@endsection