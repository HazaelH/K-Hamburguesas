<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('titulo') - K-Hamburguesas</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    @vite([
        'resources/css/app.css', 
        'resources/js/app.js',
        'resources/css/layout.css', 
        'resources/js/layout.js',
        'resources/js/client/cart.js'
    ])
</head>
<body class="antialiased min-h-screen flex flex-col">

    {{-- NAVBAR --}}
    <nav class="sticky top-0 z-50 w-full bg-slate-900/95 backdrop-blur-md border-b border-white/5 shadow-lg shadow-black/20">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex h-20 justify-between items-center">
                
                {{-- LOGO --}}
                <div class="flex-shrink-0 flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center gap-3 hover:opacity-90 transition group">
                        <div class="bg-orange-600 p-2.5 rounded-xl shadow-lg shadow-orange-900/50 group-hover:scale-105 transition-transform duration-300">
                            <i class="fas fa-utensils text-white text-xl"></i>
                        </div>
                        <div class="hidden sm:block">
                            <span class="font-bold text-xl tracking-wide text-white block leading-none">K-HAMBURGUESAS</span>
                            <span class="text-[10px] text-orange-500 block font-mono uppercase tracking-[0.25em] mt-1 font-bold">Restaurante</span>
                        </div>
                    </a>
                </div>

                @if(!request()->routeIs('login') && !request()->routeIs('register'))

                    {{-- MENÚ DESKTOP --}}
                    <div class="hidden md:flex items-center space-x-8">
                        <a href="{{ route('home') }}" class="text-sm font-bold text-white hover:text-orange-400 transition-colors border-b-2 border-transparent hover:border-orange-500 py-1">
                            <i class="fas fa-home w-6 text-center text-slate-500"></i>INICIO
                        </a>
                        <a href="{{ route('menu') }}" class="text-sm font-bold text-slate-300 hover:text-orange-400 transition-colors border-b-2 border-transparent hover:border-orange-500 py-1">
                            <i class="fas fa-list-ul w-6 text-center text-slate-500"></i>MENÚ
                        </a>
                        <a href="{{ route('offers.index') }}" class="block px-3 py-3 rounded-md text-base font-bold text-slate-300 hover:bg-slate-700 hover:text-orange-400 transition">
                            <i class="fas fa-tag w-6 text-center text-slate-500"></i> Ofertas
                        </a>
                    </div>

                    {{-- ACCIONES --}}
                    <div class="flex items-center gap-4">
                        {{-- Carrito --}}
                        <a href="{{ route('cart.index') }}" class="relative p-2 text-slate-300 hover:text-white transition-colors group">
                            <i class="fas fa-shopping-cart text-xl group-hover:animate-wiggle"></i>
                            <span id="cart-count" class="absolute top-0 right-0 -mt-1 -mr-1 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white animate-pulse">
                                {{ count(session('cart', [])) }}
                            </span>
                        </a>

                        <div class="hidden md:block h-6 w-px bg-slate-700"></div>

                        {{-- Usuario Desktop --}}
                        <div class="hidden md:flex items-center gap-4">
                            @auth
                                <div class="flex items-center gap-3">
                                    <div class="text-right hidden lg:block">
                                        <p class="text-sm font-bold text-white leading-none">{{ Auth::user()->name }}</p>
                                        <p class="text-[10px] text-orange-400 uppercase font-bold">{{ Auth::user()->rol }}</p>
                                    </div>
                                    
                                    <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}" class="h-10 w-10 rounded-full border border-slate-600 object-cover bg-slate-800 shadow-inner">

                                    {{-- NUEVO: Botón Editar Perfil (Desktop) --}}
                                    <a href="{{ route('profile.edit') }}" class="text-slate-400 hover:text-orange-400 transition p-2" title="Editar Perfil">
                                        <i class="fas fa-user-circle text-lg"></i>
                                    </a>

                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="text-slate-400 hover:text-red-500 transition p-2" title="Salir">
                                            <i class="fas fa-sign-out-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            @else
                                <a href="{{ route('login') }}" class="text-sm font-bold text-slate-300 hover:text-white">Ingresar</a>
                                <a href="{{ route('register') }}" class="bg-orange-600 hover:bg-orange-500 text-white text-sm font-bold px-5 py-2 rounded-full shadow-lg shadow-orange-900/20 transition-transform hover:-translate-y-0.5">Registro</a>
                            @endauth
                        </div>

                        {{-- Botón Sandwich (Móvil) --}}
                        <div class="md:hidden">
                            <button id="mobile-menu-btn" class="text-slate-300 hover:text-white p-2 focus:outline-none">
                                <i class="fas fa-bars text-2xl" id="menu-icon"></i>
                            </button>
                        </div>
                    </div>
                @else
                    {{-- MENÚ LOGIN/REGISTRO --}}
                    <div class="flex items-center gap-4">
                        <a href="{{ route('home') }}" class="text-sm font-bold text-slate-300 hover:text-white hidden sm:block">
                            <i class="fas fa-arrow-left mr-1"></i> Volver a Inicio
                        </a>
                        @if(request()->routeIs('login'))
                            <a href="{{ route('register') }}" class="bg-slate-800 border border-slate-700 text-white text-sm font-bold px-4 py-2 rounded-lg hover:bg-slate-700 transition">Crear Cuenta</a>
                        @endif
                        @if(request()->routeIs('register'))
                            <a href="{{ route('login') }}" class="bg-slate-800 border border-slate-700 text-white text-sm font-bold px-4 py-2 rounded-lg hover:bg-slate-700 transition">Iniciar Sesión</a>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        {{-- MENÚ MÓVIL --}}
        @if(!request()->routeIs('login') && !request()->routeIs('register'))
            <div id="mobile-menu" class="md:hidden max-h-0 overflow-hidden bg-slate-800 border-t border-slate-700">
                <div class="px-4 pt-2 pb-6 space-y-2">
                    <a href="{{ route('home') }}" class="block px-3 py-3 rounded-md text-base font-bold text-white hover:bg-slate-700 hover:text-orange-400 transition"><i class="fas fa-home w-6 text-center text-slate-500"></i> Inicio</a>
                    <a href="{{ route('menu') }}" class="block px-3 py-3 rounded-md text-base font-bold text-slate-300 hover:bg-slate-700 hover:text-orange-400 transition"><i class="fas fa-hamburger w-6 text-center text-slate-500"></i> Menú Completo</a>
                    <a href="{{ route('offers.index') }}" class="block px-3 py-3 rounded-md text-base font-bold text-slate-300 hover:bg-slate-700 hover:text-orange-400 transition"><i class="fas fa-tag w-6 text-center text-slate-500"></i> Ofertas</a>

                    <div class="border-t border-slate-700 my-2"></div>

                    @auth
                        <div class="px-3 py-3">
                            <div class="flex items-center gap-3 mb-3">
                                <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}" class="h-10 w-10 rounded-full border border-slate-600 object-cover bg-slate-700">
                                <div>
                                    <p class="text-white font-bold">{{ Auth::user()->name }}</p>
                                    <p class="text-xs text-orange-400">{{ Auth::user()->email }}</p>
                                </div>
                            </div>
                            
                            @if(Auth::user()->rol === 'empleado' || Auth::user()->rol === 'admin')
                                <a href="{{ Auth::user()->rol === 'admin' ? route('admin.dashboard') : route('employee.panel') }}" class="block w-full text-center bg-blue-600 text-white py-2 rounded-lg font-bold mb-2">
                                    <i class="fas fa-briefcase mr-2"></i> Ir al Panel
                                </a>
                            @endif

                            {{-- NUEVO: Enlace Perfil Móvil --}}
                            <a href="{{ route('profile.edit') }}" class="block w-full text-left px-3 py-2 rounded-md text-base font-medium text-slate-300 hover:bg-slate-700 hover:text-white">
                                <i class="fas fa-user-circle w-6 text-center"></i> Mi Perfil
                            </a>

                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="block w-full text-left px-3 py-2 rounded-md text-base font-medium text-red-400 hover:bg-slate-700 hover:text-red-300">
                                    <i class="fas fa-sign-out-alt w-6 text-center"></i> Cerrar Sesión
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="grid grid-cols-2 gap-4 px-3 mt-4">
                            <a href="{{ route('login') }}" class="text-center py-2 border border-slate-600 rounded-lg text-white font-bold hover:bg-slate-700">Ingresar</a>
                            <a href="{{ route('register') }}" class="text-center py-2 bg-orange-600 rounded-lg text-white font-bold hover:bg-orange-500">Registrarse</a>
                        </div>
                    @endauth
                </div>
            </div>
        @endif
    </nav>

    <main class="flex-1 container mx-auto p-4 md:p-6 fade-in">
        @yield('contenido')
    </main>

    <footer class="border-t border-slate-800 bg-slate-900 pt-10 pb-6 mt-10">
        <div class="container mx-auto px-4 text-center">
            <p class="text-slate-500 text-sm">&copy; {{ date('Y') }} K-Hamburguesas. El sabor que manda.</p>
        </div>
    </footer>
</body>
</html>