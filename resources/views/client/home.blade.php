@extends('layouts.app')

@section('titulo', 'Inicio')

@section('contenido')

    <section class="relative bg-gray-900 rounded-3xl overflow-hidden shadow-2xl mb-16 border border-gray-800">
        
        <div class="absolute inset-0">
            <img src="https://images.unsplash.com/photo-1550547660-d9450f859349?ixlib=rb-1.2.1&auto=format&fit=crop&w=1951&q=80" 
                 class="w-full h-full object-cover opacity-20" 
                 alt="Fondo Hamburguesa">
            <div class="absolute inset-0 bg-gradient-to-r from-gray-900 via-gray-900/80 to-transparent"></div>
        </div>

        <div class="relative z-10 max-w-7xl mx-auto py-16 px-6 sm:px-12 flex flex-col md:flex-row items-center gap-10">
            
            <div class="md:w-3/5 space-y-6 animate-fade-in-down">
                <span class="text-orange-500 font-bold tracking-wider uppercase text-sm">
                    Est. 2026 &bull; Sabor Auténtico
                </span>
                
                <h1 class="text-4xl md:text-6xl font-extrabold text-white leading-tight">
                    {!! nl2br(e($mensajeBienvenida)) !!} 
                    </h1>
                
                <p class="text-gray-300 text-lg max-w-lg leading-relaxed">
                    En K-Hamburguesas, la frescura y la calidad se encuentran en cada bocado. 
                    Pide ahora y recibe tu orden caliente en minutos.
                </p>
                
                <div class="flex flex-wrap gap-4 pt-4">
                    <a href="{{ route('menu') }}" 
                       class="bg-orange-600 hover:bg-orange-700 text-white px-8 py-4 rounded-xl font-bold transition transform hover:scale-105 shadow-lg shadow-orange-500/20 flex items-center">
                        <i class="fas fa-utensils mr-2"></i> Ver Menú Completo
                    </a>
                    
                    @auth
                        @if($ultimoPedido)
                            <a href="{{ route('ticket', $ultimoPedido->id) }}" 
                               class="bg-gray-800 hover:bg-gray-700 border border-gray-600 text-gray-300 px-8 py-4 rounded-xl font-bold transition flex items-center group">
                                <i class="fas fa-history mr-2 text-orange-500 group-hover:rotate-180 transition-transform"></i>
                                Ver Mi Último Ticket
                            </a>
                        @endif
                    @endauth
                </div>
            </div>

            <div class="md:w-2/5 relative hidden md:block">
                <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[300px] h-[300px] bg-orange-500/20 rounded-full blur-3xl"></div>
                
                <img src="https://png.pngtree.com/png-vector/20230408/ourmid/pngtree-gourmet-burger-vector-png-image_6680410.png" 
                     alt="Hamburguesa Gigante" 
                     class="relative z-10 w-full drop-shadow-2xl hover:scale-110 transition duration-500 cursor-pointer">
            </div>
        </div>
    </section>

    <section class="mb-20">
        <div class="flex items-center justify-between mb-8 border-b border-gray-800 pb-4">
            <h2 class="text-3xl font-bold text-white">
                <i class="fas fa-fire text-orange-500 mr-2"></i> Nuestros Favoritos
            </h2>
            <a href="{{ route('menu') }}" class="text-orange-500 hover:text-orange-400 text-sm font-bold flex items-center">
                Ver todo <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            @foreach($platosPopulares as $producto)
                <div class="bg-gray-800 rounded-2xl overflow-hidden shadow-lg border border-gray-700 hover:border-orange-500/50 transition duration-300 group flex flex-col">
                    
                    <div class="h-48 overflow-hidden relative">
                        <img src="{{ asset('imagenes/' . $producto->imagen_url) }}" 
                             alt="{{ $producto->nombre }}" 
                             class="w-full h-full object-cover transform group-hover:scale-110 transition duration-500">
                        
                        <div class="absolute bottom-2 right-2 bg-black/80 backdrop-blur text-white font-bold px-3 py-1 rounded-lg border border-orange-500 text-sm shadow-lg">
                            ${{ number_format($producto->precio, 2) }}
                        </div>
                    </div>

                    <div class="p-5 flex-1 flex flex-col">
                        <div class="flex justify-between items-start mb-2">
                            <span class="text-xs font-bold text-orange-400 uppercase tracking-wide border border-orange-500/30 px-2 py-0.5 rounded">
                                {{ $producto->categoria }}
                            </span>
                        </div>

                        <h3 class="text-lg font-bold text-white mb-2 leading-tight group-hover:text-orange-400 transition">
                            {{ $producto->nombre }}
                        </h3>
                        
                        <p class="text-gray-400 text-sm mb-4 line-clamp-2 flex-1">
                            {{ $producto->descripcion }}
                        </p>
                        
                        <a href="{{ route('menu') }}" 
                           class="w-full bg-gray-700 hover:bg-orange-600 text-white font-bold py-2 rounded-lg transition flex items-center justify-center text-sm">
                            <i class="fas fa-plus mr-2"></i> Ordenar
                        </a>
                    </div>
                </div>
                @endforeach
        </div>
    </section>

@endsection