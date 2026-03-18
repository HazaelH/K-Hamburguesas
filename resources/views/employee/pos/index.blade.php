@extends('layouts.employee')

@section('titulo', __('employee/pos/index.title'))

@section('contenido')
@vite(['resources/css/pos.css'])

{{-- 1. CONTENEDOR PRINCIPAL --}}
<div class="flex flex-col lg:flex-row h-[100dvh] w-full overflow-hidden bg-slate-950 relative selection:bg-orange-500/30">

    <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none z-0">
        <div class="absolute top-[-10%] left-[-5%] w-[400px] h-[400px] bg-orange-700/20 rounded-full blur-[120px]"></div>
        <div class="absolute bottom-[-10%] right-[20%] w-[500px] h-[500px] bg-blue-600/10 rounded-full blur-[120px]"></div>
    </div>

    {{-- 2. PANEL DE PRODUCTOS --}}
    <div class="flex-1 min-w-0 flex flex-col h-full z-10 relative lg:border-r border-slate-800 bg-slate-900/40 backdrop-blur-xl overflow-hidden">
        
        <div class="p-4 lg:p-6 pb-2 shrink-0 space-y-4">
            <div class="flex justify-between items-center gap-4">
                <h1 class="hidden lg:flex text-3xl font-black text-white items-center gap-3 tracking-tight shrink-0">
                    <i class="fas fa-cash-register text-orange-500"></i> {{ __('employee/pos/index.pos_title') }}
                </h1>
                
                <div class="relative group flex-1 max-w-md">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                        <i class="fas fa-search text-lg"></i>
                    </span>
                    <input type="text" id="buscador-pos" placeholder="{{ __('employee/pos/index.search_placeholder') }}" 
                           class="w-full bg-slate-800/80 text-white border border-slate-300 rounded-2xl py-3 pl-12 pr-4 focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 shadow-inner transition-all text-base lg:text-lg placeholder-slate-500">
                </div>
            </div>

            <div class="w-full">
                <div class="flex flex-wrap gap-2 pt-1 pb-2" id="category-filters" role="tablist">
                    <button onclick="filterCategory('all', this)" role="tab" aria-selected="true"
                            class="cat-btn active px-5 py-2.5 bg-orange-700 text-white rounded-xl font-bold text-sm shadow-md transition-transform active:scale-95 focus:outline-none focus:ring-2 focus:ring-orange-400 flex items-center gap-2">
                        <i class="fas fa-star text-xs"></i> {{ __('employee/pos/index.all_categories') }}
                    </button>

                    @php
                        $categoriasBd = \App\Models\Categoria::all();
                    @endphp

                    @foreach($categoriasBd as $cat)
                        @php
                            $nombreMostrar = $cat->nombre;
                            if (app()->getLocale() == 'en' && !empty($cat->nombre_en)) $nombreMostrar = $cat->nombre_en;
                            if (app()->getLocale() == 'pt' && !empty($cat->nombre_pt)) $nombreMostrar = $cat->nombre_pt;
                        @endphp
                        <button onclick="filterCategory('{{ $cat->nombre }}', this)" role="tab" aria-selected="false"
                                class="cat-btn px-5 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white border border-slate-300 rounded-xl font-bold text-sm transition-transform active:scale-95 focus:outline-none focus:ring-2 focus:ring-slate-400 flex items-center gap-2">
                            <i class="fas fa-tag text-xs opacity-50"></i> {{ $nombreMostrar }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto p-4 lg:p-6 pt-0 pb-28 lg:pb-6 custom-scrollbar min-h-0">
            <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-4 lg:gap-5" id="grid-productos">
                @foreach($productos as $producto)
                    <div onclick="openCustomizationModal({{ $producto->id_producto }}, '{{ addslashes($producto->nombre_traducido) }}', {{ $producto->precio }}, '{{ asset('imagenes/' . $producto->imagen_url) }}', {{ json_encode($producto->opciones_personalizacion ?? '[]') }}, '{{ app()->getLocale() }}')"
                        class="product-item group relative bg-slate-800/80 backdrop-blur-sm border border-slate-300 rounded-2xl overflow-hidden cursor-pointer transition-all duration-200 hover:border-orange-500 hover:shadow-[0_0_20px_rgba(234,88,12,0.15)] active:scale-95 flex flex-col h-full min-h-[160px] select-none"
                        data-nombre="{{ strtolower($producto->nombre_traducido) }}"
                        data-categoria="{{ $producto->categoria }}">
                        
                        <div class="h-28 lg:h-32 w-full relative overflow-hidden bg-slate-900 shrink-0">
                            @if($producto->imagen_url)
                                <img src="{{ asset('imagenes/' . $producto->imagen_url) }}" alt="{{ $producto->nombre_traducido }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-slate-700 text-3xl"><i class="fas fa-hamburger"></i></div>
                            @endif
                            <div class="absolute bottom-2 right-2 bg-orange-700 backdrop-blur-md rounded-lg px-2.5 py-1 text-white text-sm font-black border border-slate-300 shadow-lg">
                                {{ formatCurrency($producto->precio) }}
                            </div>
                        </div>
                        
                        <div class="p-3 flex-1 flex flex-col justify-start bg-gradient-to-t from-slate-900/80 to-transparent">
                            <h4 class="font-bold text-white text-sm leading-tight line-clamp-2">
                                {{ $producto->nombre_traducido }}
                            </h4>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- BOTÓN FLOTANTE MÓVIL --}}
    <div class="lg:hidden fixed bottom-0 left-0 w-full p-4 z-40 bg-gradient-to-t from-slate-950 via-slate-950/90 to-transparent pointer-events-none">
        <button onclick="toggleCartMobile()" class="w-full bg-orange-700 hover:bg-orange-600 text-white font-bold py-4 rounded-2xl shadow-[0_0_20px_rgba(234,88,12,0.5)] flex justify-between items-center px-6 pointer-events-auto border border-orange-500 transition-transform active:scale-95">
            <span class="flex items-center gap-2 text-lg"><i class="fas fa-shopping-bag"></i> {{ __('employee/pos/index.current_account') }}</span>
            <span id="mobile-fab-total" class="bg-black/30 px-3 py-1 rounded-xl text-lg font-black tracking-wide">$0.00</span>
        </button>
    </div>

    {{-- 3. PANEL DEL CARRITO --}}
    {{-- NOTA DE CLASES: En móvil es 'fixed' y está oculto abajo (translate-y-full). En PC es 'relative' y SIEMPRE visible. --}}
    <div id="cart-panel" class="fixed lg:relative bottom-0 left-0 w-full lg:w-80 xl:w-[380px] shrink-0 flex flex-col h-[85vh] lg:h-full min-h-0 bg-slate-900 shadow-[0_-10px_50px_rgba(0,0,0,0.8)] lg:shadow-none z-50 lg:z-10 border-t lg:border-t-0 lg:border-l border-slate-700 transform translate-y-full lg:translate-y-0 transition-transform duration-300 ease-in-out rounded-t-3xl lg:rounded-none">
        
        {{-- Encabezado solo para móvil --}}
        <div class="lg:hidden flex justify-between items-center p-5 border-b border-slate-800 bg-slate-800/80 rounded-t-3xl backdrop-blur-md shrink-0">
            <h2 class="text-white font-black text-xl flex items-center gap-2">
                <i class="fas fa-receipt text-orange-500"></i> {{ __('employee/pos/index.current_account') }}
            </h2>
            <button aria-label="Cerrar panel de cuenta" onclick="toggleCartMobile()" class="bg-slate-700 text-slate-300 hover:text-white w-10 h-10 rounded-full flex items-center justify-center transition-colors shadow-inner">
                <i class="fas fa-chevron-down text-xl"></i>
            </button>
        </div>

        {{-- Encabezado solo para PC --}}
        <div class="hidden lg:block p-4 lg:p-5 bg-slate-900 border-b border-slate-800 shrink-0 space-y-4">
            <div class="flex justify-between items-center">
                <h2 class="text-white font-black text-xl flex items-center gap-2 tracking-tight">
                    {{ __('employee/pos/index.current_account') }}
                </h2>
                <button onclick="clearCart()" class="text-sm bg-red-500/10 text-red-400 hover:bg-red-500 hover:text-white px-3 py-1.5 rounded-lg font-bold transition-colors flex items-center gap-2">
                    <i class="fas fa-trash-alt"></i> {{ __('employee/pos/index.clear') }}
                </button>
            </div>
        </div>

        <div class="p-4 lg:p-5 bg-slate-900 border-b border-slate-800 shrink-0">
            <div class="grid grid-cols-2 lg:grid-cols-1 gap-3">
                <div class="relative">
                    <span class="absolute left-3 top-3.5 transition-colors duration-300">
                        <i id="icono-mesa" class="fas fa-shopping-bag text-orange-500"></i>
                    </span>
                    <select id="pos-mesa" aria-label="Seleccionar mesa o para llevar" onchange="cambiarIconoMesa(this.value)" class="w-full bg-slate-800 text-white rounded-xl py-3 pl-10 pr-10 text-sm font-bold border border-slate-300 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 outline-none appearance-none cursor-pointer transition-all shadow-inner">
                        <option value="">{{ __('employee/pos/index.takeout') }}</option>
                        @for($i=1; $i<=15; $i++) 
                            <option value="{{$i}}">{{ __('employee/pos/index.table', ['number' => $i]) }}</option> 
                        @endfor
                    </select>
                    <i class="fas fa-chevron-down absolute right-4 top-3.5 text-slate-500 pointer-events-none"></i>
                </div>
                
                <div class="relative">
                    <span class="absolute left-3 top-3 text-slate-400"><i class="fas fa-user"></i></span>
                    <input type="text" id="pos-cliente" placeholder="{{ __('employee/pos/index.client_name') }}" 
                           class="w-full bg-slate-800 text-white rounded-xl py-3 pl-10 pr-4 text-sm border border-slate-300 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 outline-none placeholder-slate-500">
                </div>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto p-3 lg:p-4 space-y-2 custom-scrollbar min-h-0 bg-slate-900/50" id="cart-items">
            <div class="h-full flex flex-col items-center justify-center text-slate-500 opacity-50 select-none">
                <i class="fas fa-receipt text-6xl mb-4"></i>
                <p class="font-bold text-lg">{{ __('employee/pos/index.empty_account') }}</p>
                <p class="text-sm text-center mt-2">{!! __('employee/pos/index.empty_account_desc') !!}</p>
            </div>
        </div>

        <div class="p-4 lg:p-5 bg-slate-900 border-t border-slate-800 shrink-0 z-30">
            <div class="flex justify-between items-center mb-3 lg:hidden">
                 <button onclick="clearCart()" class="text-sm text-red-400 hover:text-red-300 font-bold transition-colors flex items-center gap-2">
                    <i class="fas fa-trash-alt"></i> {{ __('employee/pos/index.clear') }}
                </button>
            </div>

            <div class="grid grid-cols-2 gap-3 mb-4">
                <label class="cursor-pointer relative">
                    <input type="radio" name="metodo_pago_pos" value="efectivo" class="peer sr-only" checked>
                    <div class="bg-slate-800 border-2 border-slate-300 text-slate-400 py-2.5 rounded-xl text-center font-black uppercase transition-all flex flex-col items-center gap-1
                                peer-checked:bg-emerald-600/20 peer-checked:text-emerald-400 peer-checked:border-emerald-500 hover:bg-slate-700 active:scale-95">
                        <i class="fas fa-money-bill-wave text-lg"></i>
                        <span class="text-[11px] tracking-wider">{{ __('employee/pos/index.cash') }}</span>
                    </div>
                </label>

                <label class="cursor-pointer relative">
                    <input type="radio" name="metodo_pago_pos" value="tarjeta" class="peer sr-only">
                    <div class="bg-slate-800 border-2 border-slate-300 text-slate-400 py-2.5 rounded-xl text-center font-black uppercase transition-all flex flex-col items-center gap-1
                                peer-checked:bg-blue-600/20 peer-checked:text-blue-400 peer-checked:border-blue-500 hover:bg-slate-700 active:scale-95">
                        <i class="fas fa-credit-card text-lg"></i>
                        <span class="text-[11px] tracking-wider">{{ __('employee/pos/index.card') }}</span>
                    </div>
                </label>
            </div>
            
            <div class="flex justify-between items-end mb-4 px-1">
                <span class="text-slate-400 text-xs font-bold uppercase tracking-widest">{{ __('employee/pos/index.total_to_charge') }}</span>
                <span class="text-3xl lg:text-4xl font-black text-white tracking-tighter" id="cart-total">$0.00</span>
            </div>
            
            <button id="btn-pagar" onclick="submitOrder()" disabled
                    class="w-full bg-orange-700 hover:bg-orange-500 text-white font-black py-4 rounded-xl shadow-lg shadow-orange-900/30 transition-all flex justify-center items-center gap-3 text-lg disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-orange-700 active:scale-[0.98]">
                <span>{{ __('employee/pos/index.confirm_order') }}</span> <i class="fas fa-check-circle"></i>
            </button>
        </div>
    </div>
