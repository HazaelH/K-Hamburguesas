@extends('layouts.app')

@section('titulo', 'Crear Cuenta')

@section('contenido')
<div class="min-h-[80vh] flex items-center justify-center py-10">
    
    {{-- TARJETA PRINCIPAL --}}
    <div class="w-full max-w-5xl bg-slate-900 rounded-3xl shadow-2xl shadow-black/50 overflow-hidden grid grid-cols-1 md:grid-cols-2 border border-slate-700/50">
        
        {{-- COLUMNA DERECHA (Primero en móvil, derecha en desktop por orden del grid en blade... espera, lo pondré igual que login para consistencia visual) --}}
        
        {{-- COLUMNA IZQUIERDA: IMAGEN (Comiendo Hamburguesa) --}}
        <div class="hidden md:block relative group order-2 md:order-1">
            <img src="https://images.unsplash.com/photo-1550547660-d9450f859349?q=80&w=1000&auto=format&fit=crop" 
                 class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" 
                 alt="Register Background">
            
            <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/30 to-transparent"></div>

            <div class="absolute bottom-0 left-0 p-12 text-white">
                <div class="bg-blue-600 w-12 h-12 rounded-lg flex items-center justify-center mb-4 shadow-lg shadow-blue-600/50">
                    <i class="fas fa-users text-2xl"></i>
                </div>
                <h2 class="text-4xl font-bold mb-2 tracking-tight">Únete al<br>Club.</h2>
                <p class="text-slate-300 text-sm opacity-90">Acumula puntos, obtén descuentos y pide más rápido.</p>
                
                {{-- Social Proof Falso (Estético) --}}
                <div class="flex items-center gap-3 mt-6 pt-6 border-t border-white/20">
                    <div class="flex -space-x-3">
                        <img class="w-8 h-8 rounded-full border-2 border-black" src="https://i.pravatar.cc/100?img=1" alt="">
                        <img class="w-8 h-8 rounded-full border-2 border-black" src="https://i.pravatar.cc/100?img=2" alt="">
                        <img class="w-8 h-8 rounded-full border-2 border-black" src="https://i.pravatar.cc/100?img=3" alt="">
                    </div>
                    <span class="text-xs font-bold text-slate-300">+2k Foodies felices</span>
                </div>
            </div>
        </div>

        {{-- COLUMNA DERECHA: FORMULARIO --}}
        <div class="p-8 md:p-12 flex flex-col justify-center bg-slate-900 relative order-1 md:order-2">
            
            <div class="absolute top-0 right-0 w-40 h-40 bg-blue-500/10 rounded-bl-full pointer-events-none blur-3xl"></div>

            <div class="mb-6">
                <h1 class="text-3xl font-bold text-white mb-2">Crear Cuenta</h1>
                <p class="text-slate-400">Completa tus datos para comenzar.</p>
            </div>

            <form method="POST" action="{{ route('register.post') }}" class="space-y-4">
                @csrf

                {{-- Nombre --}}
                <div>
                    <label class="text-sm font-bold text-slate-300 ml-1">Nombre Completo</label>
                    <div class="relative group mt-1">
                        <span class="absolute left-4 top-3.5 text-slate-500 group-focus-within:text-orange-500 transition-colors">
                            <i class="fas fa-user"></i>
                        </span>
                        <input type="text" name="name" value="{{ old('name') }}" required placeholder="Tu nombre"
                               class="w-full bg-slate-950/50 border border-slate-700 rounded-xl py-3.5 pl-11 pr-4 text-white placeholder-slate-600 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 focus:outline-none transition-all">
                    </div>
                    @error('name') <p class="text-red-400 text-xs mt-1 ml-1">{{ $message }}</p> @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label class="text-sm font-bold text-slate-300 ml-1">Correo Electrónico</label>
                    <div class="relative group mt-1">
                        <span class="absolute left-4 top-3.5 text-slate-500 group-focus-within:text-orange-500 transition-colors">
                            <i class="fas fa-envelope"></i>
                        </span>
                        <input type="email" name="email" value="{{ old('email') }}" required placeholder="tu@email.com"
                               class="w-full bg-slate-950/50 border border-slate-700 rounded-xl py-3.5 pl-11 pr-4 text-white placeholder-slate-600 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 focus:outline-none transition-all">
                    </div>
                    @error('email') <p class="text-red-400 text-xs mt-1 ml-1">{{ $message }}</p> @enderror
                </div>

                {{-- Passwords Grid --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-bold text-slate-300 ml-1">Contraseña</label>
                        <div class="relative group mt-1">
                            <span class="absolute left-4 top-3.5 text-slate-500 group-focus-within:text-orange-500 transition-colors">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input type="password" name="password" required placeholder="••••••"
                                   class="w-full bg-slate-950/50 border border-slate-700 rounded-xl py-3.5 pl-11 pr-4 text-white placeholder-slate-600 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 focus:outline-none transition-all">
                        </div>
                    </div>
                    <div>
                        <label class="text-sm font-bold text-slate-300 ml-1">Confirmar</label>
                        <div class="relative group mt-1">
                            <span class="absolute left-4 top-3.5 text-slate-500 group-focus-within:text-orange-500 transition-colors">
                                <i class="fas fa-check-double"></i>
                            </span>
                            <input type="password" name="password_confirmation" required placeholder="••••••"
                                   class="w-full bg-slate-950/50 border border-slate-700 rounded-xl py-3.5 pl-11 pr-4 text-white placeholder-slate-600 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 focus:outline-none transition-all">
                        </div>
                    </div>
                </div>
                @error('password') <p class="text-red-400 text-xs mt-1 ml-1">{{ $message }}</p> @enderror

                {{-- Botón Registrar --}}
                <button type="submit" 
                        class="w-full bg-orange-600 hover:bg-orange-500 text-white font-bold py-4 rounded-xl shadow-lg shadow-orange-600/40 transform transition hover:-translate-y-0.5 active:scale-95 flex items-center justify-center gap-2 mt-6">
                    <span>Crear mi cuenta</span>
                </button>
            </form>

            {{-- Footer Formulario --}}
            <div class="mt-8 text-center border-t border-slate-800 pt-6">
                <p class="text-slate-400 text-sm">
                    ¿Ya eres miembro? 
                    <a href="{{ route('login') }}" class="text-orange-500 font-bold hover:text-orange-400 hover:underline transition">
                        Inicia sesión aquí
                    </a>
                </p>
            </div>

        </div>
    </div>
</div>
@endsection