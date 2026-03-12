<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="stripe-key" content="{{ env('STRIPE_KEY') }}">
    <title>@yield('titulo') - {{ __('layouts/app.k_hamburguesas') }}</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lipis/flag-icons@7.0.0/css/flag-icons.min.css"/>
    
    @vite([
        'resources/css/app.css', 
        'resources/js/app.js',
        'resources/css/layout.css', 
        'resources/js/layout.js',
        'resources/js/client/cart.js'
    ])
</head>
<body class="antialiased min-h-screen flex flex-col">

    <nav class="sticky top-0 z-50 w-full bg-slate-900/95 backdrop-blur-md border-b border-white/5 shadow-lg shadow-black/20">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex h-20 justify-between items-center">
                
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center gap-3 hover:opacity-90 transition group">
                        <div class="bg-orange-700 p-2.5 rounded-xl shadow-lg shadow-orange-900/50 group-hover:scale-105 transition-transform duration-300">
                            <i class="fas fa-utensils text-white text-xl"></i>
                        </div>
                        <div class="hidden sm:block">
                            <span class="font-bold text-xl tracking-wide text-white block leading-none">{{ strtoupper(__('layouts/app.k_hamburguesas')) }}</span>
                            <span class="text-[10px] text-orange-500 block font-mono uppercase tracking-[0.25em] mt-1 font-bold">{{ __('layouts/app.restaurant') }}</span>
                        </div>
                    </a>
                </div>

                @if(!request()->routeIs('login') && !request()->routeIs('register'))

                    <div class="hidden md:flex items-center space-x-8">
                        <a href="{{ route('home') }}" class="text-sm font-bold text-white hover:text-orange-400 transition-colors border-b-2 border-transparent hover:border-orange-500 py-1">
                            <i class="fas fa-home w-6 text-center text-slate-500"></i>{{ __('layouts/app.home') }}
                        </a>
                        <a href="{{ route('menu') }}" class="text-sm font-bold text-slate-300 hover:text-orange-400 transition-colors border-b-2 border-transparent hover:border-orange-500 py-1">
                            <i class="fas fa-list-ul w-6 text-center text-slate-500"></i>{{ __('layouts/app.menu') }}
                        </a>
                        <a href="{{ route('offers.index') }}" class="block px-3 py-3 rounded-md text-base font-bold text-slate-300 hover:bg-slate-700 hover:text-orange-400 transition">
                            <i class="fas fa-tag w-6 text-center text-slate-500"></i> {{ __('layouts/app.offers_client') }}
                        </a>
                    </div>

                    <div class="flex items-center gap-4">
                        <a href="{{ route('cart.index') }}" class="relative p-2 text-slate-300 hover:text-white transition-colors group">
                            <i class="fas fa-shopping-cart text-xl group-hover:animate-wiggle"></i>
                            <span id="cart-count" class="absolute top-0 right-0 -mt-1 -mr-1 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white animate-pulse">
                                {{ count(session('cart', [])) }}
                            </span>
                        </a>

                        <div class="hidden md:block h-6 w-px bg-slate-700"></div>

                        <div class="hidden md:flex items-center gap-4">
                            @auth
                                <div class="flex items-center gap-3">
                                    <div class="text-right hidden lg:block">
                                        <p class="text-sm font-bold text-white leading-none">{{ Auth::user()->name }}</p>
                                        <p class="text-[10px] text-orange-400 uppercase font-bold">{{ Auth::user()->rol }}</p>
                                    </div>
                                    
                                    <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}" class="h-10 w-10 rounded-full border border-slate-600 object-cover bg-slate-800 shadow-inner">

                                    <a href="{{ route('profile.edit') }}" class="text-slate-400 hover:text-orange-400 transition p-2" title="{{ __('layouts/app.my_profile') }}">
                                        <i class="fas fa-user-circle text-lg"></i>
                                    </a>

                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="text-slate-400 hover:text-red-500 transition p-2" title="{{ __('layouts/app.logout') }}">
                                            <i class="fas fa-sign-out-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            @else
                                <a href="{{ route('login') }}" class="text-sm font-bold text-slate-300 hover:text-white">{{ __('layouts/app.login') }}</a>
                                <a href="{{ route('register') }}" class="bg-orange-700 hover:bg-orange-700 text-white text-sm font-bold px-5 py-2 rounded-full shadow-lg shadow-orange-900/20 transition-transform hover:-translate-y-0.5">{{ __('layouts/app.register') }}</a>
                            @endauth
                        </div>
                        
                        {{-- MENU DE IDIOMAS APP (BANDERAS) --}}
                        <div class="relative border-l border-slate-300 pl-4 ml-2">
                            <button id="lang-btn-admin" class="flex items-center gap-1 text-xl hover:scale-110 transition-transform bg-slate-700/50 p-2 rounded-lg border border-slate-600 focus:outline-none">
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

                        <div class="md:hidden">
                            <button id="mobile-menu-btn" class="text-slate-300 hover:text-white p-2 focus:outline-none">
                                <i class="fas fa-bars text-2xl" id="menu-icon"></i>
                            </button>
                        </div>
                    </div>
                @else
                    <div class="flex items-center gap-4">
                        <a href="{{ route('home') }}" class="text-sm font-bold text-slate-300 hover:text-white hidden sm:block">
                            <i class="fas fa-arrow-left mr-1"></i> {{ __('layouts/app.back_to_home') }}
                        </a>
                        
                        {{-- MENU DE IDIOMAS (LOGIN/REGISTER) --}}
                        <div class="relative border-r border-slate-300 pr-4 mr-2">
                            <button id="lang-btn-auth" class="flex items-center gap-1 text-2xl hover:scale-110 transition-transform focus:outline-none">
                                @if(app()->getLocale() == 'es') 🇲🇽
                                @elseif(app()->getLocale() == 'en') 🇺🇸
                                @elseif(app()->getLocale() == 'pt') 🇧🇷
                                @endif
                            </button>
                            
                            <div id="lang-dropdown-auth" class="absolute right-0 mt-4 w-40 bg-slate-800 border border-slate-300 rounded-xl shadow-2xl hidden z-50 overflow-hidden">
                                <div class="p-2 space-y-1">
                                    <a href="{{ LaravelLocalization::getLocalizedURL('es', null, [], true) }}" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-slate-700 text-sm font-bold {{ app()->getLocale() == 'es' ? 'text-white bg-slate-700' : 'text-slate-400' }}">
                                        <span class="text-xl">🇲🇽</span> Español
                                    </a>
                                    <a href="{{ LaravelLocalization::getLocalizedURL('en', null, [], true) }}" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-slate-700 text-sm font-bold {{ app()->getLocale() == 'en' ? 'text-white bg-slate-700' : 'text-slate-400' }}">
                                        <span class="text-xl">🇺🇸</span> English
                                    </a>
                                    <a href="{{ LaravelLocalization::getLocalizedURL('pt', null, [], true) }}" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-slate-700 text-sm font-bold {{ app()->getLocale() == 'pt' ? 'text-white bg-slate-700' : 'text-slate-400' }}">
                                        <span class="text-xl">🇧🇷</span> Português
                                    </a>
                                </div>
                            </div>
                        </div>

                        @if(request()->routeIs('login'))
                            <a href="{{ route('register') }}" class="bg-slate-800 border border-slate-300 text-white text-sm font-bold px-4 py-2 rounded-lg hover:bg-slate-700 transition">{{ __('layouts/app.create_account') }}</a>
                        @endif
                        @if(request()->routeIs('register'))
                            <a href="{{ route('login') }}" class="bg-slate-800 border border-slate-300 text-white text-sm font-bold px-4 py-2 rounded-lg hover:bg-slate-700 transition">{{ __('layouts/app.sign_in') }}</a>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        {{-- Menú Móvil... (Igual al tuyo) --}}
        @if(!request()->routeIs('login') && !request()->routeIs('register'))
            <div id="mobile-menu" class="md:hidden max-h-0 overflow-hidden bg-slate-800 border-t border-slate-300">
                <div class="px-4 pt-2 pb-6 space-y-2">
                    <a href="{{ route('home') }}" class="block px-3 py-3 rounded-md text-base font-bold text-white hover:bg-slate-700 hover:text-orange-400 transition"><i class="fas fa-home w-6 text-center text-slate-500"></i> {{ __('layouts/app.home') }}</a>
                    <a href="{{ route('menu') }}" class="block px-3 py-3 rounded-md text-base font-bold text-slate-300 hover:bg-slate-700 hover:text-orange-400 transition"><i class="fas fa-hamburger w-6 text-center text-slate-500"></i> {{ __('layouts/app.full_menu') }}</a>
                    <a href="{{ route('offers.index') }}" class="block px-3 py-3 rounded-md text-base font-bold text-slate-300 hover:bg-slate-700 hover:text-orange-400 transition"><i class="fas fa-tag w-6 text-center text-slate-500"></i> {{ __('layouts/app.offers_client') }}</a>

                    <div class="border-t border-slate-300 my-2"></div>

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
                                    <i class="fas fa-briefcase mr-2"></i> {{ __('layouts/app.go_to_panel') }}
                                </a>
                            @endif

                            <a href="{{ route('profile.edit') }}" class="block w-full text-left px-3 py-2 rounded-md text-base font-medium text-slate-300 hover:bg-slate-700 hover:text-white">
                                <i class="fas fa-user-circle w-6 text-center"></i> {{ __('layouts/app.my_profile') }}
                            </a>

                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="block w-full text-left px-3 py-2 rounded-md text-base font-medium text-red-400 hover:bg-slate-700 hover:text-red-300">
                                    <i class="fas fa-sign-out-alt w-6 text-center"></i> {{ __('layouts/app.logout') }}
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="grid grid-cols-2 gap-4 px-3 mt-4">
                            <a href="{{ route('login') }}" class="text-center py-2 border border-slate-600 rounded-lg text-white font-bold hover:bg-slate-700">{{ __('layouts/app.login') }}</a>
                            <a href="{{ route('register') }}" class="text-center py-2 bg-orange-700 rounded-lg text-white font-bold hover:bg-orange-500">{{ __('layouts/app.register') }}</a>
                        </div>
                    @endauth
                </div>
            </div>
        @endif
    </nav>

    <main class="flex-1 container mx-auto p-4 md:p-6 fade-in">
        @yield('contenido')
    </main>

    

    {{-- FOOTER ACTUALIZADO --}}
    <footer class="bg-slate-900 border-t border-slate-800 pt-12 pb-6 text-sm text-slate-400">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 md:gap-12 mb-8">
                
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="bg-orange-700 p-2 rounded-lg shadow-lg shadow-orange-900/50">
                            <i class="fas fa-utensils text-white"></i>
                        </div>
                        <span class="font-bold text-lg text-white tracking-wide">{{ __('layouts/app.k_hamburguesas') }}</span>
                    </div>
                    <p class="leading-relaxed text-justify">
                        {{ __('layouts/app.footer_desc') }}
                    </p>
                </div>

                <div class="space-y-4">
                    <h3 class="text-white font-bold text-lg border-b border-slate-300 pb-2 inline-block">Contacto</h3>
                    <ul class="space-y-3">
                        <li class="flex items-start gap-3">
                            <i class="fas fa-map-marker-alt text-orange-500 mt-1"></i>
                            <div>
                                <span class="block font-bold text-slate-300">{{ __('layouts/app.footer_address_title') }}</span>
                                {{ __('layouts/app.footer_address') }}
                            </div>
                        </li>
                        <li class="flex items-center gap-3">
                            <i class="fas fa-phone-alt text-orange-500"></i>
                            <div>
                                <span class="block font-bold text-slate-300">{{ __('layouts/app.footer_phone_title') }}</span>
                                <a href="tel:+527251361324" class="hover:text-orange-400 transition-colors">{{ __('layouts/app.footer_phone') }}</a>
                            </div>
                        </li>
                    </ul>
                </div>

                <div class="space-y-4">
                    <h3 class="text-white font-bold text-lg border-b border-slate-300 pb-2 inline-block">{{ __('layouts/app.footer_hours_title') }}</h3>
                    <div class="bg-slate-800/50 rounded-xl p-4 border border-slate-300/50">
                        <div class="flex justify-between items-center mb-2">
                            <span class="font-bold text-slate-300"><i class="far fa-clock text-orange-500 mr-2"></i>{{ __('layouts/app.footer_mon_sun') }}</span>
                        </div>
                        <div class="text-orange-400 font-mono text-lg font-bold text-center bg-slate-900 py-2 rounded-lg">
                            {{ __('layouts/app.footer_hours') }}
                        </div>
                    </div>
                </div>

            </div>

            <div class="border-t border-slate-800 pt-6 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-xs">&copy; {{ date('Y') }} {{ __('layouts/app.k_hamburguesas') }}. {{ __('layouts/app.footer_slogan') }}</p>
                <div class="flex items-center gap-6 text-xs font-bold">
                    <a href="{{ route('terms') }}" class="hover:text-orange-500 transition-colors">{{ __('client/legal.terms_title') }}</a>
                    <a href="{{ route('privacy') }}" class="hover:text-orange-500 transition-colors">{{ __('client/legal.privacy_title') }}</a>
                </div>
            </div>
        </div>
    </footer>

    {{-- Script de Dropdowns de Idioma --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Lógica para botones de idioma App/Auth
            const setupLangDropdown = (btnId, dropId) => {
                const btn = document.getElementById(btnId);
                const drop = document.getElementById(dropId);
                if(btn && drop) {
                    btn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        drop.classList.toggle('hidden');
                    });
                    document.addEventListener('click', (e) => {
                        if (!btn.contains(e.target) && !drop.contains(e.target)) {
                            drop.classList.add('hidden');
                        }
                    });
                }
            };

            setupLangDropdown('lang-btn-app', 'lang-dropdown-app');
            setupLangDropdown('lang-btn-auth', 'lang-dropdown-auth');
        });
    </script>

    <script>
        window.K_TRANSLATIONS = {
            client: {
                cart: {
                    success_title: '{{ __("client/messages.js_success_title") }}',
                    success_desc: '{{ __("client/messages.js_success_desc") }}',
                    saving: '{{ __("client/messages.js_saving") }}',
                    preparing: '{{ __("client/messages.js_preparing") }}',
                    denied: '{{ __("client/messages.js_denied") }}',
                    connection_error: '{{ __("client/messages.js_connection_error") }}',
                    db_error: '{{ __("client/messages.js_db_error") }}'
                },
                tracker: {
                    on_the_way: '{{ __("client/ticket.on_the_way") }}',
                    on_the_way_desc: '{{ __("client/ticket.on_the_way_desc") }}',
                    outside: '{{ __("client/ticket.outside") }}',
                    outside_desc: '{{ __("client/ticket.outside_desc") }}',
                    view_ticket: '{{ __("client/ticket.menu") }}', 
                    close_alert: '{{ __("layouts/admin.close") }}' 
                }
            },
            checkout: {
                    processing: '{{ __("client/checkout.processing_payment") }}',
                    fill_all: '{{ __("client/checkout.fill_all_fields") }}'
            }
        };
    </script>
    <x-cookie-banner />
</body>
</html>
