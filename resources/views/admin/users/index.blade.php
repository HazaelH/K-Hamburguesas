@extends('layouts.admin')

@section('titulo', 'Gestión de Usuarios')

@section('contenido')
<div class="space-y-6">
    
    {{-- ENCABEZADO Y BOTÓN CREAR --}}
    <div class="flex flex-col md:flex-row justify-between items-end md:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-white">Directorio de Usuarios</h1>
            <p class="text-slate-400">Administra el acceso de empleados y clientes</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="bg-blue-600 hover:bg-blue-500 text-white px-4 py-2 rounded-lg font-bold shadow-lg transition flex items-center gap-2">
            <i class="fas fa-user-plus"></i> Nuevo Usuario
        </a>
    </div>

    {{-- BARRA DE FILTROS Y BÚSQUEDA --}}
    <div class="bg-slate-800 p-4 rounded-xl border border-slate-700 shadow-lg">
        <form action="{{ route('admin.users.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 justify-between items-center">
            
            {{-- Pestañas Rápidas (Filtros Predefinidos) --}}
            <div class="flex bg-slate-900 p-1 rounded-lg border border-slate-700 w-full md:w-auto">
                <a href="{{ route('admin.users.index') }}" 
                   class="flex-1 md:flex-none px-4 py-2 rounded-md text-sm font-bold text-center transition {{ !request('rol') ? 'bg-slate-700 text-white shadow' : 'text-slate-400 hover:text-white' }}">
                    Todos
                </a>
                <a href="{{ route('admin.users.index', ['rol' => 'empleado']) }}" 
                   class="flex-1 md:flex-none px-4 py-2 rounded-md text-sm font-bold text-center transition {{ request('rol') == 'empleado' ? 'bg-blue-600 text-white shadow' : 'text-slate-400 hover:text-white' }}">
                    Equipo
                </a>
                <a href="{{ route('admin.users.index', ['rol' => 'cliente']) }}" 
                   class="flex-1 md:flex-none px-4 py-2 rounded-md text-sm font-bold text-center transition {{ request('rol') == 'cliente' ? 'bg-green-600 text-white shadow' : 'text-slate-400 hover:text-white' }}">
                    Clientes
                </a>
            </div>

            {{-- Buscador --}}
            <div class="flex w-full md:w-auto gap-2">
                <div class="relative w-full md:w-64">
                    <span class="absolute left-3 top-2.5 text-slate-500"><i class="fas fa-search"></i></span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar nombre, email..." 
                           class="w-full bg-slate-900 text-white border border-slate-600 rounded-lg py-2 pl-10 pr-4 focus:outline-none focus:border-blue-500 text-sm">
                </div>
                
                {{-- Si hay filtro de rol activo, lo mantenemos oculto en el form para no perderlo al buscar --}}
                @if(request('rol'))
                    <input type="hidden" name="rol" value="{{ request('rol') }}">
                @endif

                <button type="submit" class="bg-slate-700 hover:bg-slate-600 text-white px-4 py-2 rounded-lg border border-slate-600 transition">
                    Buscar
                </button>
                
                {{-- Botón Limpiar (Solo si hay filtros activos) --}}
                @if(request('search') || request('rol'))
                    <a href="{{ route('admin.users.index') }}" class="bg-red-500/10 hover:bg-red-500/20 text-red-400 px-3 py-2 rounded-lg border border-red-500/30 transition" title="Limpiar Filtros">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>

        </form>
    </div>

    {{-- TABLA DE RESULTADOS --}}
    <div class="bg-slate-800 rounded-2xl border border-slate-700 shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-slate-400">
                <thead class="bg-slate-900 text-slate-200 uppercase text-xs font-bold tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Usuario</th>
                        <th class="px-6 py-4">Rol</th>
                        <th class="px-6 py-4">Email</th>
                        <th class="px-6 py-4 text-center">Registrado</th>
                        <th class="px-6 py-4 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700">
                    @forelse($users as $user)
                    <tr class="hover:bg-slate-700/50 transition">
                        <td class="px-6 py-4 flex items-center gap-3">
                            {{-- CORRECTO: Usa $user->avatar_url --}}
                            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" 
                                class="h-10 w-10 rounded-full object-cover border border-slate-600 bg-slate-700">
                            
                            <div>
                                <p class="text-white font-bold">{{ $user->name }}</p>
                                <p class="text-xs text-slate-500">ID: {{ $user->id }}</p>
                            </div>
                        </td>
                        
                        <td class="px-6 py-4">
                            @php
                                $roleColor = match($user->rol) {
                                    'admin' => 'bg-red-500/20 text-red-400 border-red-500/30',
                                    'empleado' => 'bg-blue-500/20 text-blue-400 border-blue-500/30',
                                    'cliente' => 'bg-green-500/20 text-green-400 border-green-500/30',
                                    default => 'bg-gray-500/20 text-gray-400'
                                };
                            @endphp
                            <span class="border px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide {{ $roleColor }}">
                                {{ $user->rol }}
                            </span>
                        </td>

                        <td class="px-6 py-4 text-sm">{{ $user->email }}</td>
                        <td class="px-6 py-4 text-center text-sm">{{ $user->created_at->format('d/m/Y') }}</td>

                        <td class="px-6 py-4 text-right space-x-2">
                            <a href="{{ route('admin.users.edit', $user->id) }}" class="text-blue-400 hover:text-white transition" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            @if(Auth::id() !== $user->id)
                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('¿Eliminar usuario?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-400 hover:text-white transition" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                            <i class="fas fa-search text-4xl mb-3 opacity-50"></i>
                            <p class="text-lg font-bold">No se encontraron usuarios</p>
                            <p class="text-sm">Intenta con otros filtros de búsqueda.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Paginación --}}
        <div class="p-4 border-t border-slate-700 bg-slate-900">
            {{ $users->links() }}
        </div>
    </div>

</div>
@endsection