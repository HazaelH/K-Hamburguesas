@extends('layouts.app')

@section('titulo', __('client/offers.title'))

@section('contenido')
<div class="pb-20 min-h-screen bg-slate-900 relative overflow-hidden">

    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[400px] bg-orange-600/20 blur-[120px] rounded-full pointer-events-none z-0"></div>

    <div class="text-center py-12 animate-fade-in-down relative z-10">
        <span class="bg-orange-500/10 text-orange-400 border border-orange-500/30 px-5 py-1.5 rounded-full text-xs font-black uppercase tracking-[0.2em] mb-6 inline-block shadow-[0_0_15px_rgba(249,115,22,0.2)]">
            <i class="fas fa-stopwatch mr-1 animate-pulse"></i> {{ __('client/offers.limited_time') }}
        </span>
        <h1 class="text-5xl md:text-7xl font-black text-white mb-4 tracking-tight">
            {{ __('client/offers.explosive_offers') }}
        </h1>
        <p class="text-slate-400 text-sm md:text-base max-w-lg mx-auto">{{ __('client/offers.offers_desc') }}</p>
    </div>

    <div class="container mx-auto px-4 relative z-10">
        @if($ofertas->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($ofertas as $oferta)
                    @php
                        // Obtenemos la categoría traducida para el botón del modal si aplica
                        $refTraducida = $oferta->referencia;
                        if ($oferta->tipo_aplicacion === 'categoria') {
                            $catModel = \App\Models\Categoria::where('nombre', $oferta->referencia)->first();
                            if ($catModel) {
                                if (app()->getLocale() == 'en' && !empty($catModel->nombre_en)) $refTraducida = $catModel->nombre_en;
                                if (app()->getLocale() == 'pt' && !empty($catModel->nombre_pt)) $refTraducida = $catModel->nombre_pt;
                            }
                        }
                    @endphp

                    {{-- APLICAMOS ACCESORES A LOS DATA-ATTRIBUTES --}}
                    <div onclick="window.abrirModalOferta(this)"
                         class="oferta-card cursor-pointer relative bg-slate-800 rounded-3xl overflow-hidden group hover:-translate-y-2 transition-all duration-300 shadow-xl shadow-black/50 border border-slate-700 hover:border-orange-500/50 hover:shadow-[0_10px_30px_rgba(249,115,22,0.15)] flex flex-col h-full"
                         data-titulo="{{ $oferta->titulo_traducido }}"
                         data-desc="{{ $oferta->descripcion_traducida }}"
                         data-img="{{ $oferta->imagen_url ? asset('storage/' . $oferta->imagen_url) : '' }}"
                         data-fin-iso="{{ \Carbon\Carbon::parse($oferta->fecha_fin)->endOfDay()->toIso8601String() }}"
                         data-porcentaje="{{ $oferta->porcentaje }}"
                         data-precio="{{ $oferta->precio_promo }}"
                         data-tipo="{{ $oferta->tipo_aplicacion }}"
                         data-ref="{{ $oferta->referencia }}"
                         data-ref-traducida="{{ $refTraducida }}"> 
                         
                         <div class="h-56 overflow-hidden relative bg-slate-900">
                            @if($oferta->imagen_url)
                                <img src="{{ asset('storage/' . $oferta->imagen_url) }}" alt="{{ $oferta->titulo_traducido }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-700 opacity-80 group-hover:opacity-100">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-slate-800">
                                    <i class="fas fa-tags text-7xl text-slate-700 group-hover:text-orange-500/20 transition-colors"></i>
                                </div>
                            @endif

                            <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center backdrop-blur-[2px] z-20">
                                <span class="bg-orange-500 text-white font-bold px-6 py-2.5 rounded-full transform scale-75 group-hover:scale-100 transition-transform duration-300 shadow-lg flex items-center gap-2">
                                    <i class="fas fa-eye"></i> {{ __('client/offers.view_details') }}
                                </span>
                            </div>
                            
                            @if($oferta->porcentaje > 0)
                                <div class="absolute top-4 left-4 bg-red-600 text-white font-black px-4 py-1.5 rounded-xl shadow-lg rotate-[-5deg] z-30 border border-red-400/30 text-lg">
                                    -{{ $oferta->porcentaje }}%
                                </div>
                            @elseif($oferta->precio_promo > 0)
                                <div class="absolute top-4 left-4 bg-green-600 text-white font-black px-4 py-1.5 rounded-xl shadow-lg rotate-[-5deg] z-30 border border-green-400/30 text-lg">
                                    {{ __('client/offers.only') }} {{ formatCurrency($oferta->precio_promo) }}
                                </div>
                            @endif
                        </div>

                        <div class="relative bg-slate-800 px-6 pt-8 pb-6 flex-1 flex flex-col pointer-events-none">
                            <div class="absolute top-0 left-4 right-4 border-t-2 border-dashed border-slate-600"></div>
                            <div class="absolute -top-3 -left-3 w-6 h-6 bg-slate-900 rounded-full z-10 border-b border-r border-slate-700"></div>
                            <div class="absolute -top-3 -right-3 w-6 h-6 bg-slate-900 rounded-full z-10 border-b border-l border-slate-700"></div>

                            {{-- APLICAMOS ACCESORES VISUALES --}}
                            <h3 class="text-2xl font-black text-white mb-3 leading-tight group-hover:text-orange-400 transition-colors">{{ $oferta->titulo_traducido }}</h3>
                            <p class="text-slate-400 text-sm line-clamp-2 mb-6 flex-1">{{ $oferta->descripcion_traducida }}</p>

                            <div class="flex justify-between items-center bg-slate-900/80 p-4 rounded-2xl border border-slate-700/50">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-orange-500/10 flex items-center justify-center text-orange-500 animate-pulse">
                                        <i class="fas fa-hourglass-half"></i>
                                    </div>
                                    <div>
                                        <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wider mb-0.5">{{ __('client/offers.ends_on') }}</p>
                                        <p class="text-white font-black text-sm">{{ $oferta->fecha_fin->format(app()->getLocale() == 'en' ? 'm/d/Y' : 'd/m/Y') }}</p>
                                    </div>
                                </div>
                                <i class="fas fa-chevron-right text-slate-600 group-hover:text-orange-500 transition-colors"></i>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-24 bg-slate-800/30 rounded-3xl border border-dashed border-slate-700 max-w-2xl mx-auto backdrop-blur-sm">
                <div class="w-24 h-24 bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-ticket-alt text-5xl text-slate-500"></i>
                </div>
                <h2 class="text-3xl font-black text-white mb-3">{{ __('client/offers.no_offers') }}</h2>
                <p class="text-slate-400 mb-8 max-w-md mx-auto">{{ __('client/offers.no_offers_desc') }}</p>
                <a href="{{ route('menu') }}" class="bg-orange-600 hover:bg-orange-500 text-white px-8 py-3.5 rounded-xl font-black transition-all shadow-lg hover:shadow-orange-500/30 transform hover:-translate-y-0.5">
                    {{ __('client/offers.go_to_menu') }}
                </a>
            </div>
        @endif
    </div>
</div>

<div id="modal-oferta" class="fixed inset-0 z-[100] hidden flex items-center justify-center p-4 sm:p-6" aria-modal="true">
    <div class="fixed inset-0 bg-black/80 backdrop-blur-md transition-opacity" id="modal-overlay" onclick="window.cerrarModalOferta()"></div>

    <div id="modal-oferta-panel" class="relative w-full max-w-2xl bg-slate-800 rounded-3xl shadow-2xl border border-slate-700 overflow-hidden transform scale-95 opacity-0 transition-all duration-300 flex flex-col max-h-[90vh]">
        
        <button id="btn-cerrar-modal" onclick="window.cerrarModalOferta()" class="absolute top-4 right-4 z-50 w-10 h-10 bg-black/50 hover:bg-red-500 text-white rounded-full flex items-center justify-center backdrop-blur-sm transition-colors border border-white/10">
            <i class="fas fa-times text-lg"></i>
        </button>

        <div class="h-64 sm:h-80 w-full relative bg-slate-900 shrink-0">
            <img id="modal-img" src="" class="w-full h-full object-cover hidden">
            <div id="modal-no-img" class="w-full h-full flex items-center justify-center bg-slate-800 hidden">
                <i class="fas fa-star text-7xl text-slate-700"></i>
            </div>
            <div class="absolute inset-0 bg-gradient-to-t from-slate-800 via-slate-800/20 to-transparent"></div>
            
            <div id="modal-badge-container" class="absolute bottom-6 right-6"></div>
        </div>

        <div class="p-6 sm:p-10 relative -mt-16 sm:-mt-20 overflow-y-auto custom-scrollbar">
            
            <div class="mb-3 flex items-center gap-2" id="modal-etiqueta-aplicacion"></div>

            <h3 class="text-3xl sm:text-4xl font-black text-white mb-4 leading-tight shadow-black drop-shadow-lg" id="modal-titulo">Título</h3>
            
            <div class="bg-slate-900/50 border border-slate-700 rounded-2xl p-5 mb-6">
                <p class="text-slate-300 text-sm sm:text-base leading-relaxed whitespace-pre-line" id="modal-desc">Descripción...</p>
            </div>

            <div class="mb-8">
                <p class="text-orange-500 text-xs font-bold uppercase tracking-widest mb-3 flex items-center gap-2">
                    <i class="fas fa-stopwatch"></i> {{ __('client/offers.offer_ends_in') }}
                </p>
                <div class="flex gap-3 sm:gap-4 text-center">
                    <div class="bg-slate-900 border border-slate-700 rounded-xl p-3 flex-1 shadow-inner">
                        <span id="count-days" class="block text-2xl sm:text-3xl font-black text-white">00</span>
                        <span class="text-[10px] text-slate-500 uppercase font-bold">{{ __('client/offers.days') }}</span>
                    </div>
                    <div class="bg-slate-900 border border-slate-700 rounded-xl p-3 flex-1 shadow-inner">
                        <span id="count-hours" class="block text-2xl sm:text-3xl font-black text-white">00</span>
                        <span class="text-[10px] text-slate-500 uppercase font-bold">{{ __('client/offers.hours') }}</span>
                    </div>
                    <div class="bg-slate-900 border border-slate-700 rounded-xl p-3 flex-1 shadow-inner">
                        <span id="count-mins" class="block text-2xl sm:text-3xl font-black text-white">00</span>
                        <span class="text-[10px] text-slate-500 uppercase font-bold">{{ __('client/offers.mins') }}</span>
                    </div>
                    <div class="bg-slate-900 border border-slate-700 rounded-xl p-3 flex-1 shadow-inner">
                        <span id="count-secs" class="block text-2xl sm:text-3xl font-black text-orange-400">00</span>
                        <span class="text-[10px] text-slate-500 uppercase font-bold">{{ __('client/offers.secs') }}</span>
                    </div>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-4 items-center justify-end mt-auto">
                <a id="modal-btn-pedir" href="{{ route('menu') }}" class="w-full flex-1 text-center bg-orange-600 hover:bg-orange-500 text-white font-black py-4 px-8 rounded-xl transition-all shadow-[0_0_20px_rgba(234,88,12,0.4)] hover:shadow-[0_0_30px_rgba(234,88,12,0.6)] transform hover:-translate-y-1 flex items-center justify-center gap-2 text-lg group">
                    <i class="fas fa-shopping-cart group-hover:animate-bounce"></i> {{ __('client/offers.go_to_buy') }}
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    .animate-gradient-x { background-size: 200% auto; animation: textGradient 3s linear infinite; }
    @keyframes textGradient { to { background-position: 200% center; } }
</style>

<script>
    window.MENU_LANG = {
        locale: '{{ app()->getLocale() }}',
        currencySymbol: '{{ app()->getLocale() == "en" ? "$" : (app()->getLocale() == "pt" ? "R$" : "$") }}',
        currencyCode: '{{ app()->getLocale() == "en" ? " USD" : (app()->getLocale() == "pt" ? "" : " MXN") }}',
        exchangeRate: {{ app()->getLocale() == "en" ? Cache::get('exchange_rate_usd', 20.00) : (app()->getLocale() == "pt" ? Cache::get('exchange_rate_brl', 3.50) : 1) }}
    };
</script>

@vite(['resources/js/client/offers.js'])
@endsection