</div> {{-- FIN CONTENEDOR PRINCIPAL --}}

{{-- MODAL DE PERSONALIZACIÓN (Fuera del contenedor principal para que no herede el overflow hidden) --}}
<div id="customization-modal" class="fixed inset-0 z-[150] hidden flex items-center justify-center bg-black/80 backdrop-blur-sm p-4 opacity-0 transition-opacity duration-300">
    <div id="customization-panel" class="bg-slate-900 border border-slate-300 rounded-3xl shadow-2xl w-full max-w-lg flex flex-col max-h-[90vh] transform scale-95 transition-all duration-300">
        
        <div class="p-5 border-b border-slate-800 flex justify-between items-start bg-slate-800/50 rounded-t-3xl">
            <div class="flex gap-4 items-center">
                <img id="modal-prod-img" src="" alt="Imagen del producto seleccionado" class="w-16 h-16 rounded-xl object-cover border-2 border-slate-300 shadow-lg bg-slate-900">
                <div>
                    <h3 id="modal-prod-name" class="text-xl font-black text-white leading-tight">{{ __('employee/pos/index.product') }}</h3>
                    <p class="text-orange-400 font-bold font-mono">{{ __('employee/pos/index.base_price') }}<span id="modal-prod-base-price">0.00</span></p>
                </div>
            </div>
            <button aria-label="Cerrar opciones de personalización" onclick="closeCustomizationModal()" class="w-8 h-8 bg-slate-800 hover:bg-red-500 text-slate-400 hover:text-white rounded-full flex items-center justify-center transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="p-5 overflow-y-auto custom-scrollbar flex-1 bg-slate-900" id="modal-options-container">
        </div>

        <div class="p-5 border-t border-slate-800 bg-slate-800/50 rounded-b-3xl shrink-0">
            <div class="flex justify-between items-end mb-4">
                <span class="text-slate-400 font-bold uppercase tracking-widest text-xs">{{ __('employee/pos/index.total_with_extras') }}</span>
                <span class="text-3xl font-black text-emerald-400 font-mono" id="modal-final-price">$0.00</span>
            </div>
            <button id="modal-add-btn" class="w-full bg-orange-700 hover:bg-orange-500 text-white font-black py-4 rounded-xl shadow-lg transition-all active:scale-95 flex justify-center items-center gap-2">
                <i class="fas fa-cart-plus"></i> {{ __('employee/pos/index.add_to_account') }}
            </button>
        </div>
    </div>
