@extends('layouts.admin')

@section('titulo', __('admin/users/users.title_index'))

@section('contenido')
<div class="py-6 space-y-6 relative">
    
    <div class="flex flex-col md:flex-row justify-between items-end md:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-white"><i class="fas fa-users text-blue-500 mr-2"></i> {{ __('admin/users/users.title_index') }}</h1>
            <p class="text-slate-400">{{ __('admin/users/users.subtitle_index') }}</p>
        </div>
        <a href="{{ route('admin.users.create') }}" 
           class="bg-blue-600 hover:bg-blue-500 text-white font-bold py-2.5 px-6 rounded-xl shadow-lg shadow-blue-900/20 transition transform hover:-translate-y-0.5 flex items-center gap-2">
            <i class="fas fa-plus"></i> {{ __('admin/users/users.btn_new_user') }}
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-slate-800 p-5 rounded-2xl border border-slate-300 shadow-lg flex items-center gap-4">
            <div class="h-12 w-12 rounded-full bg-blue-500/20 flex items-center justify-center text-blue-400 text-xl border border-blue-500/30">
                <i class="fas fa-users"></i>
            </div>
            <div>
                <p class="text-slate-400 text-xs font-bold uppercase tracking-wider">{{ __('admin/users/users.stats_total') }}</p>
                <p class="text-2xl font-black text-white">{{ $stats['total'] }}</p>
            </div>
        </div>
        <div class="bg-slate-800 p-5 rounded-2xl border border-slate-300 shadow-lg flex items-center gap-4">
            <div class="h-12 w-12 rounded-full bg-purple-500/20 flex items-center justify-center text-purple-400 text-xl border border-purple-500/30">
                <i class="fas fa-user-shield"></i>
            </div>
            <div>
                <p class="text-slate-400 text-xs font-bold uppercase tracking-wider">{{ __('admin/users/users.stats_admins') }}</p>
                <p class="text-2xl font-black text-white">{{ $stats['admins'] }}</p>
            </div>
        </div>
        <div class="bg-slate-800 p-5 rounded-2xl border border-slate-300 shadow-lg flex items-center gap-4">
            <div class="h-12 w-12 rounded-full bg-emerald-500/20 flex items-center justify-center text-emerald-400 text-xl border border-emerald-500/30">
                <i class="fas fa-user-tag"></i>
            </div>
            <div>
                <p class="text-slate-400 text-xs font-bold uppercase tracking-wider">{{ __('admin/users/users.stats_clients') }}</p>
                <p class="text-2xl font-black text-white">{{ $stats['clientes'] }}</p>
            </div>
        </div>
    </div>

    <div class="bg-slate-800 p-4 rounded-2xl border border-slate-300 shadow-lg">
        <form id="filtro-usuarios" action="{{ route('admin.users.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
            
            <div class="flex-1 relative">
                <span class="absolute left-4 top-3 text-slate-500"><i class="fas fa-search"></i></span>
                {{-- Se agregó aria-label al buscador --}}
                <input type="text" name="search" aria-label="{{ __('admin/users/users.search_placeholder') }}" value="{{ request('search') }}" placeholder="{{ __('admin/users/users.search_placeholder') }}" 
                       class="w-full bg-slate-900 border border-slate-600 focus:border-blue-500 text-white rounded-xl py-2.5 pl-11 pr-4 focus:outline-none transition-colors text-sm placeholder-slate-500">
            </div>

            <div class="w-full md:w-48 relative">
                {{-- Se agregó aria-label al select de rol --}}
                <select name="rol" aria-label="Filtrar por rol de usuario" onchange="this.form.submit()" class="w-full bg-slate-900 border border-slate-600 focus:border-blue-500 text-white rounded-xl py-2.5 px-4 appearance-none focus:outline-none transition-colors text-sm cursor-pointer">
                    <option value="">{{ __('admin/users/users.all_roles') }}</option>
                    <option value="admin" {{ request('rol') == 'admin' ? 'selected' : '' }}>{{ __('admin/users/users.role_admins') }}</option>
                    <option value="cliente" {{ request('rol') == 'cliente' ? 'selected' : '' }}>{{ __('admin/users/users.role_clients') }}</option>
                    <option value="equipo" {{ request('rol') == 'equipo' ? 'selected' : '' }}>{{ __('admin/users/users.role_staff') }}</option>
                </select>
                <i class="fas fa-chevron-down absolute right-4 top-3.5 text-slate-500 text-xs pointer-events-none"></i>
            </div>

            <div class="w-full md:w-48 relative">
                {{-- Se agregó aria-label al select de estado --}}
                <select name="ver_bajas" aria-label="Filtrar por estado activo o eliminado" onchange="this.form.submit()" class="w-full bg-slate-900 border border-slate-600 focus:border-blue-500 text-white rounded-xl py-2.5 px-4 appearance-none focus:outline-none transition-colors text-sm cursor-pointer font-bold {{ request('ver_bajas') ? 'text-red-400 border-red-500/50' : '' }}">
                    <option value="">{{ __('admin/users/users.active_users') }}</option>
                    <option value="1" {{ request('ver_bajas') == '1' ? 'selected' : '' }}>{{ __('admin/users/users.trashed_users') }}</option>
                </select>
                <i class="fas fa-chevron-down absolute right-4 top-3.5 text-slate-500 text-xs pointer-events-none"></i>
            </div>

            <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white px-6 rounded-xl transition-colors font-bold text-sm shadow-md">
                {{ __('admin/users/users.btn_filter') }}
            </button>

            @if(request('search') || request('rol') || request('ver_bajas'))
                {{-- Se agregó aria-label al botón de limpiar --}}
                <a href="{{ route('admin.users.index') }}" aria-label="Limpiar filtros de búsqueda" class="flex items-center justify-center bg-red-500/10 text-red-400 hover:bg-red-500 hover:text-white px-4 py-2 rounded-xl transition-colors border border-red-500/30" title="{{ __('admin/users/users.clear_filters') }}">
                    <i class="fas fa-times"></i>
                </a>
            @endif
        </form>
    </div>

    <div class="bg-slate-800 rounded-2xl border border-slate-300 overflow-hidden shadow-2xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-400">
                <thead class="bg-slate-900 text-slate-300 uppercase text-[10px] font-black tracking-widest">
                    <tr>
                        <th class="px-5 py-4 w-20">{{ __('admin/users/users.col_photo') }}</th>
                        <th class="px-5 py-4">{{ __('admin/users/users.col_info') }}</th>
                        <th class="px-5 py-4">{{ __('admin/users/users.col_role') }}</th>
                        <th class="px-5 py-4 text-center">{{ __('admin/users/users.col_status') }}</th>
                        <th class="px-5 py-4 text-right">{{ __('admin/users/users.col_actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/50">
                    @forelse($users as $user)
                    <tr class="hover:bg-slate-700/30 transition-colors {{ $user->trashed() ? 'opacity-50 grayscale' : '' }}">
                        <td class="px-5 py-3">
                            {{-- Se agregó el atributo ALT a la imagen --}}
                            <img src="{{ $user->avatar_url }}" alt="Avatar de {{ $user->name }}" class="h-10 w-10 object-cover rounded-full border-2 border-slate-600 shadow-sm">
                        </td>
                        
                        <td class="px-5 py-3">
                            <p class="text-white font-bold text-sm">{{ $user->name }}</p>
                            <p class="text-[11px] text-slate-400 font-mono truncate max-w-xs">{{ $user->email }}</p>
                        </td>

                        <td class="px-5 py-3">
                            @if($user->rol === 'admin')
                                <span class="bg-purple-500/10 text-purple-400 border border-purple-500/20 text-[10px] px-2.5 py-1 rounded-md uppercase font-black tracking-wider">
                                    <i class="fas fa-shield-alt mr-1"></i> {{ $user->rol_traducido }}
                                </span>
                            @elseif($user->rol === 'cliente')
                                <span class="bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-[10px] px-2.5 py-1 rounded-md uppercase font-black tracking-wider">
                                    <i class="fas fa-shopping-bag mr-1"></i> {{ $user->rol_traducido }}
                                </span>
                            @else
                                <span class="bg-blue-500/10 text-blue-400 border border-blue-500/20 text-[10px] px-2.5 py-1 rounded-md uppercase font-black tracking-wider">
                                    <i class="fas fa-user-tie mr-1"></i> {{ $user->rol_traducido }}
                                </span>
                            @endif
                        </td>

                        <td class="px-5 py-3 text-center">
                            @if(!$user->trashed())
                                <span class="bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-[10px] font-bold px-2 py-1 rounded uppercase tracking-wider"><i class="fas fa-circle text-[8px] mr-1"></i> {{ __('admin/users/users.status_active') }}</span>
                            @else
                                <span class="bg-red-500/10 text-red-400 border border-red-500/20 text-[10px] font-bold px-2 py-1 rounded uppercase tracking-wider"><i class="fas fa-times text-[8px] mr-1"></i> {{ __('admin/users/users.status_trashed') }}</span>
                            @endif
                        </td>

                        <td class="px-5 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                @if(!$user->trashed())
                                    {{-- Se agregaron aria-labels con el nombre del usuario para evitar enlaces idénticos --}}
                                    <a href="{{ route('admin.users.edit', $user->id) }}" aria-label="Editar usuario {{ $user->name }}" class="bg-blue-600/20 text-blue-400 hover:bg-blue-600 hover:text-white h-8 w-8 rounded flex items-center justify-center transition-colors border border-blue-500/30">
                                        <i class="fas fa-edit text-xs"></i>
                                    </a>

                                    @if(Auth::id() != $user->id)
                                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" id="form-delete-{{ $user->id }}">
                                            @csrf @method('DELETE')
                                            <button type="button" aria-label="Dar de baja a {{ $user->name }}" onclick="window.confirmarModal('form-delete-{{ $user->id }}', 'baja')" class="bg-orange-700/20 text-orange-400 hover:bg-orange-700 hover:text-white h-8 w-8 rounded flex items-center justify-center transition-colors border border-orange-500/30">
                                                <i class="fas fa-user-slash text-xs pointer-events-none"></i>
                                            </button>
                                        </form>
                                    @endif
                                @else
                                    <form action="{{ route('admin.users.restore', $user->id) }}" method="POST" class="m-0 p-0">
                                        @csrf
                                        <button type="submit" aria-label="Restaurar a {{ $user->name }}" class="bg-blue-600/20 text-blue-400 hover:bg-blue-600 hover:text-white h-8 w-8 rounded flex items-center justify-center transition-colors border border-blue-500/30">
                                            <i class="fas fa-undo-alt text-xs pointer-events-none"></i>
                                        </button>
                                    </form>

                                    <form action="{{ route('admin.users.forceDestroy', $user->id) }}" method="POST" id="form-force-delete-{{ $user->id }}" class="m-0 p-0">
                                        @csrf @method('DELETE')
                                        <button type="button" aria-label="Eliminar permanentemente a {{ $user->name }}" onclick="window.confirmarModal('form-force-delete-{{ $user->id }}', 'eliminar')" class="bg-red-600/20 text-red-400 hover:bg-red-600 hover:text-white h-8 w-8 rounded flex items-center justify-center transition-colors border border-red-500/30">
                                            <i class="fas fa-skull text-xs pointer-events-none"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-16 text-center text-slate-500">
                            <div class="bg-slate-900 h-20 w-20 rounded-full flex items-center justify-center mx-auto mb-4 border border-slate-300">
                                <i class="fas fa-users-slash text-3xl opacity-50"></i>
                            </div>
                            <p class="text-white font-bold text-lg">{{ __('admin/users/users.empty_title') }}</p>
                            <p class="text-sm mt-1">{{ __('admin/users/users.empty_desc') }}</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($users->hasPages())
        <div class="px-5 py-3 border-t border-slate-300 bg-slate-900">
            {{ $users->withQueryString()->links() }} 
        </div>
        @endif
    </div>
</div>

{{-- MODAL DINÁMICO --}}
<div id="dynamic-modal" class="fixed inset-0 z-[100] hidden flex items-center justify-center bg-black/80 backdrop-blur-sm transition-opacity opacity-0">
    <div id="dynamic-modal-panel" class="bg-slate-900 border border-slate-300 p-6 rounded-3xl shadow-2xl w-full max-w-sm transform scale-95 transition-all text-center">
        
        <div id="modal-icon-container" class="mx-auto flex h-16 w-16 items-center justify-center rounded-full mb-4">
            <i id="modal-icon" class="fas text-2xl animate-pulse"></i>
        </div>
        
        {{-- Cambiado de h3 a h2 para la jerarquía correcta --}}
        <h2 id="modal-title" class="text-xl font-black text-white mb-2">...</h2>
        <p id="modal-text" class="text-xs text-slate-400 mb-6 px-2">...</p>
        
        <div class="flex gap-3">
            <button onclick="window.cerrarModal()" class="flex-1 bg-slate-800 hover:bg-slate-700 text-white font-bold py-3 rounded-xl transition-all border border-slate-600 text-sm">
                {{ __('admin/users/users.cancel') }}
            </button>
            <button id="modal-confirm-btn" class="flex-1 text-white font-bold py-3 rounded-xl shadow-lg transition-all text-sm">
                {{ __('admin/users/users.confirm') }}
            </button>
        </div>
    </div>
</div>

{{-- SISTEMA TOAST --}}
@if(session('success') || session('error'))
    <div id="admin-toast" class="fixed bottom-6 right-6 z-[200] transform transition-all duration-500 translate-y-20 opacity-0 flex items-center gap-4 px-6 py-4 rounded-2xl shadow-2xl border {{ session('success') ? 'bg-emerald-900/95 border-emerald-500 text-emerald-100 shadow-[0_10px_40px_rgba(16,185,129,0.3)]' : 'bg-red-900/95 border-red-500 text-red-100 shadow-[0_10px_40px_rgba(239,68,68,0.3)]' }}">
        
        <div class="h-12 w-12 rounded-full flex items-center justify-center shrink-0 {{ session('success') ? 'bg-emerald-500/20 text-emerald-400' : 'bg-red-500/20 text-red-400' }}">
            <i class="fas {{ session('success') ? 'fa-check' : 'fa-exclamation-triangle' }} text-xl"></i>
        </div>
        
        <div class="max-w-xs">
            <p class="font-black text-sm uppercase tracking-wider {{ session('success') ? 'text-emerald-400' : 'text-red-400' }}">
                {{ session('success') ? __('admin/users/users.toast_success_banner') : __('admin/users/users.toast_error_banner') }}
            </p>
            <p class="font-medium text-sm mt-0.5 leading-tight">{{ session('success') ?? session('error') }}</p>
        </div>
        
        {{-- Agregado botón para cerrar notificacion por accesibilidad --}}
        <button aria-label="Cerrar notificación" onclick="document.getElementById('admin-toast').remove()" class="ml-2 text-slate-400 hover:text-white transition">
            <i class="fas fa-times pointer-events-none"></i>
        </button>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toast = document.getElementById('admin-toast');
            if(toast) {
                setTimeout(() => toast.classList.remove('translate-y-20', 'opacity-0'), 100);
                setTimeout(() => {
                    if(document.getElementById('admin-toast')) {
                        toast.classList.add('translate-y-20', 'opacity-0');
                        setTimeout(() => toast.remove(), 500);
                    }
                }, 6000);
            }
        });
    </script>
@endif

<script>
    // Pasar traducciones a JavaScript
    window.USERS_LANG = {
        soft_title: `{{ __('admin/users/users.modal_soft_title') }}`,
        soft_desc: `{{ __('admin/users/users.modal_soft_desc') }}`,
        btn_soft: `{{ __('admin/users/users.btn_soft_delete') }}`,
        
        hard_title: `{{ __('admin/users/users.modal_hard_title') }}`,
        hard_desc: `{{ __('admin/users/users.modal_hard_desc') }}`,
        btn_hard: `{{ __('admin/users/users.btn_hard_delete') }}`
    };
</script>

@endsection

@push('scripts')
    @vite(['resources/js/admin/users-index.js'])
@endpush