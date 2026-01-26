@extends('layouts.admin')

@section('titulo', 'Editar Usuario')

@vite(['resources/js/admin/users.js'])

@section('contenido')
<div class="max-w-5xl mx-auto py-8">
    
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-white">
            <i class="fas fa-user-edit text-orange-500 mr-2"></i> Editar Usuario
        </h1>
        <a href="{{ route('admin.users.index') }}" class="text-slate-400 hover:text-white transition flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

    <div class="bg-slate-800 rounded-2xl shadow-xl border border-slate-700 p-8">
        <form action="{{ route('admin.users.update', $user->id) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf
            @method('PUT')

            {{-- GRID PRINCIPAL DE 2 COLUMNAS --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
                
                {{-- COLUMNA 1: DATOS DEL USUARIO --}}
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-bold text-slate-300 mb-2">Nombre Completo</label>
                        <div class="relative">
                            <span class="absolute left-3 top-3 text-slate-500"><i class="fas fa-user"></i></span>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                                   class="w-full bg-slate-900 text-white border border-slate-600 rounded-lg p-3 pl-10 focus:outline-none focus:border-orange-500 transition">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-300 mb-2">Correo Electrónico</label>
                        <div class="relative">
                            <span class="absolute left-3 top-3 text-slate-500"><i class="fas fa-envelope"></i></span>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                                   class="w-full bg-slate-900 text-white border border-slate-600 rounded-lg p-3 pl-10 focus:outline-none focus:border-orange-500 transition">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-300 mb-2">Rol</label>
                            <div class="relative">
                                <span class="absolute left-3 top-3 text-slate-500"><i class="fas fa-id-badge"></i></span>
                                <select name="rol" class="w-full bg-slate-900 text-white border border-slate-600 rounded-lg p-3 pl-10 focus:outline-none focus:border-orange-500 transition appearance-none">
                                    <option value="cliente" {{ $user->rol == 'cliente' ? 'selected' : '' }}>Cliente</option>
                                    <option value="empleado" {{ $user->rol == 'empleado' ? 'selected' : '' }}>Empleado</option>
                                    <option value="admin" {{ $user->rol == 'admin' ? 'selected' : '' }}>Administrador</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-300 mb-2">Contraseña</label>
                            <div class="relative">
                                <span class="absolute left-3 top-3 text-slate-500"><i class="fas fa-lock"></i></span>
                                <input type="password" name="password" placeholder="Vacío para mantener"
                                       class="w-full bg-slate-900 text-white border border-slate-600 rounded-lg p-3 pl-10 focus:outline-none focus:border-orange-500 transition">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- COLUMNA 2: GESTIÓN DE AVATAR (Todo dentro de este div) --}}
                <div class="border-t lg:border-t-0 lg:border-l border-slate-700 pt-8 lg:pt-0 lg:pl-10 space-y-8">
                    
                    {{-- Previsualización Actual --}}
                    <div class="flex items-center gap-4 p-4 bg-slate-900/50 rounded-xl border border-slate-700">
                        <div class="w-16 h-16 rounded-full bg-slate-700 border border-slate-500 overflow-hidden relative shrink-0">
                            {{-- ID preview-avatar es clave para el JS --}}
                            <img id="preview-avatar" src="{{ $user->avatar_url }}" class="w-full h-full object-cover">
                            <div id="icon-avatar" class="w-full h-full flex items-center justify-center text-slate-500 hidden">
                                <i class="fas fa-camera text-xl"></i>
                            </div>
                        </div>
                        <div>
                            <p class="text-white font-bold text-sm">Previsualización</p>
                            <p class="text-slate-500 text-xs">Así se verá tu perfil</p>
                        </div>
                    </div>

                    {{-- Opción A: Avatares Predeterminados --}}
                    <div>
                        <p class="text-xs text-slate-500 mb-3 uppercase font-bold tracking-wider">Elige un personaje</p>
                        <div class="grid grid-cols-4 gap-3">
                            @foreach(range(1, 8) as $num)
                                @php $av = "avatar_{$num}.png"; @endphp
                                <label class="cursor-pointer group relative">
                                    {{-- Comparamos si es el avatar actual para marcarlo --}}
                                    <input type="radio" name="avatar_option" value="{{ $av }}" 
                                           class="peer sr-only" 
                                           {{ $user->avatar == $av ? 'checked' : '' }}>
                                           
                                    <img src="{{ asset('assets/avatars/' . $av) }}" 
                                         class="w-12 h-12 rounded-full border-2 border-slate-600 grayscale peer-checked:grayscale-0 peer-checked:border-orange-500 peer-checked:scale-110 transition-all hover:border-slate-400">
                                    
                                    <div class="absolute -top-1 -right-1 bg-orange-500 text-white text-[10px] w-4 h-4 rounded-full items-center justify-center hidden peer-checked:flex shadow-sm">
                                        <i class="fas fa-check"></i>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Separador --}}
                    <div class="flex items-center gap-4">
                        <span class="h-px bg-slate-700 flex-1"></span>
                        <span class="text-[10px] text-slate-500 font-bold uppercase">O sube una foto</span>
                        <span class="h-px bg-slate-700 flex-1"></span>
                    </div>

                    {{-- Opción B: Subir Foto (Dentro de la misma columna) --}}
                    <div>
                        <div class="relative">
                            <input type="file" name="foto_custom" id="foto_custom" accept="image/*"
                                   class="block w-full text-sm text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-slate-700 file:text-white hover:file:bg-slate-600 cursor-pointer border border-slate-700 rounded-lg bg-slate-900 focus:outline-none">
                        </div>
                        <p class="text-[10px] text-slate-500 mt-2 text-right">Formatos: JPG, PNG, WEBP (Max 2MB)</p>
                    </div>

                </div> {{-- Fin Columna Derecha --}}
            
            </div> {{-- Fin Grid Principal --}}

            {{-- Botón Guardar --}}
            <div class="border-t border-slate-700 pt-6">
                <button type="submit" 
                        class="w-full md:w-auto md:float-right bg-orange-600 hover:bg-orange-500 text-white font-bold py-3 px-8 rounded-xl shadow-lg transform transition hover:scale-[1.01]">
                    <i class="fas fa-sync-alt mr-2"></i> Actualizar Datos
                </button>
            </div>
            
            {{-- Limpiar floats --}}
            <div class="clearfix"></div> 

        </form>
    </div>
</div>
@endsection