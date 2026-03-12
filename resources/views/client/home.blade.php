@extends('layouts.app')

@section('titulo', __('client/home.title'))

@section('contenido')

    {{-- HERO SECTION --}}
    <section class="relative bg-slate-900 rounded-3xl overflow-hidden shadow-2xl mb-16 border border-slate-300">
        <div class="absolute inset-0">
            <img src="https://images.unsplash.com/photo-1550547660-d9450f859349?ixlib=rb-1.2.1&auto=format&fit=crop&w=1951&q=80" 
                 class="w-full h-full object-cover opacity-20" 
                 alt="Fondo Hamburguesa">
            <div class="absolute inset-0 bg-linear-to-r from-slate-900 via-slate-900/80 to-transparent"></div>
        </div>

        <div class="relative z-10 max-w-7xl mx-auto py-16 px-6 sm:px-12 flex flex-col md:flex-row items-center gap-10">
            <div class="md:w-3/5 space-y-6 animate-fade-in-down">
                <span class="text-orange-500 font-black tracking-widest uppercase text-xs px-3 py-1 bg-orange-500/10 rounded-full border border-orange-500/20">
                    {{ __('client/home.hero_subtitle') }}
                </span>

                <h1 class="text-4xl md:text-6xl font-black text-white leading-tight drop-shadow-lg">
                    {!! nl2br(e($mensajeBienvenida)) !!} 
                </h1>

                <p class="text-slate-300 text-lg max-w-lg leading-relaxed">
                    {{ __('client/home.hero_desc') }}
                </p>
                
                <div class="flex flex-wrap gap-4 pt-4">
                    <a href="{{ route('menu') }}" class="bg-orange-700 hover:bg-orange-700 text-white px-8 py-4 rounded-xl font-black transition-all transform hover:-translate-y-1 shadow-[0_0_20px_rgba(194,65,12,0.4)] flex items-center gap-2">
    			<i class="fas fa-utensils"></i> {{ __('client/home.btn_menu') }}
		</a>
                    
                    @auth
                        @if($ultimoPedido)
                            <a href="{{ route('ticket', $ultimoPedido->id) }}" class="bg-slate-800 hover:bg-slate-700 border border-slate-300 text-slate-300 px-8 py-4 rounded-xl font-bold transition flex items-center group">
                                <i class="fas fa-history mr-2 text-orange-500 group-hover:rotate-180 transition-transform"></i>
                                {{ __('client/home.btn_ticket') }}
                            </a>
                        @endif
                    @endauth
                </div>
            </div>

            <div class="md:w-2/5 relative hidden md:block">
                <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[300px] h-[300px] bg-orange-500/20 rounded-full blur-[100px] pointer-events-none"></div>
            </div>
        </div>
    </section>

    {{-- SECCIÓN: OFERTAS FLASH --}}
    @if($ofertas->count() > 0)
    <section class="mb-20 animate-fade-in-up">
        <div class="flex items-center justify-between mb-8 border-b border-slate-300 pb-4">
            <h2 class="text-3xl font-black text-white flex items-center gap-3">
                <i class="fas fa-bolt text-yellow-500 animate-pulse"></i> {{ __('client/home.special_offers') }}
            </h2>
            <a href="{{ route('offers.index') }}" class="text-orange-500 hover:text-orange-400 text-sm font-bold flex items-center transition-colors">
                {{ __('client/home.view_all') }} <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($ofertas as $oferta)
                <a href="{{ route('offers.index') }}" class="block relative bg-slate-800 rounded-3xl overflow-hidden group hover:-translate-y-2 transition-transform duration-300 shadow-xl border border-slate-300 hover:border-orange-500/50">
                    <div class="h-40 relative bg-slate-900">
                        @if($oferta->imagen_url)
                            <img src="{{ asset('storage/' . $oferta->imagen_url) }}" class="w-full h-full object-cover opacity-80 group-hover:opacity-100 group-hover:scale-110 transition duration-500">
                        @else
                            <div class="w-full h-full flex items-center justify-center"><i class="fas fa-percentage text-6xl text-slate-700"></i></div>
                        @endif
                        <div class="absolute inset-0 bg-linear-to-t from-slate-900 to-transparent"></div>
                    </div>
                    <div class="p-5 relative -mt-12 z-10">
                        <span class="bg-red-600 text-white text-xs font-black px-3 py-1 rounded-lg shadow-lg -rotate-3 inline-block mb-2 border border-red-400">
                            {{ $oferta->porcentaje > 0 ? "-{$oferta->porcentaje}%" : __('client/offers.only') . ' ' . formatCurrency($oferta->precio_promo) }}
                        </span>
                        {{-- APLICANDO ACCESORES DE TRADUCCIÓN --}}
                        <h2 class="text-xl font-bold text-white mb-1 group-hover:text-orange-400 transition-colors">{{ $oferta->titulo_traducido }}</h2>
                        <p class="text-slate-400 text-xs line-clamp-2">{{ $oferta->descripcion_traducida }}</p>
                    </div>
                </a>
            @endforeach
        </div>
    </section>
    @endif

    {{-- SECCIÓN: NUESTROS FAVORITOS --}}
    <section class="mb-20">
        <div class="flex items-center justify-between mb-8 border-b border-slate-300 pb-4">
            <h2 class="text-3xl font-black text-white flex items-center gap-3">
                <i class="fas fa-fire text-orange-500"></i> {{ __('client/home.favorites') }}
            </h2>
            <a href="{{ route('menu') }}" class="text-orange-500 hover:text-orange-400 text-sm font-bold flex items-center transition-colors">
                {{ __('client/home.go_to_menu') }} <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($platosPopulares as $producto)
                <div class="bg-slate-800 rounded-3xl overflow-hidden shadow-lg border border-slate-300 hover:border-orange-500/50 transition duration-300 group flex flex-col relative hover:-translate-y-1">
                    
                    <div class="h-48 overflow-hidden relative cursor-pointer" onclick="window.abrirModalHome({{ json_encode($producto) }}, '{{ app()->getLocale() }}')">
                        <img src="{{ asset('imagenes/' . $producto->imagen_url) }}" alt="{{ $producto->nombre_traducido }}" class="w-full h-full object-cover transform group-hover:scale-110 transition duration-700 opacity-90 group-hover:opacity-100">
                        <div class="absolute inset-0 bg-linear-to-t from-slate-900 via-transparent to-transparent opacity-80"></div>
                        
                        <div class="absolute bottom-3 right-3 flex flex-col items-end">
                            @if($producto->precio_final < $producto->precio)
                                <span class="bg-red-600 text-white text-[10px] font-black px-2 py-0.5 rounded-t-md shadow-md animate-pulse">{{ __('client/home.sale_badge') }}</span>
                                <div class="bg-orange-700 text-white font-black px-3 py-1 rounded-b-md rounded-tl-md text-sm shadow-md flex items-center gap-2">
                                    <span class="line-through text-orange-300 text-[10px]">{{ formatCurrency($producto->precio) }}</span>
                                    <span>{{ formatCurrency($producto->precio_final) }}</span>
                                </div>
                            @else
                                <div class="bg-orange-700 text-white font-black px-3 py-1 rounded-xl text-sm shadow-md">
                                    {{ formatCurrency($producto->precio) }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="p-5 flex-1 flex flex-col justify-between">
                        <div>
                            <span class="text-[10px] font-black text-orange-400 uppercase tracking-widest bg-orange-500/10 px-3 py-1 rounded-full border border-orange-500/20 mb-3 inline-block">
                                {{ $producto->categoria_traducida }}
                            </span>
                            <h2 class="text-lg font-black text-white mb-2 leading-tight cursor-pointer hover:text-orange-500 transition" onclick="window.abrirModalHome({{ json_encode($producto) }}, '{{ app()->getLocale() }}')">
                                {{ $producto->nombre_traducido }}
                            </h2>
                            <p class="text-slate-400 text-sm mb-4 line-clamp-2">
                                {{ $producto->descripcion_traducida }}
                            </p>
                        </div>
                        
                        <button onclick="window.abrirModalHome({{ json_encode($producto) }}, '{{ app()->getLocale() }}')" class="w-full bg-slate-700 hover:bg-orange-700 text-white font-bold py-3 rounded-xl transition-colors duration-300 flex items-center justify-center gap-2 mt-auto border border-slate-300 hover:border-orange-500">
                            <i class="fas fa-plus"></i> {{ __('client/home.add_to_order') }}
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- SECCIÓN: POR QUÉ ELEGIRNOS --}}
    <section class="mb-10 bg-slate-800/50 rounded-3xl p-8 sm:p-12 border border-slate-300">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
            <div>
                <div class="w-16 h-16 mx-auto bg-orange-500/10 rounded-2xl flex items-center justify-center text-orange-500 text-3xl mb-4 transform transition hover:-translate-y-2">
                    <i class="fas fa-hamburger"></i>
                </div>
                <h2 class="text-xl font-bold text-white mb-2">{{ __('client/home.quality_title') }}</h2>
                <p class="text-slate-400 text-sm">{{ __('client/home.quality_desc') }}</p>
            </div>
            <div>
                <div class="w-16 h-16 mx-auto bg-blue-500/10 rounded-2xl flex items-center justify-center text-blue-500 text-3xl mb-4 transform transition hover:-translate-y-2">
                    <i class="fas fa-motorcycle"></i>
                </div>
                <h2 class="text-xl font-bold text-white mb-2">{{ __('client/home.delivery_title') }}</h2>
                <p class="text-slate-400 text-sm">{{ __('client/home.delivery_desc') }}</p>
            </div>
            <div>
                <div class="w-16 h-16 mx-auto bg-green-500/10 rounded-2xl flex items-center justify-center text-green-500 text-3xl mb-4 transform transition hover:-translate-y-2">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h2 class="text-xl font-bold text-white mb-2">{{ __('client/home.secure_title') }}</h2>
                <p class="text-slate-400 text-sm">{{ __('client/home.secure_desc') }}</p>
            </div>
        </div>
    </section>
    
    {{-- SECCIÓN: UBICACIÓN Y CONTACTO --}}
    <section class="mb-20 bg-slate-800/50 rounded-3xl p-6 sm:p-10 border border-slate-300">
        <div class="flex flex-col lg:flex-row gap-10 items-center">
            
            <div class="w-full lg:w-1/3 space-y-6">
                <h2 class="text-3xl font-black text-white flex items-center gap-3 mb-6">
                    <i class="fas fa-map-marker-alt text-red-500"></i> {{ __('client/home.find_us') }}
                </h2>
                
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-orange-500/10 rounded-full flex items-center justify-center text-orange-500 shrink-0 shadow-inner">
                        <i class="fas fa-store"></i>
                    </div>
                    <div>
                        <h4 class="text-white font-bold text-lg">{{ __('client/home.address') }}</h4>
                        <p class="text-slate-400 text-sm leading-relaxed">
                            {!! __('client/home.address_text') !!}
                        </p>
                    </div>
                </div>

                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-blue-500/10 rounded-full flex items-center justify-center text-blue-500 shrink-0 shadow-inner">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div>
                        <h4 class="text-white font-bold text-lg">{{ __('client/home.schedule') }}</h4>
                        <p class="text-slate-400 text-sm leading-relaxed">
                            {!! __('client/home.schedule_text') !!}
                        </p>
                    </div>
                </div>

                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-green-500/10 rounded-full flex items-center justify-center text-green-500 shrink-0 shadow-inner">
                        <i class="fas fa-phone-alt"></i>
                    </div>
                    <div>
                        <h2 class="text-white font-bold text-lg">{{ __('client/home.call_us') }}</h2>
                        <p class="text-slate-400 text-sm">
                            <a href="tel:+527251361324" class="hover:text-orange-400 transition-colors duration-300 font-mono text-base">
                                +52 725 136 1324
                            </a>
                        </p>
                    </div>
                </div>

                <a href="https://maps.google.com/?cid=1657466745896358562&g_mp=CiVnb29nbGUubWFwcy5wbGFjZXMudjEuUGxhY2VzLkdldFBsYWNl" target="_blank" class="w-full bg-slate-700 hover:bg-slate-600 text-white font-bold py-3.5 rounded-xl transition-all flex items-center justify-center gap-2 border border-slate-300 hover:border-orange-500 mt-6 shadow-lg">
                    <i class="fas fa-location-arrow text-orange-500"></i> {{ __('client/home.how_to_arrive') }}
                </a>
            </div>

            <div class="w-full lg:w-2/3 h-80 sm:h-[400px] rounded-2xl overflow-hidden border border-slate-300 shadow-2xl relative group">
                <div class="absolute inset-0 bg-slate-900/20 group-hover:bg-transparent transition-colors duration-500 pointer-events-none z-10"></div>
                <iframe
