<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('titulo') - Panel Empleado</title>
    
    @vite(['resources/css/app.css', 'resources/css/kitchen.css', 'resources/js/app.js'])
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        body { font-family: sans-serif; background-color: #0f172a; color: white; }
        .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: rgba(255, 255, 255, 0.05); }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.2); border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(255, 255, 255, 0.4); }
        
        #mobile-menu {
            transition: max-height 0.3s ease-in-out, opacity 0.3s ease-in-out;
            max-height: 0;
            opacity: 0;
            overflow: hidden;
        }
        #mobile-menu.open { max-height: 500px; opacity: 1; }
    </style>
</head>
<body class="h-screen flex flex-col overflow-hidden bg-slate-950">

    {{-- NAVBAR PRINCIPAL --}}
    <nav class="bg-slate-900 border-b border-slate-700 h-16 flex-shrink-0 z-50 shadow-md relative">
        <div class="container mx-auto h-full px-4 md:px-6 flex justify-between items-center">
            
            {{-- LOGO --}}
            <a href="{{ route('employee.panel') }}" class="flex items-center gap-3 hover:opacity-80 transition shrink-0">
                <div class="bg-orange-600 p-2 rounded-lg shadow-lg shadow-orange-900/50">
                    <i class="fas fa-utensils text-white"></i>
                </div>
                <div>
                    <span class="font-bold text-lg tracking-wide text-white block leading-none">K-HAMBURGUESAS</span>
                    <span class="text-[10px] text-orange-500 block font-mono uppercase tracking-[0.2em] mt-1">Employee Panel</span>
                </div>
            </a>

            {{-- MENÚ DE ESCRITORIO --}}
            <div class="hidden md:flex items-center gap-2 bg-slate-800 p-1 rounded-xl border border-slate-700">
                <a href="{{ route('employee.pos') }}" class="px-3 py-1.5 rounded-lg text-sm font-bold flex items-center gap-2 transition-all whitespace-nowrap {{ request()->routeIs('employee.pos') ? 'bg-blue-600 text-white shadow' : 'text-slate-400 hover:text-white hover:bg-slate-700' }}">
                    <i class="fas fa-cash-register"></i> <span>Venta</span>
                </a>
                <a href="{{ route('employee.orders.index') }}" class="px-3 py-1.5 rounded-lg text-sm font-bold flex items-center gap-2 transition-all whitespace-nowrap {{ request()->routeIs('employee.orders.index') ? 'bg-violet-600 text-white shadow' : 'text-slate-400 hover:text-white hover:bg-slate-700' }}">
                    <i class="fas fa-wallet"></i> <span>Caja</span>
                </a>
                <a href="{{ route('kitchen.live') }}" class="px-3 py-1.5 rounded-lg text-sm font-bold flex items-center gap-2 transition-all whitespace-nowrap {{ request()->routeIs('kitchen.live') ? 'bg-orange-600 text-white shadow' : 'text-slate-400 hover:text-white hover:bg-slate-700' }}">
                    <i class="fas fa-fire"></i> <span>Cocina</span>
                </a>
                <a href="{{ route('stock.index') }}" class="px-3 py-1.5 rounded-lg text-sm font-bold flex items-center gap-2 transition-all whitespace-nowrap {{ request()->routeIs('stock.index') ? 'bg-emerald-600 text-white shadow' : 'text-slate-400 hover:text-white hover:bg-slate-700' }}">
                    <i class="fas fa-boxes"></i> <span>Stock</span>
                </a>
                <a href="{{ route('delivery.scan') }}" class="px-3 py-1.5 rounded-lg text-sm font-bold flex items-center gap-2 transition-all whitespace-nowrap {{ request()->routeIs('delivery.scan') ? 'bg-pink-600 text-white shadow' : 'text-slate-400 hover:text-white hover:bg-slate-700' }}">
                    <i class="fas fa-motorcycle"></i> <span>Repartidor</span>
                </a>
            </div>

            {{-- PERFIL DE ESCRITORIO --}}
            <div class="hidden md:flex items-center gap-4">
                @auth
                    <div class="flex items-center gap-3 border-r border-slate-700 pr-4 mr-1">
                        <span class="text-sm font-bold text-slate-300">{{ Auth::user()->name }}</span>
                        @if(Auth::user()->avatar_url)
                            <img src="{{ Auth::user()->avatar_url }}" class="h-8 w-8 rounded-full border border-slate-600 object-cover bg-slate-800">
                        @else
                            <div class="h-8 w-8 rounded-full bg-slate-700 flex items-center justify-center border border-slate-600 text-xs font-bold text-white">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                        @endif
                    </div>
                    
                    {{-- NUEVO: Botón Perfil Desktop --}}
                    <a href="{{ route('profile.edit') }}" class="text-slate-400 hover:text-orange-500 transition" title="Mi Perfil">
                        <i class="fas fa-user-circle text-xl"></i>
                    </a>

                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="text-slate-400 hover:text-red-500 transition" title="Salir">
                            <i class="fas fa-sign-out-alt text-xl"></i> 
                        </button>
                    </form>
                @endauth
            </div>

            {{-- BOTÓN HAMBURGUESA --}}
            <button id="mobile-menu-btn" class="md:hidden text-slate-300 hover:text-white focus:outline-none p-2 rounded-lg border border-slate-700 bg-slate-800">
                <i class="fas fa-bars text-xl"></i>
            </button>
        </div>

        {{-- MENÚ DESPLEGABLE MÓVIL --}}
        <div id="mobile-menu" class="absolute top-16 left-0 w-full bg-slate-900 border-b border-slate-700 shadow-2xl md:hidden z-40">
            <div class="p-4 space-y-2">
                <a href="{{ route('employee.pos') }}" class="block px-4 py-3 rounded-lg font-bold flex items-center gap-3 {{ request()->routeIs('employee.pos') ? 'bg-blue-600 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}"><i class="fas fa-cash-register w-6 text-center"></i> Venta</a>
                <a href="{{ route('employee.orders.index') }}" class="block px-4 py-3 rounded-lg font-bold flex items-center gap-3 {{ request()->routeIs('employee.orders.index') ? 'bg-violet-600 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}"><i class="fas fa-wallet w-6 text-center"></i> Caja</a>
                <a href="{{ route('kitchen.live') }}" class="block px-4 py-3 rounded-lg font-bold flex items-center gap-3 {{ request()->routeIs('kitchen.live') ? 'bg-orange-600 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}"><i class="fas fa-fire w-6 text-center"></i> Cocina</a>
                <a href="{{ route('stock.index') }}" class="block px-4 py-3 rounded-lg font-bold flex items-center gap-3 {{ request()->routeIs('stock.index') ? 'bg-emerald-600 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}"><i class="fas fa-boxes w-6 text-center"></i> Stock</a>
                <a href="{{ route('delivery.scan') }}" class="block px-4 py-3 rounded-lg font-bold flex items-center gap-3 {{ request()->routeIs('delivery.scan') ? 'bg-pink-600 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}"><i class="fas fa-motorcycle w-6 text-center"></i> Repartidor</a>

                @auth
                <div class="border-t border-slate-700 mt-4 pt-4">
                    <div class="flex items-center gap-3 px-4 mb-4">
                        <img src="{{ Auth::user()->avatar_url }}" class="h-10 w-10 rounded-full border border-slate-600 object-cover">
                        <div>
                            <p class="text-white font-bold">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-slate-400 uppercase">Empleado</p>
                        </div>
                    </div>
                    
                    {{-- NUEVO: Botón Perfil Móvil --}}
                    <a href="{{ route('profile.edit') }}" class="w-full text-left px-4 py-3 rounded-lg text-slate-400 hover:bg-slate-800 hover:text-white font-bold flex items-center gap-3 transition">
                        <i class="fas fa-user-circle w-6 text-center"></i> Mi Perfil
                    </a>

                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-3 rounded-lg text-red-400 hover:bg-red-500/10 hover:text-red-300 font-bold flex items-center gap-3 transition">
                            <i class="fas fa-sign-out-alt w-6 text-center"></i> Cerrar Sesión
                        </button>
                    </form>
                </div>
                @endauth
            </div>
        </div>
    </nav>

    <main class="flex-1 overflow-hidden relative bg-slate-900">
        @yield('contenido')
    </main>
    
    @yield('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const btn = document.getElementById('mobile-menu-btn');
            const menu = document.getElementById('mobile-menu');
            if(btn && menu) {
                btn.addEventListener('click', () => {
                    menu.classList.toggle('open');
                    const icon = btn.querySelector('i');
                    icon.classList.toggle('fa-bars');
                    icon.classList.toggle('fa-times');
                });
                document.addEventListener('click', (e) => {
                    if (!menu.contains(e.target) && !btn.contains(e.target) && menu.classList.contains('open')) {
                        menu.classList.remove('open');
                        btn.querySelector('i').classList.replace('fa-times', 'fa-bars');
                    }
                });
            }
        });
    </script>
</body>
</html>