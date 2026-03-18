@php
    $layout = 'layouts.employee'; 
    
    if (Auth::user()->rol == 'admin') {
        $layout = 'layouts.admin';
    } elseif (Auth::user()->rol == 'cliente') {
        $layout = 'layouts.app';
    }
@endphp

@extends($layout)

@section('titulo', __('profile/edit.title'))

@section('contenido')

@vite(['resources/css/profile.css', 'resources/js/profile.js'])

<div class="min-h-screen py-6 md:py-10 px-4 sm:px-6 lg:px-8 flex justify-center bg-gray-50 dark:bg-slate-950">
    
    <div class="w-full max-w-5xl bg-white dark:bg-slate-900 rounded-3xl shadow-2xl overflow-hidden border border-gray-200 dark:border-slate-300 flex flex-col md:flex-row">
        
        {{-- ========================================== --}}
        {{-- PANEL IZQUIERDO: AVATAR                    --}}
        {{-- ========================================== --}}
        <div class="w-full md:w-1/3 bg-gray-100 dark:bg-slate-950/50 p-6 md:p-8 flex flex-col items-center border-b md:border-b-0 md:border-r border-gray-200 dark:border-slate-300">
            <h2 class="text-lg md:text-xl font-bold text-gray-800 dark:text-white mb-4 md:mb-6">{{ __('profile/edit.your_avatar') }}</h2>

            <div class="relative group mb-4 md:mb-8">
                <div class="w-32 h-32 md:w-48 md:h-48 rounded-full border-4 border-white dark:border-slate-300 shadow-xl overflow-hidden relative bg-white">
                    <img id="avatar-preview" src="{{ Auth::user()->avatar_url }}" alt="Avatar de {{ Auth::user()->name }}" class="w-full h-full object-cover transition-transform duration-500 hover:scale-110">
                </div>
                <div class="absolute bottom-1 right-2 md:bottom-2 md:right-4 bg-orange-700 text-white p-2 rounded-full shadow-lg border-2 border-white dark:border-slate-300">
                    <i class="fas fa-camera text-sm md:text-base"></i>
                </div>
            </div>

            <p class="text-xs md:text-sm text-gray-500 dark:text-slate-400 text-center px-2">
                {{ __('profile/edit.avatar_desc') }}
            </p>
        </div>

        {{-- ========================================== --}}
        {{-- PANEL DERECHO: CONTENIDO CON PESTAÑAS      --}}
        {{-- ========================================== --}}
        <div class="w-full md:w-2/3 p-6 md:p-12 bg-white dark:bg-slate-900 flex flex-col h-full">
            
            {{-- MENÚ DE PESTAÑAS --}}
            <div class="flex gap-6 border-b border-gray-200 dark:border-slate-700 mb-6 overflow-x-auto hide-scrollbar">
                <button type="button" onclick="cambiarPestana('general')" id="tab-btn-general" class="pb-3 text-sm font-black border-b-2 transition-all border-orange-500 text-orange-600 dark:text-orange-500 whitespace-nowrap">
                    <i class="fas fa-user-circle mr-1"></i> General
                </button>
                
                {{-- CONDICIONAL: Solo los clientes ven la pestaña de Direcciones --}}
                @if(Auth::user()->rol == 'cliente')
                    <button type="button" onclick="cambiarPestana('direcciones')" id="tab-btn-direcciones" class="pb-3 text-sm font-bold border-b-2 transition-all border-transparent text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200 whitespace-nowrap">
                        <i class="fas fa-map-marker-alt mr-1"></i> {{ __('profile/edit.my_addresses') }}
                    </button>
                @endif

                <button type="button" onclick="cambiarPestana('seguridad')" id="tab-btn-seguridad" class="pb-3 text-sm font-bold border-b-2 transition-all border-transparent text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200 whitespace-nowrap">
                    <i class="fas fa-shield-alt mr-1"></i> Seguridad
                </button>
            </div>

            {{-- ALERTAS GLOBALES --}}
            @if(session('success'))
                <div class="mb-6 bg-green-100 dark:bg-green-900/30 border-l-4 border-green-500 text-green-700 dark:text-green-400 p-4 rounded-lg text-sm flex items-center gap-3">
                    <i class="fas fa-check-circle text-xl"></i>
                    <div><strong>{{ __('profile/edit.success_title') }}</strong> {{ session('success') }}</div>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 bg-red-100 dark:bg-red-900/30 border-l-4 border-red-500 text-red-700 dark:text-red-400 p-4 rounded-lg text-sm">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                    </ul>
                </div>
            @endif

            {{-- FORMULARIO PRINCIPAL (Envuelve General y Seguridad) --}}
            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="flex-1 flex flex-col">
                @csrf
                @method('PUT')

                {{-- PESTAÑA 1: GENERAL --}}
                <div id="tab-general" class="block animate-fade-in flex-1">
                    <div class="mb-8">
                        <label class="block text-sm font-bold text-gray-600 dark:text-slate-300 mb-3">{{ __('profile/edit.choose_character') }}</label>
                        <div class="grid grid-cols-4 sm:grid-cols-8 gap-3 mb-4">
                            @foreach(range(1, 8) as $num)
                                @php 
                                    $filename = "avatar_{$num}.png";
                                    $url = asset('assets/avatars/' . $filename);
                                    $isSelected = Auth::user()->avatar === $filename;
                                @endphp
                                <label class="cursor-pointer relative group">
                                    <input type="radio" name="avatar_preset" value="{{ $filename }}" class="peer sr-only" {{ $isSelected ? 'checked' : '' }} onchange="document.getElementById('avatar-preview').src='{{ $url }}'">
                                    <div class="w-12 h-12 rounded-full overflow-hidden border-2 border-transparent peer-checked:border-orange-500 peer-checked:ring-2 peer-checked:ring-orange-500/50 transition-all hover:scale-110">
                                        <img src="{{ $url }}" alt="Avatar {{ $num }}" class="w-full h-full object-cover">
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        <div class="mt-4">
                            <label for="avatar-upload" class="inline-flex items-center gap-2 cursor-pointer text-sm text-slate-500 hover:text-orange-500 font-bold transition">
                                <i class="fas fa-cloud-upload-alt"></i> <span>{{ __('profile/edit.upload_own') }}</span>
                            </label>
                            <input type="file" name="avatar_upload" id="avatar-upload" class="hidden" accept="image/*" onchange="if(this.files && this.files[0]) { document.getElementById('avatar-preview').src = URL.createObjectURL(this.files[0]); }">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">{{ __('profile/edit.name') }}</label>
                            <input type="text" name="name" value="{{ old('name', Auth::user()->name) }}" required class="w-full px-4 py-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-orange-500 outline-none transition font-medium">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">
                                {{ __('profile/edit.email') }} <span class="text-xs normal-case font-normal text-orange-500 ml-1">{{ __('profile/edit.not_editable') }}</span>
                            </label>
                            <div class="relative">
                                <i class="fas fa-lock absolute left-4 top-3.5 text-slate-400"></i>
                                <input type="email" value="{{ Auth::user()->email }}" disabled class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-900 text-slate-500 dark:text-slate-400 cursor-not-allowed font-mono text-sm">
                            </div>
                        </div>
                    </div>

                    <div class="mt-auto pt-6 border-t border-gray-100 dark:border-slate-800 flex justify-end gap-3">
                        <a href="{{ route('home') }}" class="px-6 py-3 text-sm font-bold text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition">{{ __('profile/edit.btn_cancel') }}</a>
                        <button type="submit" class="bg-orange-700 hover:bg-orange-500 text-white font-bold py-3 px-8 rounded-xl shadow-lg transition active:scale-95 flex items-center gap-2"><i class="fas fa-save"></i> {{ __('profile/edit.btn_save') }}</button>
                    </div>
                </div>

                {{-- PESTAÑA 3: SEGURIDAD --}}
                <div id="tab-seguridad" class="hidden animate-fade-in flex-1">
                    <div class="bg-orange-50 dark:bg-orange-900/10 rounded-2xl p-6 border border-orange-100 dark:border-orange-900/20 mb-8">
                        <h3 class="text-sm font-bold text-orange-700 dark:text-orange-400 uppercase tracking-wide mb-4 flex items-center gap-2">
                            <i class="fas fa-key"></i> Actualizar Contraseña
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">{{ __('profile/edit.new_password') }}</label>
                                <input type="password" name="password" placeholder="••••••••" class="w-full px-4 py-3 rounded-xl border border-orange-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-orange-500 outline-none transition">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">{{ __('profile/edit.confirm_password') }}</label>
                                <input type="password" name="password_confirmation" placeholder="••••••••" class="w-full px-4 py-3 rounded-xl border border-orange-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-orange-500 outline-none transition">
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end">
                            <button type="submit" class="bg-orange-700 hover:bg-orange-500 text-white font-bold py-2.5 px-6 rounded-xl shadow-md transition active:scale-95 text-sm"><i class="fas fa-save mr-1"></i> Actualizar Contraseña</button>
                        </div>
                    </div>

                    {{-- ZONA DE PELIGRO --}}
                    <div class="bg-red-50 dark:bg-red-900/10 rounded-2xl p-6 border border-red-100 dark:border-red-900/30 mt-auto">
                        <h3 class="text-sm font-bold text-red-600 dark:text-red-500 uppercase tracking-wide mb-2 flex items-center gap-2">
                            <i class="fas fa-exclamation-triangle"></i> {{ __('profile/edit.danger_zone') }}
                        </h3>
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mt-4">
                            <div>
                                <h4 class="font-bold text-slate-800 dark:text-white">{{ __('profile/edit.delete_account_title') }}</h4>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-md">{{ __('profile/edit.delete_account_desc') }}</p>
                            </div>
                            <button type="button" onclick="document.getElementById('modal-delete-account').classList.remove('hidden')" class="shrink-0 bg-white dark:bg-slate-800 border border-red-200 dark:border-red-900/50 text-red-600 dark:text-red-500 hover:bg-red-600 hover:text-white dark:hover:bg-red-600 dark:hover:text-white font-bold py-2.5 px-6 rounded-xl transition-all shadow-sm">
                                <i class="fas fa-trash-alt mr-2"></i> {{ __('profile/edit.btn_delete_account') }}
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            {{-- CONDICIONAL: PESTAÑA 2 SÓLO PARA CLIENTES --}}
            @if(Auth::user()->rol == 'cliente')
            <div id="tab-direcciones" class="hidden animate-fade-in flex-1">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white">{{ __('profile/edit.my_addresses') }}</h3>
                    <button type="button" onclick="abrirModalNuevaDireccion()" class="text-xs bg-gray-900 dark:bg-white text-white dark:text-slate-900 hover:bg-orange-600 dark:hover:bg-orange-500 font-bold py-2 px-4 rounded-xl transition shadow-md flex items-center gap-2">
                        <i class="fas fa-plus"></i> {{ __('profile/edit.add_address_btn') }}
                    </button>
                </div>

                @if(Auth::user()->addresses->count() > 0)
                    <div class="grid grid-cols-1 gap-4">
                        @foreach(Auth::user()->addresses as $dir)
                            <div class="bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-2xl p-5 relative group">
                                @if($dir->is_default)
                                    <span class="absolute top-4 right-4 text-[9px] bg-orange-500/20 text-orange-500 font-bold px-2 py-1 rounded uppercase tracking-wider">{{ __('profile/edit.default_badge') }}</span>
                                @endif
                                
                                <h4 class="font-bold text-slate-800 dark:text-white mb-2 pr-16 flex items-center gap-2"><i class="fas fa-map-marker-alt text-gray-400"></i> {{ $dir->alias }}</h4>
                                <p class="text-sm text-slate-500 dark:text-slate-400 mb-4 leading-relaxed pl-6">
                                    {{ $dir->calle }} #{{ $dir->numero }}<br>
                                    {{ $dir->colonia }}, {{ $dir->codigo_postal }}<br>
                                    {{ $dir->municipio }}, {{ $dir->estado }}<br>
                                    <span class="text-xs mt-1 block text-slate-600 dark:text-slate-500"><i class="fas fa-phone mr-1"></i> {{ $dir->codigo_pais }} {{ $dir->telefono }}</span>
                                </p>

                                <div class="flex items-center gap-2 pt-3 border-t border-gray-200 dark:border-slate-700">
                                    <button type="button" onclick="abrirModalEditarDireccion({{ $dir }})" class="text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400 font-bold flex items-center gap-1 transition"><i class="fas fa-edit"></i> {{ __('profile/edit.edit') }}</button>
                                    <span class="text-gray-300 dark:text-slate-600">|</span>
                                    <button type="button" onclick="abrirModalEliminarDireccion({{ $dir->id }})" class="text-xs text-red-500 hover:text-red-700 dark:text-red-400 font-bold flex items-center gap-1 transition"><i class="fas fa-trash-alt"></i> {{ __('profile/edit.delete') }}</button>

                                    @if(!$dir->is_default)
                                        <span class="text-gray-300 dark:text-slate-600">|</span>
                                        <form action="{{ route('profile.address.default', $dir->id) }}" method="POST" class="inline ml-auto">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="text-xs text-orange-500 hover:text-orange-700 font-bold flex items-center gap-1 transition"><i class="fas fa-star"></i> {{ __('profile/edit.make_default') }}</button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-10 bg-gray-50 dark:bg-slate-800/50 rounded-2xl border border-dashed border-gray-300 dark:border-slate-700">
                        <div class="w-12 h-12 bg-gray-200 dark:bg-slate-700 text-gray-400 dark:text-slate-500 rounded-full flex items-center justify-center text-xl mx-auto mb-3"><i class="fas fa-map-marker-alt"></i></div>
                        <h4 class="text-slate-700 dark:text-white font-bold text-sm">{{ __('profile/edit.no_addresses') }}</h4>
                        <p class="text-xs text-slate-500 mt-1">{{ __('profile/edit.no_addresses_desc') }}</p>
                    </div>
                @endif
            </div>
            @endif

        </div>
    </div>