title="Mapa de ubicación de K-Hamburguesas" 
                    src="https://maps.google.com/maps?q=Clasi-k+Hamburguesas,+Av.+Miguel+Hidalgo+14,+Villa+de+Almoloya+de+Juárez&t=&z=16&ie=UTF8&iwloc=&output=embed" 
                    width="100%" 
                    height="100%" 
                    style="border:0;" 
                    allowfullscreen="" 
                    loading="lazy" 
                    referrerpolicy="no-referrer-when-downgrade" 
                    class="absolute inset-0 grayscale-20 contrast-110 group-hover:grayscale-0 transition-all duration-500">
                </iframe>
            </div>
        </div>
    </section>
</div>

{{-- MODAL DEL PRODUCTO --}}
<div id="home-product-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 sm:p-6" aria-modal="true">
    <div class="fixed inset-0 bg-black/80 backdrop-blur-sm transition-opacity" onclick="cerrarModalHome()"></div>

    <div class="relative w-full max-w-2xl bg-slate-800 rounded-3xl shadow-2xl border border-slate-300 overflow-hidden transform transition-all translate-y-8 opacity-0 scale-95 duration-300 flex flex-col max-h-[90vh]" id="home-modal-container">
        
        <button onclick="cerrarModalHome()" class="absolute top-4 right-4 z-50 w-10 h-10 bg-black/50 hover:bg-red-500 text-white rounded-full flex items-center justify-center backdrop-blur-sm transition-colors border border-white/10">
            <i class="fas fa-times text-lg"></i>
        </button>

        <div class="h-48 sm:h-56 w-full relative bg-slate-900 shrink-0 p-4">
            <img id="home-modal-img" src="" class="w-full h-full object-contain relative z-10 drop-shadow-2xl">
            <div class="absolute inset-0 bg-gradient-to-t from-slate-800 via-slate-800/30 to-transparent opacity-90 z-20 pointer-events-none"></div>
        </div>

        <div class="p-6 sm:p-8 overflow-y-auto custom-scrollbar">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-end mb-4 gap-2">
                <h2 class="text-2xl sm:text-3xl font-black text-white leading-tight drop-shadow-lg" id="home-modal-title">Platillo</h2>
                <p class="text-2xl font-black text-orange-500 bg-slate-900 px-4 py-1.5 rounded-xl shadow-inner border border-slate-300 inline-block w-max" id="home-modal-price">$0.00</p>
            </div>
            
            <p class="text-slate-400 text-sm mb-6 leading-relaxed" id="home-modal-desc">Detalles...</p>

            <div class="space-y-6">
                
                <div id="home-opciones-contenedor" class="hidden bg-slate-900/50 p-4 rounded-2xl border border-slate-300/50">
                    <label class="block text-xs font-bold text-orange-400 mb-3 uppercase tracking-wider"><i class="fas fa-sliders-h mr-1"></i> {{ __('client/home.customize_dish') }}</label>
                    <div id="home-modal-opciones-dinamicas" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 items-end">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between bg-slate-900 p-2 border border-slate-300 rounded-2xl shadow-inner">
                            <span class="text-slate-400 font-bold ml-4 uppercase tracking-wider text-xs flex items-center gap-2"><i class="fas fa-utensils text-slate-500"></i> {{ __('client/home.quantity') }}</span>
                            <div class="flex items-center space-x-2">
                                <button onclick="cambiarCantidadHome(-1)" class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-slate-800 border border-slate-300 text-white hover:bg-orange-500 transition font-bold text-xl active:scale-95">-</button>
                                <span id="home-cantidad-span" class="text-white font-black text-2xl w-10 text-center transition-transform">1</span>
                                <button onclick="cambiarCantidadHome(1)" class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-orange-700 text-white hover:bg-orange-500 transition font-bold text-xl active:scale-95 shadow-[0_0_10px_rgba(234,88,12,0.3)]">+</button>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-400 mb-2 uppercase tracking-wider">{{ __('client/home.special_notes') }}</label>
                            <textarea id="home-modal-notas" rows="1" class="w-full bg-slate-900 text-white border border-slate-300 rounded-xl p-3 focus:ring-2 focus:ring-orange-500 outline-none transition-all text-sm placeholder-slate-600 resize-none shadow-inner" placeholder="{{ __('client/home.placeholder_notes') }}"></textarea>
                        </div>
                    </div>

                    <button onclick="agregarAlCarritoHome()" class="w-full bg-orange-700 hover:bg-orange-500 text-white font-black py-4 rounded-2xl shadow-[0_0_15px_rgba(234,88,12,0.4)] hover:shadow-[0_0_25px_rgba(234,88,12,0.6)] transform transition hover:-translate-y-0.5 active:scale-[0.98] flex flex-col items-center justify-center gap-1 group h-full max-h-[110px]">
                        <span id="home-btn-add-text" class="text-sm sm:text-base flex items-center gap-2 uppercase tracking-wider"><i class="fas fa-shopping-cart group-hover:animate-bounce"></i> {{ __('client/home.add_cart') }}</span>
                        <span id="home-modal-total" class="bg-black/20 px-4 py-1 rounded-xl text-xl sm:text-2xl font-black tracking-wide border border-white/10 w-3/4 text-center mt-1">$0.00</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL DE UPSELLING --}}