</div>

<div id="clear-cart-modal" class="fixed inset-0 z-[200] hidden flex items-center justify-center bg-black/80 backdrop-blur-sm p-4 opacity-0 transition-opacity duration-300">
    <div id="clear-cart-panel" class="bg-slate-900 border border-slate-700 p-6 md:p-8 rounded-3xl shadow-2xl max-w-sm w-full transform scale-95 transition-transform duration-300 text-center">
        <div class="w-20 h-20 bg-red-500/20 text-red-500 rounded-full flex items-center justify-center text-4xl mx-auto mb-5 border border-red-500/30">
            <i class="fas fa-trash-alt"></i>
        </div>
        <h3 class="text-2xl font-black text-white mb-2 tracking-tight">{{ __('employee/pos/index.clear_q') }}</h3>
        <p class="text-slate-400 text-sm mb-8 leading-relaxed">{{ __('employee/pos/index.clear_account_desc') }}</p>
        <div class="flex gap-3">
            <button type="button" onclick="closeClearModal()" class="flex-1 bg-slate-800 hover:bg-slate-700 text-white font-bold py-3.5 rounded-xl transition-all">{{ __('employee/pos/index.cancel') }}</button>
            <button type="button" onclick="confirmClearCart()" class="flex-1 bg-red-600 hover:bg-red-500 text-white font-bold py-3.5 rounded-xl shadow-lg transition-all">{{ __('employee/pos/index.yes_clear') }}</button>
        </div>
    </div>