</div>

{{-- MODAL DE CONFIRMACIÓN DE CUENTA --}}
<div id="modal-delete-account" class="fixed inset-0 z-[60] hidden flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm transition-opacity">
    <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl max-w-md w-full p-8 border border-slate-200 dark:border-slate-300 text-center relative overflow-hidden transform transition-all">
        <div class="absolute top-0 left-0 w-full h-1 bg-red-600"></div>
        <div class="w-16 h-16 bg-red-100 dark:bg-red-900/30 text-red-600 mx-auto rounded-full flex items-center justify-center text-3xl mb-6 shadow-inner"><i class="fas fa-skull-crossbones"></i></div>
        <h3 class="text-2xl font-black text-slate-800 dark:text-white mb-2">{{ __('profile/edit.delete_modal_title') }}</h3>
        <p class="text-slate-500 dark:text-slate-400 text-sm mb-8 leading-relaxed">{{ __('profile/edit.delete_modal_desc') }}</p>
        <form action="{{ route('profile.destroy') }}" method="POST" class="flex flex-col sm:flex-row gap-3">
            @csrf @method('DELETE')
            <button type="button" onclick="document.getElementById('modal-delete-account').classList.add('hidden')" class="w-full sm:w-1/2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-white font-bold py-3 rounded-xl transition-colors">{{ __('profile/edit.delete_modal_cancel') }}</button>
            <button type="submit" class="w-full sm:w-1/2 bg-red-600 hover:bg-red-700 text-white font-bold py-3 rounded-xl transition-colors shadow-lg shadow-red-600/30">{{ __('profile/edit.delete_modal_confirm') }}</button>
        </form>
    </div>
