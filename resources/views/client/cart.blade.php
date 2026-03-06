@extends('layouts.app')

@section('titulo', __('client/cart.title'))

@section('contenido')
<div class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8 min-h-screen bg-gray-900">

    <div class="flex items-center justify-between mb-8 pb-4 border-b border-gray-800">
        <h1 class="text-3xl font-extrabold text-white flex items-center">
            <i class="fas fa-shopping-bag text-orange-500 mr-4 text-4xl drop-shadow-[0_0_15px_rgba(234,88,12,0.3)]"></i> 
            {{ __('client/cart.your_order') }}
        </h1>
        @if(count($carrito) > 0)
            <span id="items-count-badge" class="bg-orange-500/10 text-orange-500 border border-orange-500/20 font-bold px-4 py-1.5 rounded-full text-sm">
                {{ count($carrito) }} {{ __('client/cart.items') }}
            </span>
        @endif
    </div>

    @if(count($carrito) > 0)
        <div class="flex flex-col lg:flex-row gap-8">
            
            <div class="lg:w-2/3 space-y-5">
                @foreach($carrito as $rowId => $item)
                    <div id="row-{{ $rowId }}" class="bg-gray-800/80 backdrop-blur-sm rounded-2xl p-4 sm:p-5 flex flex-col sm:flex-row items-center justify-between border border-gray-700 hover:border-gray-600 shadow-lg relative group gap-5 transition-all duration-300"
                         data-precio-base="{{ $item['precio'] }}"> {{-- Data attribute crucial para el JS --}}
                        
                        <div class="flex items-center space-x-5 w-full sm:w-auto">
                            <div class="w-24 h-24 rounded-xl overflow-hidden flex-shrink-0 bg-gray-900 border border-gray-700 p-1">
                                <img src="{{ asset('imagenes/' . $item['imagen_url']) }}" class="w-full h-full object-cover rounded-lg transform group-hover:scale-110 transition duration-500">
                            </div>
                            
                            <div class="flex-1">
                                {{-- Si estuviera en tu base de datos traducido, idealmente se pasaría aquí, pero el carrito lo guarda crudo, lo dejamos como está --}}
                                <h3 class="text-lg font-bold text-white leading-tight mb-1 group-hover:text-orange-400 transition-colors">{{ $item['nombre'] }}</h3>
                                
                                @if(!empty($item['descripcion_mods']))
                                    <div class="inline-flex items-center gap-1.5 mt-1 bg-gray-900/60 px-2.5 py-1 rounded-md border border-gray-700/50">
                                        <i class="fas fa-edit text-orange-500 text-[10px]"></i> 
                                        <span class="text-[11px] text-gray-300 italic">{{ $item['descripcion_mods'] }}</span>
                                    </div>
                                @endif
                                
                                <p class="text-gray-400 font-medium mt-2 text-sm">
                                    {{ formatCurrency($item['precio']) }} <span class="text-xs text-gray-500">{{ __('client/cart.each') }}</span>
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center justify-between w-full sm:w-auto gap-8">
                            
                            <div class="flex items-center bg-gray-900 rounded-xl border border-gray-700 p-1.5 shadow-inner">
                                <button type="button" onclick="changeQty('{{ $rowId }}', 'decrease', this)" class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-white hover:bg-gray-800 rounded-lg transition disabled:opacity-50 active:scale-95">
                                    <i class="fas fa-minus text-xs"></i>
                                </button>

                                <span id="qty-{{ $rowId }}" class="w-10 text-center font-black text-white text-base">{{ $item['cantidad'] }}</span>

                                <button type="button" onclick="changeQty('{{ $rowId }}', 'increase', this)" class="w-8 h-8 flex items-center justify-center text-orange-500 hover:text-white hover:bg-orange-600 rounded-lg transition disabled:opacity-50 active:scale-95">
                                    <i class="fas fa-plus text-xs"></i>
                                </button>
                            </div>

                            <div class="text-right min-w-[85px]">
                                <span class="block text-[10px] text-gray-500 uppercase font-bold tracking-wider mb-0.5">{{ __('client/cart.total') }}</span>
                                <span class="text-xl font-black text-white" id="item-total-{{ $rowId }}">
                                    {{ formatCurrency($item['precio'] * $item['cantidad']) }}
                                </span>
                            </div>

                            <button onclick="removeCartItem(this, '{{ $rowId }}')" class="w-10 h-10 rounded-full flex items-center justify-center text-gray-500 bg-gray-900/50 hover:bg-red-500/10 hover:text-red-500 transition-colors border border-transparent hover:border-red-500/30 group" title="{{ __('client/cart.delete') }}">
                                <i class="fas fa-trash-alt group-hover:scale-110 transition-transform"></i>
                            </button>
                        </div>
                    </div>
                @endforeach
                
                <div class="pt-4">
                    <a href="{{ route('menu') }}" class="inline-flex items-center text-gray-400 hover:text-orange-500 font-bold text-sm transition-colors group">
                        <i class="fas fa-arrow-left mr-2 transform group-hover:-translate-x-1 transition-transform"></i> 
                        {{ __('client/cart.add_more_food') }}
                    </a>
                </div>
            </div>

            <div class="lg:w-1/3">
                <div class="bg-gradient-to-b from-gray-800 to-gray-900 rounded-3xl p-6 sm:p-8 border border-gray-700 shadow-[0_10px_40px_-15px_rgba(0,0,0,0.7)] sticky top-28">
                    
                    <h2 class="text-xl font-black text-white mb-6 flex items-center">
                        <i class="fas fa-receipt text-gray-500 mr-3"></i> {{ __('client/cart.purchase_summary') }}
                    </h2>
                    
                    <div class="space-y-4 mb-6">
                        <div class="flex justify-between items-center text-gray-400 font-medium">
                            <span>{{ __('client/cart.subtotal') }}</span>
                            <span class="text-white" id="cart-subtotal">{{ formatCurrency($subtotal) }}</span>
                        </div>
                        <div class="flex justify-between items-center text-gray-400 font-medium">
                            <span>{{ __('client/cart.tax') }}</span>
                            <span class="text-white" id="cart-iva">{{ formatCurrency($iva) }}</span>
                        </div>
                    </div>

                    <div class="border-t border-dashed border-gray-600 my-6"></div>

                    <div class="flex justify-between items-end mb-8">
                        <div>
                            <span class="block text-gray-400 text-sm font-bold uppercase tracking-wider">{{ __('client/cart.final_total') }}</span>
                            <span class="block text-xs text-gray-500 mt-1">{{ __('client/cart.taxes_included') }}</span>
                        </div>
                        <span class="text-3xl font-black text-transparent bg-clip-text bg-gradient-to-r from-orange-400 to-orange-600" id="cart-total">
                            {{ formatCurrency($total) }}
                        </span>
                    </div>

                    <a href="{{ route('checkout') }}" 
                    class="relative flex items-center justify-center w-full py-4 px-6 font-black text-white text-lg rounded-2xl shadow-xl transition-all duration-300 transform hover:-translate-y-1 hover:shadow-[0_15px_30px_-10px_rgba(234,88,12,0.5)] active:scale-95 group overflow-hidden bg-orange-600 hover:bg-orange-500 border border-orange-400">
                        <div class="absolute inset-0 w-full h-full bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:animate-[shimmer_1.5s_infinite]"></div>
                        <span class="relative z-10 flex items-center">
                            {{ __('client/cart.proceed_to_checkout') }} 
                            <i class="fas fa-arrow-right ml-3 transform group-hover:translate-x-2 transition-transform duration-300"></i>
                        </span>
                    </a>

                    <div class="mt-4 flex items-center justify-center text-xs text-gray-500 font-medium gap-2">
                        <i class="fas fa-lock text-gray-600"></i> {{ __('client/cart.secure_encrypted') }}
                    </div>
                </div>
            </div>
        </div>

    @else
        <div class="flex flex-col items-center justify-center py-24 sm:py-32 bg-gray-800/30 rounded-3xl border-2 border-dashed border-gray-700/50 mt-10">
            
            <div class="relative w-32 h-32 flex items-center justify-center mb-6">
                <div class="absolute inset-0 bg-orange-500/20 rounded-full blur-xl animate-pulse"></div>
                <div class="relative bg-gray-800 border border-gray-700 w-24 h-24 rounded-full flex items-center justify-center shadow-2xl">
                    <i class="fas fa-shopping-basket text-4xl text-gray-500"></i>
                </div>
            </div>

            <h2 class="text-3xl font-black text-white mb-3">{{ __('client/cart.empty_cart') }}</h2>
            <p class="text-gray-400 mb-10 max-w-md text-center text-lg">
                {{ __('client/cart.empty_cart_desc') }}
            </p>
            
            <a href="{{ route('menu') }}" class="inline-flex items-center justify-center bg-white hover:bg-gray-100 text-gray-900 font-black py-4 px-10 rounded-2xl transition-all shadow-[0_10px_20px_-10px_rgba(255,255,255,0.3)] transform hover:-translate-y-1">
                {{ __('client/cart.explore_menu') }} <i class="fas fa-hamburger ml-3 text-orange-500"></i>
            </a>
        </div>
    @endif

</div>

<style>
    @keyframes shimmer {
        100% { transform: translateX(100%); }
    }
</style>

{{-- Inyectamos variables globales de idioma para JavaScript --}}
<script>
    window.MENU_LANG = {
        locale: '{{ app()->getLocale() }}',
        currencySymbol: '{{ app()->getLocale() == "en" ? "$" : (app()->getLocale() == "pt" ? "R$" : "$") }}',
        currencyCode: '{{ app()->getLocale() == "en" ? " USD" : (app()->getLocale() == "pt" ? "" : " MXN") }}',
        exchangeRate: {{ app()->getLocale() == "en" ? Cache::get('exchange_rate_usd', 20.00) : (app()->getLocale() == "pt" ? Cache::get('exchange_rate_brl', 3.50) : 1) }}
    };
</script>

<div id="toast-container" class="fixed bottom-5 right-5 z-50 flex flex-col gap-3 pointer-events-none"></div>

@vite(['resources/js/client/cart.js'])

@endsection