</div>

<div id="toast-container" class="fixed top-20 right-4 lg:right-8 z-[100] flex flex-col gap-3 pointer-events-none w-72 md:w-80"></div>

<script>
    const API_STORE_ORDER = "{{ route('employee.pos.store') }}";
    const CSRF_TOKEN = "{{ csrf_token() }}";

    window.MENU_LANG = {
        locale: '{{ app()->getLocale() }}',
        currencySymbol: '{{ app()->getLocale() == "en" ? "$" : (app()->getLocale() == "pt" ? "R$" : "$") }}',
        currencyCode: '{{ app()->getLocale() == "en" ? " USD" : (app()->getLocale() == "pt" ? "" : " MXN") }}',
        exchangeRate: {{ app()->getLocale() == "en" ? Cache::get('exchange_rate_usd', 20.00) : (app()->getLocale() == "pt" ? Cache::get('exchange_rate_brl', 3.50) : 1) }}
    };

    window.POS_LANG = {
        extras: "{{ __('employee/pos/index.extras_modifications') }}",
        added: "{{ __('employee/pos/index.added') }}",
        account_cleared: "{{ __('employee/pos/index.account_cleared') }}",
        clear_q: "{{ __('employee/pos/index.clear_account_q') }}",
        clear_desc: "{{ __('employee/pos/index.clear_account_desc') }}",
        cancel: "{{ __('employee/pos/index.cancel') }}",
        yes_clear: "{{ __('employee/pos/index.yes_clear') }}",
        order_sent: "{{ __('employee/pos/index.order_sent') }}",
        conn_error: "{{ __('employee/pos/index.conn_error') }}",
        empty_acc: "{{ __('employee/pos/index.empty_account') }}",
        empty_desc: "{!! __('employee/pos/index.empty_account_desc') !!}",
    };
</script>

@vite(['resources/js/employee/pos.js'])
@endsection