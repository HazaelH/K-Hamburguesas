@extends('layouts.admin')
@vite(['resources/js/admin/image-preview.js'])

@section('titulo', 'Nuevo Platillo')

@section('contenido')
<div class="max-w-4xl mx-auto py-8">
    
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-white">
            <i class="fas fa-plus-circle text-orange-500 mr-2"></i> Nuevo Platillo
        </h1>
        <a href="{{ route('admin.products.index') }}" class="text-gray-400 hover:text-white transition flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Cancelar
        </a>
    </div>

    <div class="bg-gray-800 rounded-2xl shadow-2xl border border-gray-700 p-8">
        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-300 mb-2">Nombre del Platillo</label>
                        <input type="text" name="nombre" value="{{ old('nombre') }}" required placeholder="Ej: Hamburguesa Monster"
                               class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg p-3 focus:outline-none focus:border-orange-500 transition">
                        @error('nombre') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-300 mb-2">Precio ($)</label>
                        <div class="relative">
                            <span class="absolute left-3 top-3 text-gray-500">$</span>
                            <input type="number" name="precio" step="0.50" value="{{ old('precio') }}" required placeholder="0.00"
                                   class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg p-3 pl-8 focus:outline-none focus:border-orange-500 transition">
                        </div>
                        @error('precio') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-300 mb-2">Categoría</label>
                        <input list="categorias-list" name="categoria" value="{{ old('categoria') }}" required placeholder="Ej: Bebidas, Hamburguesas..."
                               class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg p-3 focus:outline-none focus:border-orange-500 transition">
                        
                        <datalist id="categorias-list">
                            @foreach($categorias as $cat)
                                <option value="{{ $cat }}">
                            @endforeach
                        </datalist>
                        @error('categoria') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-300 mb-2">Descripción</label>
                        <textarea name="descripcion" rows="4" placeholder="Ingredientes, detalles..."
                                  class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg p-3 focus:outline-none focus:border-orange-500 transition">{{ old('descripcion') }}</textarea>
                    </div>
                </div>

                <div class="flex flex-col">
                    <label class="block text-sm font-bold text-gray-300 mb-2">Fotografía del Platillo</label>
                    
                    <div class="flex-1 bg-gray-900 border-2 border-dashed border-gray-600 rounded-xl flex items-center justify-center relative overflow-hidden group hover:border-orange-500 transition" id="image-preview-container">
                        
                        <div class="text-center p-6" id="placeholder-text">
                            <i class="fas fa-cloud-upload-alt text-4xl text-gray-500 mb-2"></i>
                            <p class="text-gray-400 text-sm">Click para subir imagen</p>
                            <p class="text-gray-600 text-xs mt-1">JPG, PNG, WEBP (Max 3MB)</p>
                        </div>

                        <img id="preview-img" src="#" alt="Vista previa" class="absolute inset-0 w-full h-full object-cover hidden">
                        
                        <input type="file" name="imagen" id="imagen-input" accept="image/*"
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                    </div>
                    @error('imagen') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="border-t border-gray-700 pt-6 mt-6">
                <button type="submit" 
                        class="w-full bg-orange-600 hover:bg-orange-700 text-white font-bold py-4 rounded-xl shadow-lg transform transition hover:scale-[1.01]">
                    <i class="fas fa-save mr-2"></i> Guardar Producto
                </button>
            </div>
        </form>
    </div>
</div>



@endsection