<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('titulo', 'Admin Panel') - K-Hamburguesas</title>

    {{-- Tailwind y FontAwesome --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    {{-- Estilos específicos del Admin --}}
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #0f172a; color: #e2e8f0; }
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #1e293b; }
        ::-webkit-scrollbar-thumb { background: #475569; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #64748b; }
        .sidebar-transition { transition: transform 0.3s ease-in-out, width 0.3s ease-in-out; }
    </style>
</head>

<body class="h-screen flex overflow-hidden bg-slate-900">

    {{-- 1. SIDEBAR --}}
    <aside id="sidebar" class="sidebar-transition fixed inset-y-0 left-0 z-50 w-64 bg-slate-800 border-r border-slate-700 transform -translate-x-full md:translate-x-0 md:static md:inset-auto flex flex-col">
        
        {{-- Brand --}}
        <div class="h-16 flex items-center justify-center border-b border-slate-700">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 font-bold text-xl text-white tracking-wider">
                <div class="bg-orange-600 p-1.5 rounded rotate-3">
                    <i class="fas fa-hamburger text-white"></i>
                </div>
                <span>K-ADMIN</span>
            </a>
        </div>

        {{-- Menú --}}
        <div class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
            <a href="{{ route('admin.dashboard') }}" 
               class="flex items-center gap-3 px-3 py-3 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-orange-600 text-white shadow-lg shadow-orange-900/50' : 'text-slate-400 hover:bg-slate-700 hover:text-white' }}">
                <i class="fas fa-fw fa-tachometer-alt w-5"></i>
                <span>Dashboard</span>
            </a>

            <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mt-4 mb-2 px-3">Gestión</div>

            <a href="{{ route('admin.offers.index') }}" 
               class="flex items-center gap-3 px-3 py-3 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.offers.*') ? 'bg-slate-700 text-white border-l-4 border-orange-500' : 'text-slate-400 hover:bg-slate-700 hover:text-white' }}">
                <i class="fas fa-fw fa-tags w-5"></i>
                <span>Ofertas</span>
            </a>

            <a href="{{ route('admin.products.index') }}" class="flex items-center gap-3 px-3 py-3 rounded-lg text-sm font-medium text-slate-400 hover:bg-slate-700 hover:text-white transition-colors">
                <i class="fas fa-fw fa-utensils w-5"></i>
                <span>Productos</span>
            </a>
            
            <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-3 py-3 rounded-lg text-sm font-medium text-slate-400 hover:bg-slate-700 hover:text-white transition-colors">
                <i class="fas fa-fw fa-users w-5"></i>
                <span>Usuarios</span>
            </a>
        </div>

        {{-- Footer Sidebar --}}
        <div class="p-4 border-t border-slate-700 space-y-2">
            
            {{-- NUEVO: Botón Perfil --}}
            <a href="{{ route('profile.edit') }}" class="w-full flex items-center justify-center gap-2 bg-slate-700 hover:bg-slate-600 text-slate-200 py-2 rounded-lg transition text-sm font-bold border border-slate-600">
                <i class="fas fa-user-circle"></i> Mi Perfil
            </a>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="w-full flex items-center justify-center gap-2 bg-slate-900 hover:bg-red-900/30 text-slate-400 hover:text-red-400 py-2 rounded-lg transition text-sm font-bold border border-slate-700">
                    <i class="fas fa-sign-out-alt"></i> Salir
                </button>
            </form>
        </div>
    </aside>

    {{-- Overlay móvil --}}
    <div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-40 hidden md:hidden glass"></div>

    {{-- 2. CONTENT WRAPPER --}}
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

        {{-- TOPBAR --}}
        <header class="h-16 bg-slate-800/80 backdrop-blur-md border-b border-slate-700 flex items-center justify-between px-4 sm:px-6 z-30">
            <button id="sidebar-toggle" class="md:hidden text-slate-400 hover:text-white focus:outline-none">
                <i class="fas fa-bars text-xl"></i>
            </button>

            <div class="hidden md:block text-slate-400 text-sm font-mono">
                {{ now()->format('l, d F Y') }}
            </div>

            <div class="flex items-center gap-4">
                <div class="flex items-center gap-3">
                    <div class="text-right hidden sm:block">
                        <span class="block text-sm font-bold text-white">{{ Auth::user()->name }}</span>
                        <span class="block text-xs text-orange-500 font-bold uppercase">Administrador</span>
                    </div>
                    <img class="h-9 w-9 rounded-full border border-slate-600 object-cover" 
                        src="{{ Auth::user()->avatar_url }}" 
                        alt="{{ Auth::user()->name }}">
                </div>
            </div>
        </header>

        {{-- MAIN CONTENT --}}
        <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 bg-slate-900 scroll-smooth">
            @yield('contenido')
        </main>

        {{-- FOOTER --}}
        <footer class="bg-slate-800 border-t border-slate-700 py-4 px-6 text-center text-xs text-slate-500">
            &copy; {{ date('Y') }} K-Hamburguesas Admin System.
        </footer>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toggleBtn = document.getElementById('sidebar-toggle');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');

            function toggleSidebar() {
                sidebar.classList.toggle('-translate-x-full');
                overlay.classList.toggle('hidden');
            }

            if(toggleBtn) {
                toggleBtn.addEventListener('click', toggleSidebar);
                overlay.addEventListener('click', toggleSidebar);
            }
        });
    </script>
    @stack('scripts')
</body>
</html>