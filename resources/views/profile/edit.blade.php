@php
    $layout = 'layouts.employee'; // Por defecto (Empleado)
    
    if (Auth::user()->rol == 'admin') {
        $layout = 'layouts.admin';
    } elseif (Auth::user()->rol == 'cliente') {
        $layout = 'layouts.app';
    }
@endphp

@extends($layout)

@section('titulo', 'Editar Perfil')

@section('contenido')

@vite(['resources/css/profile.css', 'resources/js/profile.js'])

<div class="min-h-screen py-10 px-4 sm:px-6 lg:px-8 flex justify-center">
    
    <div class="w-full max-w-4xl bg-white dark:bg-slate-900 rounded-3xl shadow-2xl overflow-hidden border border-gray-200 dark:border-slate-700 flex flex-col md:flex-row">
        
        {{-- COLUMNA IZQUIERDA: AVATAR --}}
        <div class="w-full md:w-1/3 bg-gray-50 dark:bg-slate-800 p-8 flex flex-col items-center border-b md:border-b-0 md:border-r border-gray-200 dark:border-slate-700">
            <h2 class="text-xl font-bold text-gray-800 dark:text-white mb-6">Tu Avatar</h2>

            {{-- Previsualización Grande --}}
            <div class="relative group mb-8">
                <div class="avatar-preview-container avatar-preview-border bg-white dark:bg-slate-700">
                    <img id="avatar-preview" src="{{ Auth::user()->avatar_url }}" class="w-full h-full object-cover">
                </div>
                <div class="camera-icon-badge">
                    <i class="fas fa-camera"></i>
                </div>
            </div>

            <p class="text-sm text-gray-500 dark:text-slate-400 text-center mb-4">
                Elige un personaje o sube tu foto.
            </p>
        </div>

        {{-- COLUMNA DERECHA: FORMULARIO --}}
        <div class="w-full md:w-2/3 p-8 bg-white dark:bg-slate-900">
            
            @if(session('success'))
                <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded text-sm animate-bounce-in">
                    <strong>¡Éxito!</strong> {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- 1. SELECTOR DE AVATAR (Local Assets) --}}
                <div class="mb-8">
                    <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-3">Elige un Personaje</label>
                    
                    {{-- Grid de Avatares Locales (avatar_1.png a avatar_8.png) --}}
                    <div class="flex flex-wrap gap-4 mb-4">
                        @foreach(range(1, 8) as $num)
                            @php 
                                $filename = "avatar_{$num}.png";
                                $url = asset('assets/avatars/' . $filename);
                                $isSelected = Auth::user()->avatar === $filename;
                            @endphp

                            <label class="avatar-radio-option group">
                                {{-- El value es solo el nombre del archivo para la BD --}}
                                <input type="radio" 
                                       name="avatar_preset" 
                                       value="{{ $filename }}" 
                                       class="avatar-radio-input peer sr-only" 
                                       {{ $isSelected ? 'checked' : '' }}
                                       {{-- Pasamos la URL completa para el JS de preview --}}
                                       onchange="previewPreset('{{ $url }}')">
                                
                                <div class="avatar-radio-img group-hover:scale-110 transition-transform bg-white">
                                    <img src="{{ $url }}" class="w-full h-full object-cover">
                                </div>

                                {{-- Icono de check (solo visible si está seleccionado) --}}
                                <div class="absolute -top-1 -right-1 bg-orange-500 text-white text-[10px] w-4 h-4 rounded-full items-center justify-center hidden peer-checked:flex shadow-sm z-10">
                                    <i class="fas fa-check"></i>
                                </div>
                            </label>
                        @endforeach
                    </div>

                    {{-- Input de Archivo --}}
                    <div class="relative mt-4">
                        <label class="upload-box group">
                            <i class="fas fa-cloud-upload-alt text-gray-400 group-hover:text-orange-500 text-2xl mb-1"></i>
                            <span class="text-xs text-gray-500 dark:text-slate-400 font-bold">O sube tu propia imagen</span>
                            <input type="file" name="avatar_upload" id="avatar-upload-input" class="hidden" onchange="previewUpload(event)" accept="image/*">
                        </label>
                    </div>
                </div>

                {{-- 2. DATOS PERSONALES --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">Nombre</label>
                        <input type="text" name="name" value="{{ old('name', Auth::user()->name) }}" required
                               class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500 focus:outline-none transition">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">Teléfono</label>
                        <input type="text" name="telefono" value="{{ old('telefono', Auth::user()->telefono) }}" placeholder="Opcional"
                               class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500 focus:outline-none transition">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">Correo Electrónico</label>
                        <input type="email" name="email" value="{{ old('email', Auth::user()->email) }}" required
                               class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500 focus:outline-none transition">
                    </div>
                </div>

                {{-- 3. CAMBIAR CONTRASEÑA --}}
                <div class="border-t border-gray-200 dark:border-slate-700 pt-6 mb-6">
                    <h3 class="text-sm font-bold text-orange-500 uppercase tracking-wide mb-4">Seguridad (Opcional)</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">Nueva Contraseña</label>
                            <input type="password" name="password" placeholder="••••••••" 
                                   class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500 focus:outline-none transition">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">Confirmar Contraseña</label>
                            <input type="password" name="password_confirmation" placeholder="••••••••" 
                                   class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500 focus:outline-none transition">
                        </div>
                    </div>
                </div>

                {{-- BOTÓN GUARDAR --}}
                <div class="flex justify-end">
                    <button type="submit" 
                            class="bg-orange-600 hover:bg-orange-500 text-white font-bold py-3 px-8 rounded-xl shadow-lg shadow-orange-500/30 transform transition hover:-translate-y-1 hover:scale-105">
                        <i class="fas fa-save mr-2"></i> Guardar Cambios
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection