@extends('layouts.admin')

@section('titulo', 'Nuevo Usuario')

{{-- Cargamos el JS específico --}}
@vite(['resources/js/admin/users.js'])

@section('contenido')
<div class="max-w-4xl mx-auto py-8">
    
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-white">
            <i class="fas fa-user-plus text-blue-500 mr-2"></i> Nuevo Usuario
        </h1>
        <a href="{{ route('admin.users.index') }}" class="text-slate-400 hover:text-white transition flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

    <div class="bg-slate-800 rounded-2xl shadow-xl border border-slate-700 p-8">
        <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                
                {{-- COLUMNA IZQUIERDA: DATOS --}}
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-bold text-slate-300 mb-2">Nombre Completo</label>
                        <div class="relative">
                            <span class="absolute left-3 top-3 text-slate-500"><i class="fas fa-user"></i></span>
                            <input type="text" name="name" value="{{ old('name') }}" required placeholder="Ej: Juan Pérez"
                                   class="w-full bg-slate-900 text-white border border-slate-600 rounded-lg p-3 pl-10 focus:outline-none focus:border-blue-500 transition">
                        </div>
                        @error('name') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-300 mb-2">Correo Electrónico</label>
                        <div class="relative">
                            <span class="absolute left-3 top-3 text-slate-500"><i class="fas fa-envelope"></i></span>
                            <input type="email" name="email" value="{{ old('email') }}" required placeholder="correo@ejemplo.com"
                                   class="w-full bg-slate-900 text-white border border-slate-600 rounded-lg p-3 pl-10 focus:outline-none focus:border-blue-500 transition">
                        </div>
                        @error('email') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-300 mb-2">Rol</label>
                            <div class="relative">
                                <span class="absolute left-3 top-3 text-slate-500"><i class="fas fa-id-badge"></i></span>
                                <select name="rol" class="w-full bg-slate-900 text-white border border-slate-600 rounded-lg p-3 pl-10 focus:outline-none focus:border-blue-500 transition appearance-none">
                                    <option value="cliente">Cliente</option>
                                    <option value="empleado">Empleado</option>
                                    <option value="admin">Administrador</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-300 mb-2">Contraseña</label>
                            <div class="relative">
                                <span class="absolute left-3 top-3 text-slate-500"><i class="fas fa-lock"></i></span>
                                <input type="password" name="password" required placeholder="Min 6 caracteres"
                                       class="w-full bg-slate-900 text-white border border-slate-600 rounded-lg p-3 pl-10 focus:outline-none focus:border-blue-500 transition">
                            </div>
                            @error('password') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                {{-- COLUMNA DERECHA: AVATAR --}}
                <div class="border-l border-slate-700 pl-8 space-y-6">
                    <label class="block text-sm font-bold text-slate-300">Imagen de Perfil</label>

                    {{-- Opción A: Predeterminados --}}
                    <div>
                        <p class="text-xs text-slate-500 mb-3 uppercase font-bold tracking-wider">Elige un personaje</p>
                        
                        {{-- Grid de 4 columnas (se harán 2 filas automáticas con 8 items) --}}
                        <div class="grid grid-cols-4 gap-3">
                            @foreach(range(1, 8) as $num)
                                @php $av = "avatar_{$num}.png"; @endphp
                                <label class="cursor-pointer group relative">
                                    <input type="radio" name="avatar_option" value="{{ $av }}" class="peer sr-only">
                                    <img src="{{ asset('assets/avatars/' . $av) }}" 
                                        class="w-12 h-12 rounded-full border-2 border-slate-600 grayscale peer-checked:grayscale-0 peer-checked:border-orange-500 peer-checked:scale-110 transition-all hover:border-slate-400"
                                        title="Avatar {{ $num }}">
                                    <div class="absolute -top-1 -right-1 bg-orange-500 text-white text-[10px] w-4 h-4 rounded-full items-center justify-center hidden peer-checked:flex shadow-sm">
                                        <i class="fas fa-check"></i>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex items-center gap-4 py-2">
                        <span class="h-px bg-slate-700 flex-1"></span>
                        <span class="text-xs text-slate-500 font-bold">O BIEN</span>
                        <span class="h-px bg-slate-700 flex-1"></span>
                    </div>

                    {{-- Opción B: Subir Foto --}}
                    <div>
                        <p class="text-xs text-slate-500 mb-3 uppercase font-bold tracking-wider">Sube tu foto</p>
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 rounded-full bg-slate-700 border border-slate-600 overflow-hidden relative shrink-0">
                                <img id="preview-avatar" src="#" class="w-full h-full object-cover hidden">
                                <div id="icon-avatar" class="w-full h-full flex items-center justify-center text-slate-500">
                                    <i class="fas fa-camera text-xl"></i>
                                </div>
                            </div>
                            <div class="flex-1">
                                <input type="file" name="foto_custom" id="foto_custom" accept="image/*"
                                       class="block w-full text-sm text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-slate-700 file:text-white hover:file:bg-slate-600 cursor-pointer">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="border-t border-slate-700 pt-6">
                <button type="submit" 
                        class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-3 rounded-xl shadow-lg transform transition hover:scale-[1.01]">
                    <i class="fas fa-save mr-2"></i> Guardar Usuario
                </button>
            </div>
        </form>
    </div>
</div>
@endsection