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

<div class="min-h-screen py-10 px-4 sm:px-6 lg:px-8 flex justify-center bg-gray-50 dark:bg-slate-950">
    
    <div class="w-full max-w-5xl bg-white dark:bg-slate-900 rounded-3xl shadow-2xl overflow-hidden border border-gray-200 dark:border-slate-300 flex flex-col md:flex-row">
        
        <div class="w-full md:w-1/3 bg-gray-100 dark:bg-slate-950/50 p-8 flex flex-col items-center border-b md:border-b-0 md:border-r border-gray-200 dark:border-slate-300">
            <h2 class="text-xl font-bold text-gray-300 dark:text-white mb-6">{{ __('profile/edit.your_avatar') }}</h2>

            <div class="relative group mb-8">
                <div class="w-48 h-48 rounded-full border-4 border-white dark:border-slate-300 shadow-xl overflow-hidden relative bg-white">
                    <img id="avatar-preview" src="{{ Auth::user()->avatar_url }}" alt="Avatar de {{ Auth::user()->name }}" class="w-full h-full object-cover transition-transform duration-500 hover:scale-110">
                </div>
                <div class="absolute bottom-2 right-4 bg-orange-700 text-white p-2 rounded-full shadow-lg border-2 border-white dark:border-slate-300">
                    <i class="fas fa-camera"></i>
                </div>
            </div>

            <p class="text-sm text-gray-500 dark:text-slate-300 text-center mb-4 px-4">
                {{ __('profile/edit.avatar_desc') }}
            </p>
        </div>

        <div class="w-full md:w-2/3 p-8 md:p-12 bg-white dark:bg-slate-900">
            
            @if(session('success'))
                <div class="mb-6 bg-green-100 dark:bg-green-900/30 border-l-4 border-green-500 text-green-700 dark:text-green-400 p-4 rounded-lg text-sm flex items-center gap-3">
                    <i class="fas fa-check-circle text-xl"></i>
                    <div>
                        <strong>{{ __('profile/edit.success_title') }}</strong> {{ session('success') }}
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 bg-red-100 dark:bg-red-900/30 border-l-4 border-red-500 text-red-700 dark:text-red-400 p-4 rounded-lg text-sm">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="mb-8">
                    <label class="block text-sm font-bold text-gray-300 dark:text-slate-300 mb-3">{{ __('profile/edit.choose_character') }}</label>
                    
                    <div class="grid grid-cols-4 sm:grid-cols-8 gap-3 mb-4">
                        @foreach(range(1, 8) as $num)
                            @php 
                                $filename = "avatar_{$num}.png";
                                $url = asset('assets/avatars/' . $filename);
                                $isSelected = Auth::user()->avatar === $filename;
                            @endphp

                            <label class="cursor-pointer relative group">
                                <input type="radio" 
                                    name="avatar_preset" 
                                    value="{{ $filename }}" 
                                    class="peer sr-only" 
                                    aria-label="Seleccionar Avatar {{ $num }}"
                                    {{ $isSelected ? 'checked' : '' }}
                                    onchange="document.getElementById('avatar-preview').src='{{ $url }}'">
                                
                                <div class="w-12 h-12 rounded-full overflow-hidden border-2 border-transparent peer-checked:border-orange-500 peer-checked:ring-2 peer-checked:ring-orange-500/50 transition-all hover:scale-110">
                                    <img src="{{ $url }}" alt="Opción de Avatar {{ $num }}" class="w-full h-full object-cover">
                                </div>
                            </label>
                        @endforeach
                    </div>

                    <div class="mt-4">
                        <label for="avatar-upload" class="inline-flex items-center gap-2 cursor-pointer text-sm text-slate-500 hover:text-orange-500 font-bold transition">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <span>{{ __('profile/edit.upload_own') }}</span>
                        </label>
                        <input type="file" name="avatar_upload" id="avatar-upload" class="hidden" 
                            onchange="if(this.files && this.files[0]) { document.getElementById('avatar-preview').src = URL.createObjectURL(this.files[0]); }" 
                            accept="image/*">
                    </div>
                </div>

                <hr class="border-slate-200 dark:border-slate-300 my-8">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    
                    <div>
                        <label for="name" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">{{ __('profile/edit.name') }}</label>
                        <input type="text" name="name" id="name" value="{{ old('name', Auth::user()->name) }}" required
                               class="w-full px-4 py-3 rounded-xl border border-slate-300 dark:border-slate-300 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition font-medium">
                    </div>
                    
                    <div>
                        <label for="telefono" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">{{ __('profile/edit.phone') }}</label>
                        <input type="text" name="telefono" id="telefono" value="{{ old('telefono', Auth::user()->telefono) }}" placeholder="{{ __('profile/edit.phone_placeholder') }}"
                               class="w-full px-4 py-3 rounded-xl border border-slate-300 dark:border-slate-300 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition font-medium">
                    </div>
                    
                    <div class="md:col-span-2">
                        <label for="email" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">
                            {{ __('profile/edit.email') }} <span class="text-xs normal-case font-normal text-orange-500 ml-1">{{ __('profile/edit.not_editable') }}</span>
                        </label>
                        <div class="relative">
                            <i class="fas fa-lock absolute left-4 top-3.5 text-slate-400"></i>
                            <input type="email" id="email" value="{{ Auth::user()->email }}" disabled
                                   class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 dark:border-slate-300 bg-slate-100 dark:bg-slate-950 text-slate-500 dark:text-slate-400 cursor-not-allowed font-mono text-sm">
                        </div>
                    </div>
                </div>

                <div class="bg-orange-50 dark:bg-orange-900/10 rounded-2xl p-6 border border-orange-100 dark:border-orange-900/20 mb-8">
                    <h3 class="text-sm font-bold text-orange-700 dark:text-orange-400 uppercase tracking-wide mb-4 flex items-center gap-2">
                        <i class="fas fa-shield-alt"></i> {{ __('profile/edit.security') }}
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="password" class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">{{ __('profile/edit.new_password') }}</label>
                            <input type="password" name="password" id="password" placeholder="••••••••" 
                                   class="w-full px-4 py-3 rounded-xl border border-orange-200 dark:border-slate-300 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-orange-500 outline-none transition">
                        </div>
                        <div>
                            <label for="password_confirmation" class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">{{ __('profile/edit.confirm_password') }}</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" placeholder="••••••••" 
                                   class="w-full px-4 py-3 rounded-xl border border-orange-200 dark:border-slate-300 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-orange-500 outline-none transition">
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-4">
                    <a href="{{ route('home') }}" class="px-6 py-3 text-sm font-bold text-slate-700 dark:text-slate-300 hover:text-slate-800 dark:hover:text-white transition">{{ __('profile/edit.btn_cancel') }}</a>
                    
                    <button type="submit" 
                            class="bg-orange-700 hover:bg-orange-700 text-white font-bold py-3 px-8 rounded-xl shadow-lg shadow-orange-500/30 transform transition hover:-translate-y-0.5 active:scale-95 flex items-center gap-2">
                        <i class="fas fa-save"></i> {{ __('profile/edit.btn_save') }}
                    </button>
                </div>

            </form>

            {{-- ======================================================== --}}
            {{-- ZONA DE PELIGRO: ELIMINAR CUENTA (Derechos ARCO)         --}}
            {{-- ======================================================== --}}
            <hr class="border-slate-200 dark:border-slate-300 my-10">

            <div class="bg-red-50 dark:bg-red-900/10 rounded-2xl p-6 border border-red-100 dark:border-red-900/30">
                <h3 class="text-sm font-bold text-red-600 dark:text-red-500 uppercase tracking-wide mb-2 flex items-center gap-2">
                    <i class="fas fa-exclamation-triangle"></i> {{ __('profile/edit.danger_zone') }}
                </h3>
                
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mt-4">
                    <div>
                        <h4 class="font-bold text-slate-800 dark:text-white">{{ __('profile/edit.delete_account_title') }}</h4>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-md">
                            {{ __('profile/edit.delete_account_desc') }}
                        </p>
                    </div>
                    
                    <button type="button" onclick="document.getElementById('modal-delete-account').classList.remove('hidden')" 
                            class="shrink-0 bg-white dark:bg-slate-800 border border-red-200 dark:border-red-900/50 text-red-600 dark:text-red-500 hover:bg-red-600 hover:text-white dark:hover:bg-red-600 dark:hover:text-white font-bold py-2.5 px-6 rounded-xl transition-all shadow-sm">
                        <i class="fas fa-trash-alt mr-2"></i> {{ __('profile/edit.btn_delete_account') }}
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- MODAL DE CONFIRMACIÓN --}}
<div id="modal-delete-account" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm transition-opacity">
    <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl max-w-md w-full p-8 border border-slate-200 dark:border-slate-300 text-center relative overflow-hidden transform transition-all">
        
        <div class="absolute top-0 left-0 w-full h-1 bg-red-600"></div>

        <div class="w-16 h-16 bg-red-100 dark:bg-red-900/30 text-red-600 mx-auto rounded-full flex items-center justify-center text-3xl mb-6 shadow-inner">
            <i class="fas fa-skull-crossbones"></i>
        </div>

        <h3 class="text-2xl font-black text-slate-800 dark:text-white mb-2">{{ __('profile/edit.delete_modal_title') }}</h3>
        <p class="text-slate-500 dark:text-slate-400 text-sm mb-8 leading-relaxed">
            {{ __('profile/edit.delete_modal_desc') }}
        </p>

        <form action="{{ route('profile.destroy') }}" method="POST" class="flex flex-col sm:flex-row gap-3">
            @csrf
            @method('DELETE')
            <button type="button" onclick="document.getElementById('modal-delete-account').classList.add('hidden')" 
                    class="w-full sm:w-1/2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-white font-bold py-3 rounded-xl transition-colors">
                {{ __('profile/edit.delete_modal_cancel') }}
            </button>
            <button type="submit" 
                    class="w-full sm:w-1/2 bg-red-600 hover:bg-red-700 text-white font-bold py-3 rounded-xl transition-colors shadow-lg shadow-red-600/30">
                {{ __('profile/edit.delete_modal_confirm') }}
            </button>
        </form>
    </div>
</div>

@endsection
