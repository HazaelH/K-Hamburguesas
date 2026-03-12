@extends('layouts.app')

@section('titulo', __('client/checkout.title'))

@section('contenido')
{{-- Estilos para adaptar intl-tel-input al modo oscuro --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@23.0.4/build/css/intlTelInput.css">
<style>
    .iti { width: 100%; display: block; }
    .iti__country-list { 
        background-color: #1f2937 !important; 
        border: 1px solid #374151 !important; 
        color: white !important; 
        border-radius: 0.75rem !important; 
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.5) !important;
    }
    .iti__country.iti__highlight { background-color: #374151 !important; }
    .iti__selected-dial-code { color: #f97316 !important; font-weight: bold; margin-left: 4px; }
    .iti__arrow { border-top-color: #9ca3af !important; }
    .iti__search-input { background-color: #111827 !important; border-color: #374151 !important; color: white !important; border-radius: 0.5rem !important;}
</style>

<div class="bg-gray-900 min-h-screen pb-20">
    
    <div class="bg-gray-800 border-b border-gray-300 pt-8 pb-6 mb-8 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-white flex items-center">
                    <i class="fas fa-motorcycle text-orange-500 mr-3 opacity-80"></i> {{ __('client/checkout.delivery_details') }}
                </h1>
                <p class="text-gray-400 text-sm mt-1">{{ __('client/checkout.subtitle') }}</p>
            </div>
            
            <div class="flex items-center text-sm font-bold text-gray-500">
                <span class="flex items-center gap-2"><i class="fas fa-shopping-cart"></i> {{ __('client/checkout.cart') }}</span>
                <i class="fas fa-chevron-right text-[10px] mx-3 opacity-50"></i>
                <span class="flex items-center gap-2 text-orange-500"><i class="fas fa-map-marker-alt"></i> {{ __('client/checkout.data_and_payment') }}</span>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        @if ($errors->any())
            <div class="bg-red-500/10 border border-red-500/30 text-red-400 p-5 mb-8 rounded-xl shadow-lg flex gap-4 items-start">
                <i class="fas fa-exclamation-circle text-2xl mt-0.5"></i>
                <div>
                    <p class="font-bold text-lg mb-1">{{ __('client/checkout.check_shipping_data') }}</p>
                    <ul class="list-disc list-inside text-sm space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-600 text-white p-5 mb-8 rounded-xl shadow-lg flex items-center gap-4">
                <i class="fas fa-server text-2xl"></i>
                <div>
                    <p class="font-bold text-lg">{{ __('client/checkout.system_error') }}</p>
                    <p class="text-sm opacity-90">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        {{-- Usamos Data-Attributes para traducciones --}}
        <form id="checkout-form" action="{{ route('checkout.process') }}" method="POST" 
            class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 relative"
            data-msg-fill="{{ __('client/checkout.fill_all_fields') }}"
            data-msg-process="{{ __('client/checkout.processing_payment') }}"
            data-msg-consent-title="{{ __('client/checkout.error_consent_title') }}"
            data-msg-consent="{{ __('client/checkout.error_consent_msg') }}"
            data-msg-addr-loaded="{{ __('client/checkout.address_loaded_title') }}"
            data-msg-addr-filled="{{ __('client/checkout.address_loaded_msg') }}">
            @csrf

            <div class="lg:col-span-7 space-y-8">
                
                <div class="bg-gray-800 rounded-3xl p-6 sm:p-8 border border-gray-300 shadow-xl relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-1.5 h-full bg-orange-500"></div>
                    
                    <div class="flex justify-between items-center border-b border-gray-300 pb-4 mb-6">
                        <h2 class="text-xl font-bold text-white flex items-center">
                            <span class="bg-gray-900 text-orange-500 w-8 h-8 rounded-full flex items-center justify-center mr-3 text-sm border border-gray-300">1</span>
                            {{ __('client/checkout.where_to_send') }}
                        </h2>
                        
                        {{-- BOTÓN DE DIRECCIONES GUARDADAS --}}
                        @if(Auth::check() && Auth::user()->addresses()->count() > 0)
                            <button type="button" onclick="abrirModalDirecciones()" class="text-xs bg-gray-700 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-xl transition-colors border border-gray-600 flex items-center gap-2">
                                <i class="fas fa-address-book text-orange-400"></i> {{ __('client/checkout.my_addresses_btn') }}
                            </button>
                        @endif
                    </div>

                    <div class="mb-5">
                        <label class="block text-gray-400 text-xs font-bold uppercase tracking-wider mb-2">{{ __('client/checkout.cellphone') }}</label>
                        
                        {{-- INTL-TEL-INPUT --}}
                        <input type="hidden" name="codigo_pais" id="codigo_pais_final" value="{{ old('codigo_pais', '+52') }}">
                        <div class="relative w-full">
                            <input type="tel" name="telefono" id="telefono_input" value="{{ old('telefono', Auth::check() ? Auth::user()->telefono : '') }}" placeholder="{{ __('client/checkout.cellphone_placeholder') }}"
                                class="w-full bg-gray-900 text-white rounded-xl py-3 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-orange-500 transition-all shadow-inner
                                @error('telefono') border-red-500 @enderror">
                        </div>
                        @error('telefono') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-5">
                        <div class="sm:col-span-2">
                            <label class="block text-gray-400 text-xs font-bold uppercase tracking-wider mb-2">{{ __('client/checkout.street') }}</label>
                            <input type="text" name="calle" id="calle_input" value="{{ old('calle') }}" placeholder="{{ __('client/checkout.street_placeholder') }}"
                                   class="w-full bg-gray-900 text-white rounded-xl px-4 py-3 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-orange-500 transition-all shadow-inner
                                   @error('calle') border-red-500 @enderror">
                        </div>
                        <div>
                            <label class="block text-gray-400 text-xs font-bold uppercase tracking-wider mb-2">{{ __('client/checkout.ext_num') }}</label>
                            <input type="text" name="numero" id="numero_input" value="{{ old('numero') }}" placeholder="{{ __('client/checkout.ext_num_placeholder') }}"
                                maxlength="5"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '');"
                                class="w-full bg-gray-900 text-white rounded-xl px-4 py-3 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-orange-500 transition-all shadow-inner
                                @error('numero') border-red-500 @enderror">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-5">
                        <div>
                            <label class="block text-gray-400 text-xs font-bold uppercase tracking-wider mb-2">{{ __('client/checkout.zip_code') }}</label>
                            <div class="relative">
                                <input type="text" id="cp_input" name="codigo_postal" value="{{ old('codigo_postal') }}" placeholder="50000" 
                                    maxlength="5"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '');"
                                    class="w-full bg-gray-900 text-white rounded-xl px-4 py-3 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-orange-500 transition-all shadow-inner
                                    @error('codigo_postal') border-red-500 @enderror">
                                <i id="cp_loading" class="fas fa-circle-notch fa-spin absolute right-3 top-3.5 text-orange-500 hidden"></i>
                            </div>
                        </div>
                        
                        <div id="colonia_container" class="sm:col-span-2 relative">
                            <label class="block text-gray-400 text-xs font-bold uppercase tracking-wider mb-2">{{ __('client/checkout.neighborhood') }}</label>
                            
                            <div class="relative">
                                <input type="text" id="colonia_input" autocomplete="off" placeholder="{{ __('client/checkout.neighborhood_placeholder') }}"
                                    class="w-full bg-gray-900 text-white rounded-xl px-4 py-3 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-orange-500 transition-all shadow-inner placeholder-gray-600">
                                
                                <ul id="colonia_autocomplete_list" class="hidden absolute z-50 w-full bg-gray-800 border border-gray-600 rounded-xl shadow-2xl mt-1 max-h-56 overflow-y-auto divide-y divide-gray-300">
                                </ul>
                            </div>

                            <div id="colonia_select_wrapper" class="hidden relative">
                                <select id="colonia_select" class="custom-select w-full bg-gray-900 text-white rounded-xl px-4 py-3 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-orange-500 transition-all shadow-inner cursor-pointer">
                                    <option value="" disabled selected>{{ __('client/checkout.select_neighborhood') }}</option>
                                </select>
                                <i class="fas fa-chevron-down absolute right-4 top-4 text-orange-500 pointer-events-none"></i>
                            </div>

                            <input type="hidden" name="colonia" id="colonia_final" value="{{ old('colonia') }}">
                            @error('colonia') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5 opacity-70">
                        <div>
                            <label class="block text-gray-500 text-xs font-bold uppercase tracking-wider mb-2">{{ __('client/checkout.municipality') }}</label>
                            <input type="text" name="municipio" id="municipio_input" readonly value="{{ old('municipio') }}" tabindex="-1"
                                   class="w-full bg-gray-900 text-gray-400 rounded-xl px-4 py-3 border border-gray-300 cursor-not-allowed">
                        </div>
                        <div>
                            <label class="block text-gray-500 text-xs font-bold uppercase tracking-wider mb-2">{{ __('client/checkout.state') }}</label>
                            <input type="text" name="estado" id="estado_input" readonly value="{{ old('estado') }}" tabindex="-1"
                                   class="w-full bg-gray-900 text-gray-400 rounded-xl px-4 py-3 border border-gray-300 cursor-not-allowed">
                        </div>
                    </div>

                    <div>
                        <label class="block text-gray-400 text-xs font-bold uppercase tracking-wider mb-2">{{ __('client/checkout.references') }}</label>
                        <textarea name="referencias" id="referencias_input" rows="2" placeholder="{{ __('client/checkout.references_placeholder') }}"
                                  class="w-full bg-gray-900 text-white rounded-xl px-4 py-3 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-orange-500 transition-all shadow-inner resize-none">{{ old('referencias') }}</textarea>
                    </div>

                    {{-- CHECKBOX PARA GUARDAR DIRECCIÓN --}}
                    @auth
                    <div class="mt-5 bg-gray-900/50 p-4 rounded-xl border border-gray-300/50">
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <div class="relative flex items-center justify-center shrink-0">
                                <input type="checkbox" name="guardar_direccion" value="1" class="peer appearance-none w-5 h-5 border-2 border-gray-600 rounded bg-gray-900 checked:bg-orange-500 checked:border-orange-500 transition-colors cursor-pointer">
                                <i class="fas fa-check absolute text-white text-xs opacity-0 peer-checked:opacity-100 transition-opacity pointer-events-none"></i>
                            </div>
                            <span class="text-sm text-gray-300 font-bold select-none group-hover:text-white transition-colors">
                                {{ __('client/checkout.save_address_checkbox') }}
                            </span>
                        </label>
                    </div>
                    @endauth

                </div>

                <div class="bg-gray-800 rounded-3xl p-6 sm:p-8 border border-gray-300 shadow-xl relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-1.5 h-full bg-orange-500"></div>
                    
                    <h2 class="text-xl font-bold text-white mb-6 flex items-center border-b border-gray-300 pb-4">
                        <span class="bg-gray-900 text-orange-500 w-8 h-8 rounded-full flex items-center justify-center mr-3 text-sm border border-gray-300">2</span>
                        {{ __('client/checkout.how_to_pay') }}
                    </h2>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <label class="relative flex flex-col items-center justify-center p-4 bg-gray-900 rounded-2xl border-2 cursor-pointer transition-all hover:bg-gray-800 group payment-label {{ old('metodo_pago', 'efectivo') == 'efectivo' ? 'border-orange-500 shadow-[0_0_15px_rgba(234,88,12,0.2)]' : 'border-gray-300' }}">
                            <input type="radio" name="metodo_pago" value="efectivo" class="sr-only peer payment-radio" {{ old('metodo_pago', 'efectivo') == 'efectivo' ? 'checked' : '' }}>
                            <i class="fas fa-money-bill-wave text-2xl mb-2 payment-icon transition-colors {{ old('metodo_pago', 'efectivo') == 'efectivo' ? 'text-green-400' : 'text-gray-500' }}"></i>
                            <span class="font-bold text-white text-sm text-center">{{ __('client/checkout.cash') }}</span>
                            <span class="text-[10px] text-gray-500 mt-1 text-center">{{ __('client/checkout.on_delivery') }}</span>
                            <i class="fas fa-check-circle check-icon absolute top-3 right-3 text-orange-500 text-lg transition-opacity duration-300 {{ old('metodo_pago', 'efectivo') == 'efectivo' ? 'opacity-100' : 'opacity-0' }}"></i>
                        </label>

                        <label class="relative flex flex-col items-center justify-center p-4 bg-gray-900 rounded-2xl border-2 cursor-pointer transition-all hover:bg-gray-800 group payment-label {{ old('metodo_pago') == 'tarjeta_entrega' ? 'border-orange-500 shadow-[0_0_15px_rgba(234,88,12,0.2)]' : 'border-gray-300' }}">
                            <input type="radio" name="metodo_pago" value="tarjeta_entrega" class="sr-only peer payment-radio" {{ old('metodo_pago') == 'tarjeta_entrega' ? 'checked' : '' }}>
                            <i class="fas fa-credit-card text-2xl mb-2 payment-icon transition-colors {{ old('metodo_pago') == 'tarjeta_entrega' ? 'text-blue-400' : 'text-gray-500' }}"></i>
                            <span class="font-bold text-white text-sm text-center">{{ __('client/checkout.terminal') }}</span>
                            <span class="text-[10px] text-gray-500 mt-1 text-center">{{ __('client/checkout.on_delivery') }}</span>
                            <i class="fas fa-check-circle check-icon absolute top-3 right-3 text-orange-500 text-lg transition-opacity duration-300 {{ old('metodo_pago') == 'tarjeta_entrega' ? 'opacity-100' : 'opacity-0' }}"></i>
                        </label>

                        <label class="relative flex flex-col items-center justify-center p-4 bg-gray-900 rounded-2xl border-2 cursor-pointer transition-all hover:bg-gray-800 group payment-label {{ old('metodo_pago') == 'stripe' ? 'border-orange-500 shadow-[0_0_15px_rgba(234,88,12,0.2)]' : 'border-gray-300' }}">
                            <input type="radio" name="metodo_pago" value="stripe" class="sr-only peer payment-radio" id="radio-stripe" {{ old('metodo_pago') == 'stripe' ? 'checked' : '' }}>
                            <i class="fas fa-lock text-2xl mb-2 payment-icon transition-colors {{ old('metodo_pago') == 'stripe' ? 'text-purple-400' : 'text-gray-500' }}"></i>
                            <span class="font-bold text-white text-sm text-center">{{ __('client/checkout.online') }}</span>
                            <span class="text-[10px] text-gray-500 mt-1 text-center">{{ __('client/checkout.secure_payment') }}</span>
                            <i class="fas fa-check-circle check-icon absolute top-3 right-3 text-orange-500 text-lg transition-opacity duration-300 {{ old('metodo_pago') == 'stripe' ? 'opacity-100' : 'opacity-0' }}"></i>
                        </label>
                    </div>

                    <div id="stripe-container" class="mt-6 p-5 bg-gray-900 rounded-xl border border-purple-500/30 hidden transition-all">
                        <label class="block text-gray-400 text-xs font-bold uppercase tracking-wider mb-3">
                            <i class="fas fa-shield-alt text-purple-400 mr-1"></i> {{ __('client/checkout.card_data') }}
                        </label>
                        
                        <div id="card-element" class="bg-gray-800 p-4 rounded-lg border border-gray-300"></div>
                        <div id="card-errors" role="alert" class="text-red-400 text-xs mt-2 font-bold"></div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-5">
                <div class="bg-gradient-to-b from-gray-800 to-gray-900 p-6 sm:p-8 rounded-3xl border border-gray-300 shadow-2xl sticky top-28 lg:top-24">
                    
                    <h3 class="text-xl font-black text-white mb-6 flex items-center">
                        <i class="fas fa-receipt text-gray-500 mr-2"></i> {{ __('client/checkout.summary') }}
                    </h3>
                    
                    <div class="space-y-4 mb-6 max-h-48 overflow-y-auto pr-2 scrollbar-thin scrollbar-thumb-gray-600">
                        @foreach($carrito as $item)
                            @php
                                $productoModel = \App\Models\Product::find($item['id_producto']);
                                $nombreItem = $productoModel ? $productoModel->nombre_traducido : $item['nombre'];
                            @endphp
                            <div class="flex justify-between items-center text-sm border-b border-gray-300/50 pb-3 last:border-0 last:pb-0">
                                <div class="flex items-center gap-3">
                                    <span class="bg-orange-700 text-white rounded-lg w-7 h-7 flex items-center justify-center text-xs font-bold">{{ $item['cantidad'] }}x</span>
                                    <span class="text-gray-300">{{ $nombreItem }}</span>
                                </div>
                                <span class="text-white font-bold">{{ formatCurrency($item['precio'] * $item['cantidad']) }}</span>
                            </div>
                        @endforeach
                    </div>

                    <div class="bg-gray-900/50 rounded-2xl p-5 border border-gray-300 space-y-3 mb-8">
                        <div class="flex justify-between text-gray-400 text-sm font-medium">
                            <span>{{ __('client/checkout.subtotal') }}</span>
                            <span>{{ formatCurrency($subtotal) }}</span>
                        </div>
                        
                        <div class="flex justify-between items-center text-sm font-medium">
                            <span class="text-gray-400">{{ __('client/checkout.delivery_fee') }}</span>
                            @if($envio == 0)
                                <span class="text-green-400 bg-green-500/10 px-2 py-0.5 rounded text-xs font-bold uppercase tracking-wider">{{ __('client/checkout.free') }}</span>
                            @else
                                <span class="text-white">{{ formatCurrency($envio) }}</span>
                            @endif
                        </div>
                        
                        <div class="border-t border-gray-300 border-dashed my-3"></div>

                        <div class="flex justify-between items-end">
                            <span class="text-gray-400 font-bold uppercase tracking-wider text-xs">{{ __('client/checkout.total_to_pay') }}</span>
                            <span class="text-2xl font-black text-transparent bg-clip-text bg-gradient-to-r from-orange-400 to-orange-700">
                                {{ formatCurrency($totalFinal) }}
                            </span>
                        </div>
                    </div>

                    {{-- INICIO DE SECCIÓN LEGAL --}}
                    <div class="mb-6 bg-gray-900/30 p-4 rounded-xl border border-gray-300">
                        <label class="flex items-start gap-3 cursor-pointer group">
                            <div class="relative flex items-center justify-center shrink-0 mt-0.5">
                                <input type="checkbox" id="legal_consent" name="legal_consent" class="peer appearance-none w-5 h-5 border-2 border-gray-300 rounded bg-gray-900 checked:bg-orange-500 checked:border-orange-500 transition-colors cursor-pointer">
                                <i class="fas fa-check absolute text-white text-xs opacity-0 peer-checked:opacity-100 transition-opacity pointer-events-none"></i>
                            </div>
                            <span class="text-xs text-gray-400 leading-relaxed select-none">
                                {{ __('client/checkout.consent_text') }} 
                                <a href="{{ route('terms') }}" target="_blank" class="text-orange-500 hover:text-orange-400 font-bold underline transition-colors">{{ __('client/checkout.terms_link') }}</a> 
                                {{ __('client/checkout.and') }} 
                                <a href="{{ route('privacy') }}" target="_blank" class="text-orange-500 hover:text-orange-400 font-bold underline transition-colors">{{ __('client/checkout.privacy_link') }}</a>.
                            </span>
                        </label>
                    </div>
                    {{-- FIN DE SECCIÓN LEGAL --}}

                    <button type="button" id="btn-confirmar" class="relative flex items-center justify-center w-full py-4 px-6 font-black text-white text-lg rounded-2xl shadow-xl transition-all duration-300 transform hover:-translate-y-1 hover:shadow-[0_15px_30px_-10px_rgba(234,88,12,0.5)] active:scale-95 group overflow-hidden bg-orange-700 hover:bg-orange-400 border border-orange-400">
                        <div class="absolute inset-0 w-full h-full bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:animate-shimmer"></div>
                        <span class="relative z-10 flex items-center">
                            <i class="fas fa-check-circle mr-3 transform group-hover:scale-110 transition-transform"></i>
                            {{ __('client/checkout.confirm_order') }}
                        </span>
                    </button>
                    
                    <p class="text-center text-[10px] text-gray-500 mt-4 font-medium uppercase tracking-wider">
                        {{ __('client/checkout.prepare_warning') }}
                    </p>
                </div>
            </div>
        </form> 
    </div>
</div>

{{-- MODAL DE DIRECCIONES GUARDADAS --}}
@auth
@if(Auth::user()->addresses()->count() > 0)
<div id="modal-direcciones" class="fixed inset-0 z-[200] hidden flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm transition-opacity duration-300">
    <div class="bg-gray-800 border border-gray-300 rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden transform scale-95 opacity-0 transition-all duration-300 flex flex-col max-h-[80vh]" id="modal-direcciones-panel">
        
        <div class="p-6 border-b border-gray-300 flex justify-between items-center bg-gray-900/50">
            <h3 class="text-xl font-bold text-white"><i class="fas fa-bookmark text-orange-500 mr-2"></i> {{ __('client/checkout.modal_addresses_title') }}</h3>
            <button onclick="cerrarModalDirecciones()" class="text-gray-400 hover:text-red-400 transition-colors w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-300">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="p-6 overflow-y-auto custom-scrollbar space-y-4 flex-1">
            @foreach(Auth::user()->addresses as $dir)
                <div class="bg-gray-900 border border-gray-300 rounded-2xl p-4 hover:border-orange-500/50 cursor-pointer transition-colors group relative"
                     onclick="seleccionarDireccion('{{ $dir->codigo_pais }}', '{{ $dir->telefono }}', '{{ $dir->calle }}', '{{ $dir->numero }}', '{{ $dir->codigo_postal }}', '{{ $dir->colonia }}', '{{ $dir->municipio }}', '{{ $dir->estado }}', '{{ $dir->referencias }}')">
                    
                    @if($dir->is_default)
                        <span class="absolute top-4 right-4 text-[10px] bg-orange-500/20 text-orange-500 font-bold px-2 py-1 rounded uppercase tracking-wider">{{ __('client/checkout.default_badge') }}</span>
                    @endif

                    <h4 class="font-bold text-white text-lg flex items-center gap-2 mb-2">
                        <i class="fas fa-map-marker-alt text-gray-500 group-hover:text-orange-500 transition-colors"></i> {{ $dir->alias }}
                    </h4>
                    
                    <p class="text-sm text-gray-400 leading-relaxed">
                        {{ $dir->calle }} #{{ $dir->numero }}<br>
                        {{ $dir->colonia }}, {{ $dir->codigo_postal }}<br>
                        {{ $dir->municipio }}, {{ $dir->estado }}
                    </p>
                    
                    <div class="mt-3 pt-3 border-t border-gray-800 text-xs text-gray-500 flex justify-between items-center">
                        <span><i class="fas fa-phone mr-1"></i> {{ $dir->codigo_pais }} {{ $dir->telefono }}</span>
                        <span class="text-orange-500 font-bold opacity-0 group-hover:opacity-100 transition-opacity">{{ __('client/checkout.use_this_address') }} <i class="fas fa-arrow-right ml-1"></i></span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endif
@endauth

<div id="toast-container" class="fixed bottom-5 right-5 z-50 flex flex-col gap-3 pointer-events-none"></div>

<script src="https://js.stripe.com/v3/"></script>
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@23.0.4/build/js/intlTelInput.min.js"></script>
@vite(['resources/css/checkout.css', 'resources/js/client/checkout.js'])
@endsection