</div>

{{-- CONDICIONAL: MODALES DE DIRECCIÓN SÓLO PARA CLIENTES --}}
@if(Auth::user()->rol == 'cliente')
    {{-- MODAL NUEVA DIRECCIÓN --}}
    <div id="modal-nueva-direccion" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm transition-opacity">
        <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl max-w-2xl w-full p-8 border border-slate-200 dark:border-slate-700 relative overflow-hidden transform transition-all max-h-[90vh] flex flex-col">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-black text-slate-800 dark:text-white flex items-center gap-2"><i class="fas fa-map-marker-alt text-orange-500"></i> {{ __('profile/edit.add_address_title') }}</h3>
                <button onclick="document.getElementById('modal-nueva-direccion').classList.add('hidden')" class="text-slate-400 hover:text-red-500 transition-colors"><i class="fas fa-times text-xl"></i></button>
            </div>
            <div class="overflow-y-auto custom-scrollbar pr-2 flex-1">
                {{-- Agregamos el ID "form-nueva-direccion" para blindarlo --}}
                <form id="form-nueva-direccion" action="{{ route('profile.address.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">{{ __('profile/edit.alias') }}</label>
                            <input type="text" name="alias" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-orange-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">{{ __('profile/edit.phone') }}</label>
                            <input type="text" name="telefono" maxlength="10" oninput="this.value = this.value.replace(/[^0-9]/g, '');" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-orange-500 outline-none">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">{{ __('profile/edit.street') }}</label>
                            <input type="text" name="calle" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-orange-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">{{ __('profile/edit.ext_num') }}</label>
                            <input type="text" name="numero" maxlength="5" oninput="this.value = this.value.replace(/[^0-9]/g, '');" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-orange-500 outline-none">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">{{ __('profile/edit.zip_code') }}</label>
                            <div class="relative">
                                <input type="text" name="codigo_postal" id="add_cp" maxlength="5" oninput="this.value = this.value.replace(/[^0-9]/g, '');" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-orange-500 outline-none">
                                <i id="add_loading" class="fas fa-circle-notch fa-spin absolute right-3 top-3.5 text-orange-500 hidden"></i>
                            </div>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">{{ __('profile/edit.neighborhood') }}</label>
                            <input type="text" name="colonia" id="add_colonia" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-orange-500 outline-none">
                            <select id="add_colonia_select" name="colonia_select" class="hidden w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-orange-500 outline-none"></select>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">{{ __('profile/edit.municipality') }}</label>
                            <input type="text" name="municipio" id="add_municipio" readonly required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-gray-200 dark:bg-slate-900 text-slate-500 cursor-not-allowed outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">{{ __('profile/edit.state') }}</label>
                            <input type="text" name="estado" id="add_estado" readonly required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-gray-200 dark:bg-slate-900 text-slate-500 cursor-not-allowed outline-none">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">{{ __('profile/edit.references') }}</label>
                        <textarea name="referencias" rows="2" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-orange-500 outline-none resize-none"></textarea>
                    </div>
                    <button type="submit" class="w-full bg-orange-700 hover:bg-orange-500 text-white font-bold py-3.5 rounded-xl transition-all shadow-lg mt-4">{{ __('profile/edit.save_address') }}</button>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL EDITAR DIRECCIÓN --}}
    <div id="modal-edit-direccion" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm transition-opacity">
        <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl max-w-2xl w-full p-8 border border-slate-200 dark:border-slate-700 relative overflow-hidden transform transition-all max-h-[90vh] flex flex-col">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-black text-slate-800 dark:text-white flex items-center gap-2"><i class="fas fa-edit text-orange-500"></i> Editar Dirección</h3>
                <button onclick="document.getElementById('modal-edit-direccion').classList.add('hidden')" class="text-slate-400 hover:text-red-500 transition-colors"><i class="fas fa-times text-xl"></i></button>
            </div>
            <div class="overflow-y-auto custom-scrollbar pr-2 flex-1">
                <form id="form-edit-address" action="" method="POST" class="space-y-4">
                    @csrf @method('PUT')
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">{{ __('profile/edit.alias') }}</label>
                            <input type="text" name="alias" id="edit_alias" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-orange-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">{{ __('profile/edit.phone') }}</label>
                            <input type="text" name="telefono" id="edit_telefono" maxlength="10" oninput="this.value = this.value.replace(/[^0-9]/g, '');" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-orange-500 outline-none">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">{{ __('profile/edit.street') }}</label>
                            <input type="text" name="calle" id="edit_calle" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-orange-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">{{ __('profile/edit.ext_num') }}</label>
                            <input type="text" name="numero" id="edit_numero" maxlength="5" oninput="this.value = this.value.replace(/[^0-9]/g, '');" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-orange-500 outline-none">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">{{ __('profile/edit.zip_code') }}</label>
                            <div class="relative">
                                <input type="text" name="codigo_postal" id="edit_cp" maxlength="5" oninput="this.value = this.value.replace(/[^0-9]/g, '');" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-orange-500 outline-none">
                                <i id="edit_loading" class="fas fa-circle-notch fa-spin absolute right-3 top-3.5 text-orange-500 hidden"></i>
                            </div>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">{{ __('profile/edit.neighborhood') }}</label>
                            <input type="text" name="colonia" id="edit_colonia" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-orange-500 outline-none">
                            <select id="edit_colonia_select" name="colonia_select" class="hidden w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-orange-500 outline-none"></select>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">{{ __('profile/edit.municipality') }}</label>
                            <input type="text" name="municipio" id="edit_municipio" readonly required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-gray-200 dark:bg-slate-900 text-slate-500 cursor-not-allowed outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">{{ __('profile/edit.state') }}</label>
                            <input type="text" name="estado" id="edit_estado" readonly required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-gray-200 dark:bg-slate-900 text-slate-500 cursor-not-allowed outline-none">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">{{ __('profile/edit.references') }}</label>
                        <textarea name="referencias" id="edit_referencias" rows="2" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-orange-500 outline-none resize-none"></textarea>
                    </div>
                    <button type="submit" class="w-full bg-orange-700 hover:bg-orange-500 text-white font-bold py-3.5 rounded-xl transition-all shadow-lg mt-4">Guardar Cambios</button>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL ELIMINAR DIRECCIÓN --}}
    <div id="modal-delete-address" class="fixed inset-0 z-[60] hidden flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm transition-opacity">
        <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl max-w-sm w-full p-8 border border-slate-200 dark:border-slate-300 text-center relative overflow-hidden transform transition-all">
            <div class="w-16 h-16 bg-red-100 dark:bg-red-900/30 text-red-600 mx-auto rounded-full flex items-center justify-center text-3xl mb-6 shadow-inner">
                <i class="fas fa-trash-alt"></i>
            </div>
            <h3 class="text-xl font-black text-slate-800 dark:text-white mb-2">{{ __('profile/edit.delete_confirm') }}</h3>
            <p class="text-slate-500 text-sm mb-6">Esta acción no se puede deshacer.</p>
            <form id="form-delete-address" action="" method="POST" class="flex flex-col gap-3">
                @csrf @method('DELETE')
                <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 rounded-xl transition-colors shadow-lg shadow-red-600/30">Sí, eliminar</button>
                <button type="button" onclick="document.getElementById('modal-delete-address').classList.add('hidden')" class="w-full bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-white font-bold py-3 rounded-xl transition-colors">Cancelar</button>
            </form>
        </div>
    </div>
