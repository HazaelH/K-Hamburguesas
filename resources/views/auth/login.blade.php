@extends('layouts.app')

@section('titulo', 'Iniciar Sesión')

@section('contenido')

{{-- Cargamos el CSS específico de Auth --}}
@vite(['resources/css/auth.css'])

<div class="min-h-[85vh] flex items-center justify-center py-10 px-4">
    
    {{-- TARJETA PRINCIPAL --}}
    <div class="w-full max-w-5xl bg-slate-900 rounded-3xl shadow-2xl shadow-black/60 overflow-hidden grid grid-cols-1 md:grid-cols-2 border border-slate-700/50">
        
        {{-- COLUMNA IZQUIERDA: SLIDESHOW (Oculta en móviles) --}}
        <div class="hidden md:block relative group overflow-hidden bg-black">
            
            {{-- CONTENEDOR DE IMÁGENES --}}
            <div class="absolute inset-0 w-full h-full">
                {{-- Imagen 1 (Inicia al segundo 0) --}}
                <div class="slideshow-bg" 
                     style="background-image: url('https://images.unsplash.com/photo-1568901346375-23c9450c58cd?q=80&w=1000&auto=format&fit=crop'); animation-delay: 0s;">
                </div>

                {{-- Imagen 2 (Inicia al segundo 6) --}}
                <div class="slideshow-bg" 
                     style="background-image: url('https://images.unsplash.com/photo-1550547660-d9450f859349?q=80&w=1000&auto=format&fit=crop'); animation-delay: 6s;">
                </div>

                {{-- Imagen 3 (Inicia al segundo 12) --}}
                <div class="slideshow-bg" 
                     style="background-image: url('https://images.unsplash.com/photo-1594212699903-ec8a3eca50f5?q=80&w=1000&auto=format&fit=crop'); animation-delay: 12s;">
                </div>
            </div>
            
            {{-- OVERLAY OSCURO (Gradiente) --}}
            <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent z-10"></div>

            {{-- TEXTO FLOTANTE --}}
            <div class="absolute bottom-0 left-0 p-12 text-white z-20">
                <div class="bg-orange-600 w-12 h-12 rounded-lg flex items-center justify-center mb-4 shadow-lg shadow-orange-600/50 backdrop-blur-sm">
                    <i class="fas fa-crown text-2xl"></i>
                </div>
                <h2 class="text-4xl font-bold mb-2 tracking-tight drop-shadow-lg">El sabor que<br>manda.</h2>
                <p class="text-slate-300 text-sm opacity-90 drop-shadow-md">Accede a tus pedidos favoritos y promociones exclusivas.</p>
            </div>
        </div>

        {{-- COLUMNA DERECHA: FORMULARIO --}}
        <div class="p-8 md:p-12 flex flex-col justify-center bg-slate-900 relative">
            
            {{-- Decoración de fondo (Blur naranja) --}}
            <div class="absolute top-0 right-0 w-32 h-32 bg-orange-500/10 rounded-bl-full pointer-events-none blur-3xl"></div>

            <div class="mb-8 relative z-10">
                <h1 class="text-3xl font-bold text-white mb-2">Bienvenido de nuevo</h1>
                <p class="text-slate-400">Por favor, ingresa tus credenciales.</p>
            </div>

            <form method="POST" action="{{ route('login.post') }}" class="space-y-5 relative z-10">
                @csrf

                {{-- Input Email --}}
                <div class="space-y-2">
                    <label class="text-sm font-bold text-slate-300 ml-1">Correo Electrónico</label>
                    <div class="relative group">
                        <span class="input-icon absolute left-4 top-3.5 text-slate-500 transition-colors duration-300">
                            <i class="fas fa-envelope"></i>
                        </span>
                        <input type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="ejemplo@correo.com"
                               class="w-full bg-slate-950/50 border border-slate-700 rounded-xl py-3.5 pl-11 pr-4 text-white placeholder-slate-600 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 focus:outline-none transition-all shadow-inner">
                    </div>
                    @error('email') <p class="text-red-400 text-xs mt-1 ml-1 font-bold"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
                </div>

                {{-- Input Password --}}
                <div class="space-y-2">
                    <div class="flex justify-between items-center">
                        <label class="text-sm font-bold text-slate-300 ml-1">Contraseña</label>
                        <a href="#" class="text-xs text-orange-500 hover:text-orange-400 transition font-semibold">¿Olvidaste tu contraseña?</a>
                    </div>
                    <div class="relative group">
                        <span class="input-icon absolute left-4 top-3.5 text-slate-500 transition-colors duration-300">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" name="password" required placeholder="••••••••"
                               class="w-full bg-slate-950/50 border border-slate-700 rounded-xl py-3.5 pl-11 pr-4 text-white placeholder-slate-600 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 focus:outline-none transition-all shadow-inner">
                    </div>
                </div>

                {{-- Botón Entrar --}}
                <button type="submit" 
                        class="w-full bg-orange-600 hover:bg-orange-500 text-white font-bold py-4 rounded-xl shadow-lg shadow-orange-600/40 transform transition hover:-translate-y-0.5 active:scale-95 flex items-center justify-center gap-2 mt-4">
                    <span>Iniciar Sesión</span>
                    <i class="fas fa-arrow-right text-sm"></i>
                </button>
            </form>

            {{-- Footer Formulario --}}
            <div class="mt-8 text-center border-t border-slate-800 pt-6 relative z-10">
                <p class="text-slate-400 text-sm">
                    ¿Aún no tienes cuenta? 
                    <a href="{{ route('register') }}" class="text-orange-500 font-bold hover:text-orange-400 hover:underline transition">
                        Regístrate gratis
                    </a>
                </p>
            </div>

        </div>
    </div>
</div>
@endsection