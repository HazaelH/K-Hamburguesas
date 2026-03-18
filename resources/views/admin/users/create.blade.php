@extends('layouts.admin')

@section('titulo', __('admin/users/users.title_create'))

@vite(['resources/js/admin/users.js'])

@section('contenido')
<div class="max-w-4xl mx-auto py-8">
    
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-white">
            <i class="fas fa-user-plus text-blue-500 mr-2"></i> {{ __('admin/users/users.title_create') }}
        </h1>
        <a href="{{ route('admin.users.index') }}" class="text-slate-400 hover:text-white transition flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> {{ __('admin/users/users.back') }}
        </a>
    </div>

    <div class="bg-slate-800 rounded-2xl shadow-xl border border-slate-300 p-8">
        <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                
                <div class="space-y-6">
                    <div>
                        {{-- Accesibilidad: Agregado for --}}
                        <label for="name" class="block text-sm font-bold text-slate-300 mb-2">{{ __('admin/users/users.label_name') }}</label>
                        <div class="relative">
                            <span class="absolute left-3 top-3 text-slate-500"><i class="fas fa-user"></i></span>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" required placeholder="{{ __('admin/users/users.placeholder_name') }}"
                                   oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]/g, '');" 
                                   class="w-full bg-slate-900 text-white border border-slate-300 rounded-lg p-3 pl-10 focus:outline-none focus:border-blue-500 transition">
                        </div>
                        @error('name') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        {{-- Accesibilidad: Agregado for --}}
                        <label for="email" class="block text-sm font-bold text-slate-300 mb-2">{{ __('admin/users/users.label_email') }}</label>
                        <div class="relative">
                            <span class="absolute left-3 top-3 text-slate-500"><i class="fas fa-envelope"></i></span>
                            {{-- Accesibilidad: Agregado id --}}
                            <input type="email" id="email" name="email" value="{{ old('email') }}" required placeholder="{{ __('admin/users/users.placeholder_email') }}"
                                   class="w-full bg-slate-900 text-white border border-slate-300 rounded-lg p-3 pl-10 focus:outline-none focus:border-blue-500 transition">
                        </div>
                        @error('email') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- SECCIÓN DE ROL Y CONTRASEÑAS --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        {{-- Rol --}}
                        <div class="md:col-span-2">
                            {{-- Accesibilidad: Agregado for --}}
                            <label for="rol" class="block text-sm font-bold text-slate-300 mb-2">{{ __('admin/users/users.label_role') }}</label>
                            <div class="relative">
                                <span class="absolute left-3 top-3 text-slate-500"><i class="fas fa-id-badge"></i></span>
                                {{-- Accesibilidad: Agregado id --}}
                                <select id="rol" name="rol" class="w-full bg-slate-900 text-white border border-slate-300 rounded-lg p-3 pl-10 focus:outline-none focus:border-blue-500 transition appearance-none">
                                    <option value="cliente" {{ old('rol') == 'cliente' ? 'selected' : '' }}>{{ __('admin/users/users.role_cliente') }}</option>
                                    <option value="mesero" {{ old('rol') == 'mesero' ? 'selected' : '' }}>{{ __('admin/users/users.role_mesero') }}</option>
                                    <option value="cajero" {{ old('rol') == 'cajero' ? 'selected' : '' }}>{{ __('admin/users/users.role_cajero') }}</option>
                                    <option value="cocinero" {{ old('rol') == 'cocinero' ? 'selected' : '' }}>{{ __('admin/users/users.role_cocinero') }}</option>
                                    <option value="repartidor" {{ old('rol') == 'repartidor' ? 'selected' : '' }}>{{ __('admin/users/users.role_repartidor') }}</option>
                                    <option value="admin" {{ old('rol') == 'admin' ? 'selected' : '' }}>{{ __('admin/users/users.role_admin') }}</option>
                                </select>
                            </div>
                        </div>

                        {{-- Contraseña --}}
                        <div>
                            {{-- Accesibilidad: Agregado for --}}
                            <label for="create-password" class="block text-sm font-bold text-slate-300 mb-2">{{ __('admin/users/users.label_password') }}</label>
                            <div class="relative">
                                <span class="absolute left-3 top-3 text-slate-500"><i class="fas fa-lock"></i></span>
                                <input type="password" name="password" id="create-password" required placeholder="{{ __('admin/users/users.placeholder_password_create') }}"
                                    class="w-full bg-slate-900 text-white border border-slate-300 rounded-lg p-3 pl-10 pr-10 focus:outline-none focus:border-blue-500 transition">
                                {{-- Accesibilidad: aria-label y aumento de área táctil (p-2) --}}
                                <button type="button" aria-label="Mostrar u ocultar contraseña" onclick="togglePassword('create-password', 'eye-create')" class="absolute right-1.5 top-1.5 text-slate-500 hover:text-blue-500 transition-colors focus:outline-none p-2 rounded-lg hover:bg-slate-800">
                                    <i id="eye-create" class="fas fa-eye pointer-events-none"></i>
                                </button>
                            </div>
                            @error('password') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Confirmar Contraseña --}}
                        <div>
                            {{-- Accesibilidad: Agregado for --}}
                            <label for="create-password-confirm" class="block text-sm font-bold text-slate-300 mb-2">{{ __('admin/users/users.label_password_confirmation') }}</label>
                            <div class="relative">
                                <span class="absolute left-3 top-3 text-slate-500"><i class="fas fa-check-double"></i></span>
                                <input type="password" name="password_confirmation" id="create-password-confirm" required placeholder="{{ __('admin/users/users.placeholder_password_confirm') }}"
                                    class="w-full bg-slate-900 text-white border border-slate-300 rounded-lg p-3 pl-10 pr-10 focus:outline-none focus:border-blue-500 transition">
                                {{-- Accesibilidad: aria-label y aumento de área táctil (p-2) --}}
                                <button type="button" aria-label="Mostrar u ocultar confirmación de contraseña" onclick="togglePassword('create-password-confirm', 'eye-create-conf')" class="absolute right-1.5 top-1.5 text-slate-500 hover:text-blue-500 transition-colors focus:outline-none p-2 rounded-lg hover:bg-slate-800">
                                    <i id="eye-create-conf" class="fas fa-eye pointer-events-none"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="border-l border-slate-300 pl-8 space-y-6">
                    <p class="block text-sm font-bold text-slate-300">{{ __('admin/users/users.profile_image') }}</p>

                    <div>
                        <p class="text-xs text-slate-500 mb-3 uppercase font-bold tracking-wider">{{ __('admin/users/users.choose_character') }}</p>
                        
                        <div class="grid grid-cols-4 gap-3">
                            @foreach(range(1, 8) as $num)
                                @php $av = "avatar_{$num}.png"; @endphp
                                <label class="cursor-pointer group relative">
                                    <input type="radio" name="avatar_option" value="{{ $av }}" class="peer sr-only">
                                    {{-- Accesibilidad: Agregado alt descriptivo --}}
                                    <img src="{{ asset('assets/avatars/' . $av) }}" 
                                         alt="Opción de avatar predeterminado {{ $num }}"
                                         class="w-12 h-12 rounded-full border-2 border-slate-300 grayscale peer-checked:grayscale-0 peer-checked:border-orange-500 peer-checked:scale-110 transition-all hover:border-slate-400">
                                    <div class="absolute -top-1 -right-1 bg-orange-500 text-white text-[10px] w-4 h-4 rounded-full items-center justify-center hidden peer-checked:flex shadow-sm">
                                        <i class="fas fa-check"></i>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex items-center gap-4 py-2">
                        <span class="h-px bg-slate-700 flex-1"></span>
                        <span class="text-xs text-slate-500 font-bold uppercase">{{ __('admin/users/users.or_upload') }}</span>
                        <span class="h-px bg-slate-700 flex-1"></span>
                    </div>

                    <div>
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 rounded-full bg-slate-700 border border-slate-300 overflow-hidden relative shrink-0">
                                {{-- Accesibilidad: Agregado alt --}}
                                <img id="preview-avatar" src="#" alt="Previsualización de la foto de perfil" class="w-full h-full object-cover hidden">
                                <div id="icon-avatar" class="w-full h-full flex items-center justify-center text-slate-500">
                                    <i class="fas fa-camera text-xl"></i>
                                </div>
                            </div>
                            <div class="flex-1">
                                {{-- Accesibilidad: Label asociado al input file --}}
                                <label for="foto_custom" class="sr-only">Subir foto desde dispositivo</label>
                                <input type="file" name="foto_custom" id="foto_custom" accept="image/*"
                                       class="block w-full text-sm text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-slate-700 file:text-white hover:file:bg-slate-600 cursor-pointer focus:outline-none">
                            </div>
                        </div>
                        <p class="text-[10px] text-slate-500 mt-2">{{ __('admin/users/users.upload_formats') }}</p>
                    </div>
                </div>
            </div>

            <div class="border-t border-slate-300 pt-6">
                <button type="submit" 
                        class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-3 rounded-xl shadow-lg transform transition hover:scale-[1.01]">
                    <i class="fas fa-save mr-2 pointer-events-none"></i> {{ __('admin/users/users.btn_save_user') }}
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function togglePassword(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }
</script>
@endsection