@endif

{{-- ESTILOS EXTRAS PARA LAS PESTAÑAS --}}
<style>
    .hide-scrollbar::-webkit-scrollbar { display: none; }
    .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    .animate-fade-in { animation: fadeIn 0.3s ease-in-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
</style>

{{-- SCRIPTS DE LAS PESTAÑAS Y MODALES --}}
<script>
    // CONTROL DE PESTAÑAS
    function cambiarPestana(pestana) {
        // Ocultar todos
        document.getElementById('tab-general').classList.add('hidden');
        document.getElementById('tab-seguridad').classList.add('hidden');
        
        const tabDirecciones = document.getElementById('tab-direcciones');
        if(tabDirecciones) tabDirecciones.classList.add('hidden');

        // Resetear estilos botones
        const btns = ['general', 'seguridad'];
        if(document.getElementById('tab-btn-direcciones')) btns.push('direcciones');
        
        btns.forEach(b => {
            const btn = document.getElementById('tab-btn-' + b);
            if(btn) {
                btn.classList.remove('border-orange-500', 'text-orange-600', 'dark:text-orange-500', 'font-black');
                btn.classList.add('border-transparent', 'text-gray-500', 'font-bold');
            }
        });

        // Mostrar el seleccionado
        const currentTab = document.getElementById('tab-' + pestana);
        if(currentTab) currentTab.classList.remove('hidden');
        
        const btnActivo = document.getElementById('tab-btn-' + pestana);
        if(btnActivo) {
            btnActivo.classList.remove('border-transparent', 'text-gray-500', 'font-bold');
            btnActivo.classList.add('border-orange-500', 'text-orange-600', 'dark:text-orange-500', 'font-black');
        }
    }

    // MODALES DE DIRECCIONES
    function abrirModalNuevaDireccion() {
        document.getElementById('modal-nueva-direccion').classList.remove('hidden');
    }

    function abrirModalEditarDireccion(dir) {
        document.getElementById('form-edit-address').action = '/perfil/direccion/' + dir.id;
        document.getElementById('edit_alias').value = dir.alias;
        document.getElementById('edit_telefono').value = dir.telefono;
        document.getElementById('edit_calle').value = dir.calle;
        document.getElementById('edit_numero').value = dir.numero;
        document.getElementById('edit_cp').value = dir.codigo_postal;
        document.getElementById('edit_colonia').value = dir.colonia;
        document.getElementById('edit_municipio').value = dir.municipio;
        document.getElementById('edit_estado').value = dir.estado;
        document.getElementById('edit_referencias').value = dir.referencias || '';
        document.getElementById('modal-edit-direccion').classList.remove('hidden');
    }

    function abrirModalEliminarDireccion(id) {
        document.getElementById('form-delete-address').action = '/perfil/direccion/' + id;
        document.getElementById('modal-delete-address').classList.remove('hidden');
    }

    // API CÓDIGO POSTAL
    function iniciarApiCP(prefix) {
        const cpInput = document.getElementById(prefix + '_cp');
        const estInput = document.getElementById(prefix + '_estado');
        const munInput = document.getElementById(prefix + '_municipio');
        const colInput = document.getElementById(prefix + '_colonia');
        const colSelect = document.getElementById(prefix + '_colonia_select');
        const loadingMsg = document.getElementById(prefix + '_loading');

        if (!cpInput) return;

        cpInput.addEventListener('input', async function() {
            const cp = this.value.trim();
            if (cp.length === 5) {
                try {
                    loadingMsg.classList.remove('hidden');
                    const response = await fetch(`/api/zip-codes/${cp}`);
                    if (!response.ok) throw new Error('CP Error');
                    const data = await response.json();

                    estInput.value = data.estado;
                    munInput.value = data.municipio;

                    colInput.classList.add('hidden');
                    colSelect.classList.remove('hidden');
                    colSelect.innerHTML = '<option value="" disabled selected>Selecciona tu colonia</option>';
                    
                    data.colonias.forEach(col => {
                        colSelect.innerHTML += `<option value="${col}">${col}</option>`;
                    });
                } catch (error) {
                    estInput.value = ''; munInput.value = '';
                    colSelect.classList.add('hidden');
                    colInput.classList.remove('hidden');
                } finally {
                    loadingMsg.classList.add('hidden');
                }
            } else {
                colSelect.classList.add('hidden');
                colInput.classList.remove('hidden');
            }
        });

        if (colSelect) {
            colSelect.addEventListener('change', function() {
                colInput.value = this.value;
            });
        }
    }

    // FUNCIÓN PARA PREVENIR DOBLE CLIC (BLINDAJE DE FORMULARIOS)
    function blindarFormularioDireccion(formId) {
        const form = document.getElementById(formId);
        if (!form) return;
        
        form.addEventListener('submit', function(e) {
            const btn = this.querySelector('button[type="submit"]');
            
            // Si el formulario cumple con las validaciones (required, etc.)
            if (this.checkValidity()) {
                // Si ya lo estamos procesando, bloqueamos el evento
                if (btn.dataset.submitting === 'true') {
                    e.preventDefault();
                    return;
                }
                
                // Marcamos como procesando y cambiamos la apariencia
                btn.dataset.submitting = 'true';
                btn.disabled = true;
                btn.classList.add('opacity-75', 'cursor-not-allowed');
                btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Procesando...';
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        iniciarApiCP('add');
        iniciarApiCP('edit');
        
        // Aplicamos el blindaje a los formularios de direcciones
        blindarFormularioDireccion('form-nueva-direccion');
        blindarFormularioDireccion('form-edit-address');
    });
</script>

@endsection