<div id="upsell-modal" class="fixed inset-0 z-[100] hidden flex items-center justify-center p-4 sm:p-6" aria-modal="true">
    <div class="fixed inset-0 bg-black/80 backdrop-blur-md transition-opacity" onclick="cerrarUpsellModal()"></div>
    <div id="upsell-modal-panel" class="relative w-full max-w-md bg-slate-800 rounded-3xl shadow-2xl border border-slate-300 overflow-hidden transform scale-95 opacity-0 transition-all duration-300 text-center p-8">
        <div class="w-20 h-20 bg-emerald-500/20 rounded-full flex items-center justify-center mx-auto mb-6">
            <i class="fas fa-check text-4xl text-emerald-500"></i>
        </div>
        <h2 class="text-2xl font-black text-white mb-2">{{ __('client/home.added_to_cart') }}</h2>
        <p class="text-slate-400 text-sm mb-8">{{ __('client/home.upsell_desc') }}</p>
        
        <div class="flex flex-col gap-3">
            <a href="{{ route('menu') }}" class="w-full bg-orange-700 hover:bg-orange-500 text-white font-bold py-3.5 rounded-xl shadow-lg hover:shadow-orange-500/30 transition-all">
                {{ __('client/home.btn_menu') }}
            </a>
            <button onclick="cerrarUpsellModal()" class="w-full bg-slate-700 hover:bg-slate-600 text-white font-bold py-3.5 rounded-xl transition-all">
                {{ __('client/home.continue_home') }}
            </button>
            <a href="{{ route('cart.index') }}" class="w-full text-slate-400 hover:text-white text-sm font-bold py-2 mt-2 underline transition-colors">
                {{ __('client/home.go_to_pay') }}
            </a>
        </div>
    </div>
