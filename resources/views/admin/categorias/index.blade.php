@extends('layouts.admin')

@section('titulo', __('admin/categorias/index.title'))

@section('contenido')
<div class="py-6 space-y-6 relative max-w-5xl mx-auto">
    
    <div class="flex flex-col md:flex-row justify-between items-end md:items-center gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-bold text-white"><i class="fas fa-tags text-orange-500 mr-2"></i> {{ __('admin/categorias/index.header_title') }}</h1>
            <p class="text-slate-400">{{ __('admin/categorias/index.header_subtitle') }}</p>
        </div>
        <button onclick="abrirModalCategoria()" class="bg-orange-700 hover:bg-orange-500 text-white font-bold py-2.5 px-6 rounded-xl shadow-lg shadow-orange-900/20 transition transform hover:-translate-y-0.5 flex items-center gap-2">
            <i class="fas fa-plus"></i> {{ __('admin/categorias/index.btn_new') }}
        </button>
    </div>

    {{-- SISTEMA DE TOASTS (Éxito y Errores) --}}
    @if(session('success') || session('error') || $errors->any())
        <div id="toast-notification" class="fixed bottom-5 right-5 z-[200] flex flex-col gap-3 shadow-2xl transform transition-all duration-500 translate-y-0 opacity-100">
            @if(session('success'))
                <div class="bg-emerald-950/90 border border-emerald-500 text-emerald-400 px-6 py-4 rounded-2xl flex items-center gap-4 backdrop-blur-md shadow-[0_0_20px_rgba(16,185,129,0.2)]">
                    <i class="fas fa-check-circle text-2xl animate-bounce"></i>
                    <div>
                        <p class="font-bold text-lg leading-tight">{{ __('admin/categorias/messages.success_title') }}</p>
                        <p class="text-xs text-emerald-300 mt-1 max-w-xs">{{ session('success') }}</p>
                    </div>
                    <button onclick="document.getElementById('toast-notification').remove()" class="ml-2 text-emerald-600 hover:text-emerald-400 transition"><i class="fas fa-times"></i></button>
                </div>
            @endif

            @if(session('error') || $errors->any())
                <div class="bg-red-950/90 border border-red-500 text-red-400 px-6 py-4 rounded-2xl flex items-center gap-4 backdrop-blur-md shadow-[0_0_20px_rgba(239,68,68,0.2)]">
                    <i class="fas fa-shield-alt text-2xl animate-pulse"></i>
                    <div>
                        <p class="font-bold text-lg leading-tight">{{ __('admin/categorias/messages.error_title') }}</p>
                        <p class="text-xs text-red-300 mt-1 max-w-xs">
                            {{ session('error') ?? __('admin/categorias/index.check_errors') }}
                        </p>
                    </div>
                    <button onclick="document.getElementById('toast-notification').remove()" class="ml-2 text-red-600 hover:text-red-400 transition"><i class="fas fa-times"></i></button>
                </div>
            @endif
        </div>
        <script>
            setTimeout(() => {
                const toast = document.getElementById('toast-notification');
                if(toast) {
                    toast.classList.add('translate-y-10', 'opacity-0');
                    setTimeout(() => toast.remove(), 500);
                }
            }, 6000);
        </script>
    @endif

    <div class="bg-slate-800 rounded-2xl border border-slate-300 overflow-hidden shadow-2xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-400">
                <thead class="bg-slate-900 text-slate-300 uppercase text-[10px] font-black tracking-widest">
                    <tr>
                        <th class="px-5 py-4 w-1/3">{{ __('admin/categorias/index.col_es') }}</th>
                        <th class="px-5 py-4 w-1/4">{{ __('admin/categorias/index.col_en') }}</th>
                        <th class="px-5 py-4 w-1/4">{{ __('admin/categorias/index.col_pt') }}</th>
                        <th class="px-5 py-4 text-right">{{ __('admin/categorias/index.col_actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/50">
                    @forelse($categorias as $cat)
                    <tr class="hover:bg-slate-700/30 transition-colors">
                        <td class="px-5 py-4 font-bold text-white text-base">
                            {{ $cat->nombre }}
                        </td>
                        <td class="px-5 py-4 text-blue-300">
                            {{ $cat->nombre_en ?? '--' }}
                        </td>
                        <td class="px-5 py-4 text-emerald-300">
                            {{ $cat->nombre_pt ?? '--' }}
                        </td>
                        <td class="px-5 py-4 text-right flex justify-end gap-2">
                            <button type="button" 
                                    onclick="abrirModalCategoria({{ $cat->id }}, '{{ addslashes($cat->nombre) }}', '{{ addslashes($cat->nombre_en) }}', '{{ addslashes($cat->nombre_pt) }}')"
                                    class="bg-blue-600/20 text-blue-400 hover:bg-blue-600 hover:text-white h-8 w-8 rounded flex items-center justify-center transition-colors border border-blue-500/30" title="{{ __('admin/categorias/index.btn_edit') }}">
                                <i class="fas fa-edit text-xs"></i>
                            </button>

                            {{-- Formulario para borrar con ID único --}}
                            <form action="{{ route('admin.categorias.destroy', $cat->id) }}" method="POST" id="form-delete-{{ $cat->id }}">
                                @csrf @method('DELETE')
                                <button type="button" onclick="abrirModalBorrado('form-delete-{{ $cat->id }}')" class="bg-red-600/20 text-red-400 hover:bg-red-600 hover:text-white h-8 w-8 rounded flex items-center justify-center transition-colors border border-red-500/30" title="{{ __('admin/categorias/index.btn_delete') }}">
                                    <i class="fas fa-trash-alt text-xs"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-5 py-10 text-center text-slate-500">
                            {{ __('admin/categorias/index.no_categories') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- MODAL DE CREACIÓN / EDICIÓN --}}
<div id="modal-categoria" class="fixed inset-0 z-[100] hidden flex items-center justify-center p-4">
    <div class="fixed inset-0 bg-black/80 backdrop-blur-sm transition-opacity" onclick="cerrarModalCategoria()"></div>
    <div class="bg-slate-900 border border-slate-300 w-full max-w-lg rounded-3xl p-6 sm:p-8 shadow-2xl relative z-10 transform scale-95 opacity-0 transition-all duration-300" id="modal-panel">
        
        <h3 id="modal-title" class="text-2xl font-black text-white mb-6">Nueva Categoría</h3>
        
        <form id="form-categoria" action="{{ route('admin.categorias.store') }}" method="POST" class="space-y-5">
            @csrf
            <input type="hidden" name="_method" id="form-method" value="POST">

            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2"><span class="text-xl mr-1">🇲🇽</span> {{ __('admin/categorias/index.label_es') }}</label>
                <input type="text" name="nombre" id="input-es" required placeholder="{{ __('admin/categorias/index.ph_es') }}"
                       class="w-full bg-slate-800 text-white border border-slate-300 rounded-xl px-4 py-3 focus:outline-none focus:border-orange-500 transition shadow-inner">
                @error('nombre') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2"><span class="text-xl mr-1">🇺🇸</span> {{ __('admin/categorias/index.label_en') }}</label>
                <input type="text" name="nombre_en" id="input-en" placeholder="{{ __('admin/categorias/index.ph_en') }}"
                       class="w-full bg-slate-800 text-white border border-slate-300 rounded-xl px-4 py-3 focus:outline-none focus:border-blue-500 transition shadow-inner">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2"><span class="text-xl mr-1">🇧🇷</span> {{ __('admin/categorias/index.label_pt') }}</label>
                <input type="text" name="nombre_pt" id="input-pt" placeholder="{{ __('admin/categorias/index.ph_pt') }}"
                       class="w-full bg-slate-800 text-white border border-slate-300 rounded-xl px-4 py-3 focus:outline-none focus:border-emerald-500 transition shadow-inner">
            </div>

            <div class="flex gap-3 pt-4 border-t border-slate-800">
                <button type="button" onclick="cerrarModalCategoria()" class="flex-1 bg-slate-800 hover:bg-slate-700 text-white font-bold py-3.5 rounded-xl transition-all border border-slate-300">
                    {{ __('admin/categorias/index.btn_cancel') }}
                </button>
                <button type="submit" id="btn-submit-categoria" class="flex-1 bg-orange-700 hover:bg-orange-500 text-white font-bold py-3.5 rounded-xl shadow-lg transition-all flex justify-center items-center gap-2">
                    <i class="fas fa-save"></i> {{ __('admin/categorias/index.btn_save') }}
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL DE CONFIRMACIÓN DE BORRADO --}}
<div id="delete-modal" class="fixed inset-0 z-[110] hidden flex items-center justify-center bg-black/80 backdrop-blur-sm transition-opacity opacity-0 p-4">
    <div id="delete-modal-panel" class="bg-slate-900 border border-slate-300 p-6 sm:p-8 rounded-3xl shadow-2xl w-full max-w-sm transform scale-95 transition-all text-center">
        
        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full mb-5 bg-red-500/20 text-red-500 border border-red-500/50 shadow-[0_0_15px_rgba(239,68,68,0.3)]">
            <i class="fas fa-exclamation-triangle text-3xl animate-pulse"></i>
        </div>
        
        <h3 class="text-2xl font-black text-white mb-2">{{ __('admin/categorias/index.modal_del_title') }}</h3>
        <p class="text-sm text-slate-400 mb-8 leading-relaxed">{{ __('admin/categorias/index.modal_del_desc') }}</p>
        
        <div class="flex gap-3">
            <button onclick="cerrarModalBorrado()" class="flex-1 bg-slate-800 hover:bg-slate-700 text-white font-bold py-3.5 rounded-xl transition-all border border-slate-600">
                {{ __('admin/categorias/index.btn_cancel') }}
            </button>
            <button id="btn-confirm-delete" class="flex-1 bg-red-600 hover:bg-red-500 text-white font-bold py-3.5 rounded-xl shadow-lg transition-all shadow-red-900/50 flex justify-center items-center gap-2">
                <i class="fas fa-trash-alt"></i> {{ __('admin/categorias/index.btn_delete') }}
            </button>
        </div>
    </div>
</div>

<script>
    // =========================================================
    // 1. LÓGICA DEL MODAL DE CREACIÓN/EDICIÓN
    // =========================================================
    const modal = document.getElementById('modal-categoria');
    const panel = document.getElementById('modal-panel');
    const form = document.getElementById('form-categoria');
    const method = document.getElementById('form-method');
    const title = document.getElementById('modal-title');
    const btnSubmitCat = document.getElementById('btn-submit-categoria');
    
    const textNew = `{{ __('admin/categorias/index.modal_new_title') }}`;
    const textEdit = `{{ __('admin/categorias/index.modal_edit_title') }}`;

    let isSubmittingForm = false; // Candado para crear/editar

    function abrirModalCategoria(id = null, es = '', en = '', pt = '') {
        // Reseteamos el candado visual si se reabre el modal
        isSubmittingForm = false;
        if(btnSubmitCat) {
            btnSubmitCat.disabled = false;
            btnSubmitCat.style.pointerEvents = 'auto';
            btnSubmitCat.classList.remove('opacity-75', 'cursor-not-allowed');
            btnSubmitCat.innerHTML = `<i class="fas fa-save"></i> {{ __('admin/categorias/index.btn_save') }}`;
        }

        if (id) {
            title.innerText = textEdit;
            form.action = `/admin/categorias/${id}`;
            method.value = 'PUT';
        } else {
            title.innerText = textNew;
            form.action = `{{ route('admin.categorias.store') }}`;
            method.value = 'POST';
        }

        document.getElementById('input-es').value = es;
        document.getElementById('input-en').value = en;
        document.getElementById('input-pt').value = pt;

        modal.classList.remove('hidden');
        setTimeout(() => {
            panel.classList.remove('scale-95', 'opacity-0');
            panel.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function cerrarModalCategoria() {
        panel.classList.remove('scale-100', 'opacity-100');
        panel.classList.add('scale-95', 'opacity-0');
        setTimeout(() => modal.classList.add('hidden'), 300);
    }

    @if($errors->any())
        document.addEventListener('DOMContentLoaded', () => abrirModalCategoria());
    @endif

    // BLINDAJE DE FORMULARIO CREAR/EDITAR
    if (form && btnSubmitCat) {
        form.addEventListener('submit', function(e) {
            if (isSubmittingForm) {
                e.preventDefault();
                return;
            }

            if (form.checkValidity()) {
                isSubmittingForm = true;
                btnSubmitCat.disabled = true;
                btnSubmitCat.style.pointerEvents = 'none';
                btnSubmitCat.classList.add('opacity-75', 'cursor-not-allowed');
                btnSubmitCat.innerHTML = `<i class="fas fa-spinner fa-spin mr-2"></i> Procesando...`;
            }
        });
    }

    // =========================================================
    // 2. LÓGICA DEL MODAL DE BORRADO
    // =========================================================
    let formToSubmit = null;
    let isDeleting = false; // Candado para eliminar
    const btnConfirmDelete = document.getElementById('btn-confirm-delete');

    function abrirModalBorrado(formId) {
        // Reset del candado
        isDeleting = false;
        if(btnConfirmDelete) {
            btnConfirmDelete.disabled = false;
            btnConfirmDelete.style.pointerEvents = 'auto';
            btnConfirmDelete.classList.remove('opacity-75', 'cursor-not-allowed');
            btnConfirmDelete.innerHTML = `<i class="fas fa-trash-alt"></i> {{ __('admin/categorias/index.btn_delete') }}`;
        }

        formToSubmit = formId;
        const deleteModal = document.getElementById('delete-modal');
        const deletePanel = document.getElementById('delete-modal-panel');
        
        deleteModal.classList.remove('hidden');
        setTimeout(() => {
            deleteModal.classList.remove('opacity-0');
            deletePanel.classList.remove('scale-95');
            deletePanel.classList.add('scale-100');
        }, 10);
    }

    function cerrarModalBorrado() {
        const deleteModal = document.getElementById('delete-modal');
        const deletePanel = document.getElementById('delete-modal-panel');
        
        deleteModal.classList.add('opacity-0');
        deletePanel.classList.remove('scale-100');
        deletePanel.classList.add('scale-95');
        
        setTimeout(() => {
            deleteModal.classList.add('hidden');
            formToSubmit = null;
        }, 300);
    }

    // BLINDAJE DEL BOTÓN CONFIRMAR ELIMINAR
    if (btnConfirmDelete) {
        btnConfirmDelete.addEventListener('click', function() {
            if (isDeleting) return;

            if (formToSubmit) {
                isDeleting = true;
                btnConfirmDelete.disabled = true;
                btnConfirmDelete.style.pointerEvents = 'none';
                btnConfirmDelete.classList.add('opacity-75', 'cursor-not-allowed');
                btnConfirmDelete.innerHTML = `<i class="fas fa-spinner fa-spin mr-2"></i> Eliminando...`;
                
                document.getElementById(formToSubmit).submit();
            }
        });
    }
</script>
@endsection