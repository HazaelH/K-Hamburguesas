<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('titulo', __('layouts/admin.admin_panel')) - {{ __('layouts/admin.k_hamburguesas') }}</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lipis/flag-icons@7.0.0/css/flag-icons.min.css"/>
    
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #0f172a; color: #e2e8f0; }
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #1e293b; }
        ::-webkit-scrollbar-thumb { background: #475569; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #64748b; }
        .sidebar-transition { transition: transform 0.3s ease-in-out, width 0.3s ease-in-out; }
    </style>

    <script>
        // Configuración Global y Traducciones para K-ADMIN
        window.K_ADMIN_CONFIG = {
            checkAlertsUrl: "{{ route('admin.alerts.check') }}",
            locale: "{{ app()->getLocale() }}",
            lang: {
                reject: "{{ __('layouts/admin.reject') }}",
                approve: "{{ __('layouts/admin.approve') }}",
                all_good_title: "{{ __('layouts/admin.js_all_good_title') }}",
                all_good_desc: "{{ __('layouts/admin.js_all_good_desc') }}",
                kitchen_request: "{{ __('layouts/admin.js_kitchen_request') }}",
                stock_request: "{{ __('layouts/admin.js_stock_request') }}",
                approve_change: "{{ __('layouts/admin.js_approve_change') }}",
                empty_sos_reply: "{{ __('layouts/admin.js_empty_sos_reply') }}",
                sending: "{{ __('layouts/admin.js_sending') }}",
                error_sending: "{{ __('layouts/admin.js_error_sending') }}",
                alert_cancel: "{{ __('layouts/admin.js_alert_cancel') }}",
                alert_stock: "{{ __('layouts/admin.js_alert_stock') }}",
                alert_sos: "{{ __('layouts/admin.js_alert_sos') }}",
                click_to_resolve: "{{ __('layouts/admin.js_click_to_resolve') }}"
            }
        };
    </script>
</head>

<body class="h-screen flex overflow-hidden bg-slate-900">

    <aside id="sidebar" class="sidebar-transition fixed inset-y-0 left-0 z-50 w-64 bg-slate-800 border-r border-slate-300 transform -translate-x-full md:translate-x-0 md:static md:inset-auto flex flex-col">
        <div class="h-16 flex items-center justify-center border-b border-slate-300">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 font-bold text-xl text-white tracking-wider">
                <div class="bg-orange-700 p-1.5 rounded rotate-3">
                    <i class="fas fa-hamburger text-white"></i>
                </div>
                <span>K-ADMIN</span>
            </a>
        </div>

        <div class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-3 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-orange-700 text-white shadow-lg shadow-orange-900/50' : 'text-slate-400 hover:bg-slate-700 hover:text-white' }}">
                <i class="fas fa-fw fa-tachometer-alt w-5"></i><span>{{ __('layouts/admin.dashboard') }}</span>
            </a>

            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mt-4 mb-2 px-3">{{ __('layouts/admin.management') }}</div>

            <a href="{{ route('admin.offers.index') }}" class="flex items-center gap-3 px-3 py-3 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.offers.*') ? 'bg-slate-700 text-white border-l-4 border-orange-500' : 'text-slate-400 hover:bg-slate-700 hover:text-white' }}">
                <i class="fas fa-fw fa-tags w-5"></i><span>{{ __('layouts/admin.offers') }}</span>
            </a>

            <a href="{{ route('admin.orders.index') }}" class="flex items-center gap-3 px-3 py-3 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.orders.*') ? 'bg-slate-700 text-white border-l-4 border-blue-500' : 'text-slate-400 hover:bg-slate-700 hover:text-white' }}">
                <i class="fas fa-fw fa-receipt w-5"></i><span>{{ __('layouts/admin.order_history') }}</span>
            </a>

            <a href="{{ route('admin.products.index') }}" class="flex items-center gap-3 px-3 py-3 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.products.*') ? 'bg-slate-700 text-white border-l-4 border-emerald-500' : 'text-slate-400 hover:bg-slate-700 hover:text-white' }}">
                <i class="fas fa-fw fa-utensils w-5"></i><span>{{ __('layouts/admin.products') }}</span>
            </a>

            <a href="{{ route('admin.categorias.index') }}" class="flex items-center gap-3 px-3 py-3 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.categorias.*') ? 'bg-slate-700 text-white border-l-4 border-emerald-500' : 'text-slate-400 hover:bg-slate-700 hover:text-white' }}">
                <i class="fas fa-fw fa-layer-group w-5"></i><span>{{ __('layouts/admin.categorias') }}</span>
            </a>
            
            <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-3 py-3 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.users.*') ? 'bg-slate-700 text-white border-l-4 border-purple-500' : 'text-slate-400 hover:bg-slate-700 hover:text-white' }}">
                <i class="fas fa-fw fa-users w-5"></i><span>{{ __('layouts/admin.users') }}</span>
            </a>
        </div>

        <div class="p-4 border-t border-slate-300 space-y-2">
            <a href="{{ route('profile.edit') }}" class="w-full flex items-center justify-center gap-2 bg-slate-700 hover:bg-slate-600 text-slate-200 py-2 rounded-lg transition text-sm font-bold border border-slate-600">
                <i class="fas fa-user-circle"></i> {{ __('layouts/admin.my_profile') }}
            </a>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" aria-label="Cerrar sesión" class="w-full flex items-center justify-center gap-2 bg-slate-900 hover:bg-red-900/30 text-slate-400 hover:text-red-400 py-2 rounded-lg transition text-sm font-bold border border-slate-300">
                    <i class="fas fa-sign-out-alt"></i> {{ __('layouts/admin.logout') }}
                </button>
            </form>
        </div>
    </aside>

    <div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-40 hidden md:hidden glass"></div>

    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        
        <header class="h-16 bg-slate-800/80 backdrop-blur-md border-b border-slate-300 flex items-center justify-between px-4 sm:px-6 z-30">
            
            <button id="sidebar-toggle" aria-label="Abrir menú lateral" class="md:hidden text-slate-400 hover:text-white focus:outline-none">
                <i class="fas fa-bars text-xl"></i>
            </button>

            <div id="reloj-en-vivo" class="hidden md:block text-slate-400 text-sm font-mono tracking-wide">
                {{ __('layouts/admin.loading_time') }}
            </div>

            <div class="flex items-center gap-6">
                
                {{-- MENU DE IDIOMAS (BANDERAS) --}}
                <div class="relative">
                    <button id="lang-btn-admin" aria-label="Cambiar idioma" class="flex items-center gap-1 text-xl hover:scale-110 transition-transform bg-slate-700/50 p-2 rounded-lg border border-slate-600 focus:outline-none">
                        @if(app()->getLocale() == 'es') <span class="fi fi-mx rounded"></span>
                        @elseif(app()->getLocale() == 'en') <span class="fi fi-us rounded"></span>
                        @elseif(app()->getLocale() == 'pt') <span class="fi fi-br rounded"></span>
                        @endif
                    </button>
                    
                    <div id="lang-dropdown-admin" class="absolute right-0 mt-2 w-40 bg-slate-800 border border-slate-300 rounded-xl shadow-2xl hidden z-50 overflow-hidden">
                        <div class="p-2 space-y-1">
                            <a href="{{ LaravelLocalization::getLocalizedURL('es', null, [], true) }}" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-slate-700 text-sm font-bold {{ app()->getLocale() == 'es' ? 'text-white bg-slate-700' : 'text-slate-400' }}">
                                <span class="fi fi-mx rounded shadow-sm"></span> Español
                            </a>
                            <a href="{{ LaravelLocalization::getLocalizedURL('en', null, [], true) }}" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-slate-700 text-sm font-bold {{ app()->getLocale() == 'en' ? 'text-white bg-slate-700' : 'text-slate-400' }}">
                                <span class="fi fi-us rounded shadow-sm"></span> English
                            </a>
                            <a href="{{ LaravelLocalization::getLocalizedURL('pt', null, [], true) }}" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-slate-700 text-sm font-bold {{ app()->getLocale() == 'pt' ? 'text-white bg-slate-700' : 'text-slate-400' }}">
                                <span class="fi fi-br rounded shadow-sm"></span> Português
                            </a>
                        </div>
                    </div>
                </div>

                <button aria-label="Abrir panel de historial" onclick="document.getElementById('historial-panel').classList.toggle('translate-x-full')" class="text-slate-400 hover:text-white transition" title="{{ __('layouts/admin.validation_history') }}">
                    <i class="fas fa-history text-xl"></i>
                </button>

                <div class="relative">
                    <button aria-label="Abrir panel de notificaciones" onclick="toggleCentroNotificaciones()" class="relative text-slate-400 hover:text-white transition group">
                        <i class="fas fa-bell text-xl group-hover:animate-bounce"></i>
                        <span id="admin-alert-badge" class="absolute -top-1.5 -right-2 bg-red-500 text-white text-[10px] font-bold w-4 h-4 rounded-full flex items-center justify-center hidden shadow-[0_0_10px_rgba(239,68,68,0.8)]">0</span>
                    </button>

                    <div id="notificaciones-dropdown" class="absolute right-0 mt-4 w-80 bg-slate-800 border border-slate-300 rounded-2xl shadow-2xl hidden z-50 overflow-hidden transform transition-all origin-top-right scale-95 opacity-0">
                        <div class="bg-slate-900 px-4 py-3 border-b border-slate-300 flex justify-between items-center">
                            <span class="font-bold text-white text-sm">{{ __('layouts/admin.pending_requests') }}</span>
                            <span id="notificaciones-count" class="bg-red-500 text-white text-[10px] px-2 py-0.5 rounded-full font-bold">0</span>
                        </div>
                        <div id="notificaciones-lista" class="max-h-80 overflow-y-auto custom-scrollbar bg-slate-800">
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3 border-l border-slate-300 pl-6">
                    <div class="text-right hidden sm:block">
                        <span class="block text-sm font-bold text-white">{{ Auth::user()->name }}</span>
                        <span class="block text-xs text-orange-500 font-bold uppercase">{{ __('layouts/admin.administrator') }}</span>
                    </div>
                    <img class="h-9 w-9 rounded-full border border-slate-600 object-cover" src="{{ Auth::user()->avatar_url }}" alt="Avatar de {{ Auth::user()->name }}">
                </div>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 bg-slate-900 scroll-smooth">
            @yield('contenido')
        </main>

        <footer class="bg-slate-800 border-t border-slate-300 py-4 px-6 text-center text-xs text-slate-400">
            &copy; {{ date('Y') }} {{ __('layouts/admin.k_hamburguesas') }} {{ __('layouts/admin.copyright') }}
        </footer>
    </div>

    {{-- MODAL DE AUTORIZACIÓN ADMIN --}}
    <div id="admin-auth-modal" class="fixed inset-0 z-[100] hidden flex items-center justify-center bg-black/80 backdrop-blur-sm px-4">
        <div class="bg-slate-900 border border-slate-300 p-6 rounded-3xl shadow-2xl w-full max-w-sm transform scale-95 transition-all opacity-0 flex flex-col max-h-[90vh]" id="admin-auth-panel">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-red-500/20 border border-red-500/50 mb-3 shrink-0">
                <i class="fas fa-shield-alt text-red-500 text-2xl"></i>
            </div>
            <h3 class="text-xl font-black text-center text-white mb-1 tracking-tight">{{ __('layouts/admin.kitchen_request') }}</h3>
            <p class="text-sm text-center text-slate-400 mb-4">{{ __('layouts/admin.cook_requests_cancel') }} <span class="text-white font-bold" id="admin-auth-order">{{ __('layouts/admin.order_number') }}...</span></p>
            
            {{-- NUEVO: CAJA PARA MOSTRAR EL MOTIVO --}}
            <div class="bg-slate-800 p-4 rounded-xl border border-slate-300 mb-5 relative overflow-hidden shrink-0 hidden" id="admin-auth-motivo-box">
                <div class="absolute top-0 left-0 w-1 h-full bg-red-500"></div>
                <p class="text-[10px] text-red-400 font-bold uppercase tracking-widest mb-1.5 flex items-center gap-1"><i class="fas fa-comment-dots"></i> Detalle / Motivo</p>
                <p class="text-sm text-slate-200 italic font-medium leading-relaxed" id="admin-auth-mensaje">"..."</p>
            </div>
            
            <input type="password" id="admin-pin-input" placeholder="••••" maxlength="4" autocomplete="off" class="w-full bg-slate-800 border-2 border-slate-300 text-white rounded-xl py-4 text-center text-3xl tracking-[1em] font-mono focus:outline-none focus:border-red-500 transition-all shadow-inner mb-2 placeholder:tracking-normal placeholder:text-slate-600 shrink-0">
            <p id="admin-pin-error" class="text-red-500 text-xs text-center font-bold h-4 mb-4 hidden shrink-0">{{ __('layouts/admin.incorrect_pin') }}</p>
            
            <div class="flex gap-3 shrink-0">
                <button onclick="resolverAlerta('rechazar')" class="flex-1 bg-slate-800 hover:bg-slate-700 text-white font-bold py-3.5 rounded-xl transition-all text-sm">{{ __('layouts/admin.reject') }}</button>
                <button onclick="resolverAlerta('aprobar')" class="flex-1 bg-red-600 hover:bg-red-500 text-white font-bold py-3.5 rounded-xl shadow-lg shadow-red-900/50 transition-all text-sm">{{ __('layouts/admin.approve') }}</button>
            </div>
        </div>
    </div>

    {{-- MODAL DE SOPORTE S.O.S (REPARTIDORES) --}}
    <div id="admin-sos-modal" class="fixed inset-0 z-[100] hidden flex items-center justify-center bg-black/80 backdrop-blur-sm px-4">
        <div class="bg-slate-900 border border-slate-300 p-6 rounded-3xl shadow-2xl w-full max-w-md transform scale-95 transition-all opacity-0 flex flex-col max-h-[90vh]" id="admin-sos-panel">
            
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-blue-500/20 border border-blue-500/50 mb-3 shrink-0">
                <i class="fas fa-headset text-blue-500 text-2xl"></i>
            </div>
            
            <h3 class="text-xl font-black text-center text-white mb-1 tracking-tight">{{ __('layouts/admin.support_en_route') }}</h3>
            <p class="text-sm text-center text-slate-400 mb-5">{{ __('layouts/admin.attending_order') }} <span class="text-white font-bold" id="admin-sos-order">{{ __('layouts/admin.order_number') }}...</span></p>
            
            <div class="bg-slate-800 p-4 rounded-xl border border-slate-300 mb-5 shrink-0 relative overflow-hidden">
                <div class="absolute top-0 left-0 w-1 h-full bg-orange-500"></div>
                <p class="text-[10px] text-orange-400 font-bold uppercase tracking-widest mb-1.5 flex items-center gap-1"><i class="fas fa-motorcycle"></i> {{ __('layouts/admin.driver_report') }}</p>
                <p class="text-sm text-slate-200 italic font-medium leading-relaxed" id="admin-sos-mensaje">"..."</p>
            </div>
            
            <div class="flex-1 min-h-0 flex flex-col">
                <label class="block text-[10px] font-bold text-blue-400 mb-2 uppercase tracking-widest"><i class="fas fa-reply"></i> {{ __('layouts/admin.admin_instruction') }}</label>
                <textarea id="admin-sos-respuesta" rows="3" placeholder="{{ __('layouts/admin.placeholder_instruction') }}" class="w-full flex-1 min-h-[80px] bg-slate-950 border border-slate-300 text-white rounded-xl p-3 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all mb-5 resize-none custom-scrollbar"></textarea>
            </div>
            
            <div class="flex gap-3 shrink-0">
                <button onclick="cerrarModalSOSAdmin()" class="flex-1 bg-slate-800 hover:bg-slate-700 text-white font-bold py-3.5 rounded-xl transition-all text-sm">{{ __('layouts/admin.close') }}</button>
                <button onclick="enviarResolucionSOS()" id="btn-resolver-sos" class="flex-1 bg-blue-600 hover:bg-blue-500 text-white font-bold py-3.5 rounded-xl shadow-lg shadow-blue-900/50 transition-all flex justify-center items-center gap-2 text-sm">
                    {{ __('layouts/admin.send_instruction') }} <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </div>
    </div>

    {{-- PANEL LATERAL DE HISTORIAL DE VALIDACIONES --}}
    <div id="historial-panel" class="fixed inset-y-0 right-0 z-[60] w-80 bg-slate-800 border-l border-slate-300 shadow-2xl transform translate-x-full transition-transform duration-300 flex flex-col">
        <div class="h-16 flex items-center justify-between px-4 border-b border-slate-300 bg-slate-900">
            <h2 class="text-white font-bold flex items-center gap-2">
                <i class="fas fa-clipboard-check text-emerald-500"></i> {{ __('layouts/admin.validation_history') }}
            </h2>
            <button aria-label="Cerrar panel de historial" onclick="document.getElementById('historial-panel').classList.add('translate-x-full')" class="text-slate-400 hover:text-white">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto p-4 space-y-4 custom-scrollbar" id="historial-lista">
            @php
                $logs = \App\Models\AuditLog::latest()->take(15)->get();
            @endphp

            @forelse($logs as $log)
                <div class="bg-slate-900 p-3 rounded-lg border-l-4 {{ $log->accion == 'APROBÓ' ? 'border-emerald-500' : 'border-red-500' }}">
                    <div class="flex justify-between items-start mb-1">
                        <span class="text-xs font-bold {{ $log->accion == 'APROBÓ' ? 'text-emerald-500' : 'text-red-500' }}">
                            {{ $log->accion == 'APROBÓ' ? __('layouts/admin.approve') : __('layouts/admin.reject') }}
                        </span>
                        <span class="text-[10px] text-slate-400">{{ $log->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="text-sm text-white mb-2">{{ $log->detalle }}</p>
                    <div class="text-[10px] text-slate-400 flex justify-between">
                        <span><i class="fas fa-user-shield"></i> {{ $log->admin_name }}</span>
                        <span><i class="fas fa-user-tie"></i> {{ __('layouts/admin.requested_by') }} {{ $log->empleado_name }}</span>
                    </div>
                </div>
            @empty
                <div class="text-center text-slate-400 py-8">
                    <i class="fas fa-folder-open text-3xl mb-2 opacity-50"></i>
                    <p class="text-sm">{{ __('layouts/admin.no_recent_validations') }}</p>
                </div>
            @endforelse
        </div>
    </div>

    @vite(['resources/js/admin/notifications.js'])
    
    <script>
        // Script para el menú de idiomas de Admin
        document.addEventListener('DOMContentLoaded', () => {
            const langBtn = document.getElementById('lang-btn-admin');
            const langDropdown = document.getElementById('lang-dropdown-admin');
            
            if(langBtn && langDropdown) {
                langBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    langDropdown.classList.toggle('hidden');
                });
                
                document.addEventListener('click', (e) => {
                    if (!langBtn.contains(e.target) && !langDropdown.contains(e.target)) {
                        langDropdown.classList.add('hidden');
                    }
                });
            }
        });
    </script>
    
    @stack('scripts')
</body>
</html>