</div>

<div id="toast-container" class="fixed bottom-5 right-5 z-50 flex flex-col gap-3 pointer-events-none"></div>
<div id="live-modal-container"></div>

<script>
    window.MENU_LANG = {
        locale: '{{ app()->getLocale() }}',
        currencySymbol: '{{ app()->getLocale() == "en" ? "$" : (app()->getLocale() == "pt" ? "R$" : "$") }}',
        currencyCode: '{{ app()->getLocale() == "en" ? " USD" : (app()->getLocale() == "pt" ? "" : "") }}',
        exchangeRate: {{ app()->getLocale() == "en" ? Cache::get('exchange_rate_usd', 20.00) : (app()->getLocale() == "pt" ? Cache::get('exchange_rate_brl', 3.50) : 1) }}
    };
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const memKey = 'k_rider_aviso_visto_';

        function verificarAvisoRepartidor() {
            fetch('{{ route("api.cliente.tracker") }}')
                .then(res => res.json())
                .then(data => {
                    if (!data.active || !data.notificacion) return;

                    const yaVisto = sessionStorage.getItem(memKey + data.notificacion);
                    
                    if (!yaVisto && !document.getElementById('modal-aviso-repartidor')) {
                        const container = document.getElementById('live-modal-container');
                        
                        let color, icon, title, text;
                        
                        const onWayTitle = `{{ __('client/home.rider_on_way_title') }}`;
                        const onWayDesc = `{!! __('client/home.rider_on_way_desc') !!}`.replace(':id', data.id);
                        
                        const arrivedTitle = `{{ __('client/home.rider_arrived_title') }}`;
                        const arrivedDesc = `{!! __('client/home.rider_arrived_desc') !!}`.replace(':id', data.id);
                        
                        const btnTicket = `{{ __('client/home.btn_view_ticket_qr') }}`;
                        const btnClose = `{{ __('client/home.btn_close_notice') }}`;

                        if (data.notificacion === 'en_camino_real') {
                            color = 'indigo';
                            icon = '<i class="fas fa-motorcycle text-4xl animate-pulse"></i>';
                            title = onWayTitle;
                            text = onWayDesc;
                        } else {
                            color = 'pink';
                            icon = '<i class="fas fa-bell text-4xl animate-bounce"></i>';
                            title = arrivedTitle;
                            text = arrivedDesc;
                        }

                        container.innerHTML = `
                            <div id="modal-aviso-repartidor" class="fixed inset-0 z-[200] flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm transition-opacity duration-300">
                                <div class="relative w-full max-w-sm bg-slate-900 rounded-3xl shadow-2xl border border-${color}-500/50 shadow-${color}-900/50 overflow-hidden transform transition-all scale-100 p-8 text-center animate-fade-in-up">
                                    <div class="w-20 h-20 mx-auto rounded-full flex items-center justify-center mb-6 shadow-inner bg-${color}-500/20 text-${color}-500">
                                        ${icon}
                                    </div>
                                    <h2 class="text-2xl font-black text-white mb-2 uppercase tracking-wide">${title}</h2>
                                    <p class="text-slate-400 text-sm mb-8 leading-relaxed">${text}</p>
                                    <div class="flex flex-col gap-3">
                                        <a href="/ticket/${data.id}" class="w-full font-bold py-3.5 rounded-xl shadow-lg transition-all bg-${color}-600 hover:bg-${color}-500 text-white">
                                            ${btnTicket}
                                        </a>
                                        <button onclick="cerrarAvisoRepartidor('${data.notificacion}')" class="w-full bg-slate-800 hover:bg-slate-700 text-white font-bold py-3.5 rounded-xl transition-all border border-slate-300">
                                            ${btnClose}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        `;
                        
                        new Audio('https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3').play().catch(e=>{});
                    }
                })
                .catch(err => console.log("Buscando actualizaciones..."));
        }

        verificarAvisoRepartidor();
        setInterval(verificarAvisoRepartidor, 10000);
    });

    window.cerrarAvisoRepartidor = function(tipoNotificacion) {
        document.getElementById('live-modal-container').innerHTML = '';
        sessionStorage.setItem('k_rider_aviso_visto_' + tipoNotificacion, 'true');
    };
</script>

@vite(['resources/js/client/home-interaction.js'])
@endsection
