@extends('layouts.admin')

@section('titulo', 'Gestión del Menú')

@section('contenido')
<div class="py-6">
    
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-white">🍔 Menú del Restaurante</h1>
            <p class="text-gray-400">Administra tus platillos y precios</p>
        </div>
        <a href="{{ route('admin.products.create') }}" 
           class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-6 rounded-lg shadow-lg transition transform hover:scale-105">
            <i class="fas fa-plus mr-2"></i> Nuevo Platillo
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-500/20 border border-green-500 text-green-400 px-4 py-3 rounded relative mb-6" role="alert">
            <strong class="font-bold">¡Éxito!</strong>
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-gray-800 p-6 rounded-xl border border-gray-700 shadow-xl flex items-center">
            <div class="p-3 bg-blue-500/20 rounded-full text-blue-400 mr-4">
                <i class="fas fa-utensils text-2xl"></i>
            </div>
            <div>
                <p class="text-gray-400 text-sm">Total Platillos</p>
                <p class="text-2xl font-bold text-white">{{ $stats['total'] }}</p>
            </div>
        </div>
        <div class="bg-gray-800 p-6 rounded-xl border border-gray-700 shadow-xl flex items-center">
            <div class="p-3 bg-purple-500/20 rounded-full text-purple-400 mr-4">
                <i class="fas fa-tags text-2xl"></i>
            </div>
            <div>
                <p class="text-gray-400 text-sm">Categorías</p>
                <p class="text-2xl font-bold text-white">{{ $stats['categorias'] }}</p>
            </div>
        </div>
        <div class="bg-gray-800 p-6 rounded-xl border border-gray-700 shadow-xl flex items-center">
            <div class="p-3 bg-green-500/20 rounded-full text-green-400 mr-4">
                <i class="fas fa-dollar-sign text-2xl"></i>
            </div>
            <div>
                <p class="text-gray-400 text-sm">Precio Promedio</p>
                <p class="text-2xl font-bold text-white">${{ number_format($stats['precio_promedio'], 2) }}</p>
            </div>
        </div>
    </div>

    <div class="bg-gray-800 rounded-xl border border-gray-700 overflow-hidden shadow-2xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-gray-400">
                <thead class="bg-gray-900 text-gray-200 uppercase text-xs font-bold tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Imagen</th>
                        <th class="px-6 py-4">Nombre / Descripción</th>
                        <th class="px-6 py-4">Categoría</th>
                        <th class="px-6 py-4">Precio</th>
                        <th class="px-6 py-4 text-center">Estado</th>
                        <th class="px-6 py-4 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    @forelse($productos as $producto)
                    <tr class="hover:bg-gray-750 transition">
                        <td class="px-6 py-4">
                            @if($producto->imagen_url)
                                <img src="{{ asset('imagenes/' . $producto->imagen_url) }}" alt="{{ $producto->nombre }}" class="h-16 w-16 object-cover rounded-lg border border-gray-600">
                            @else
                                <div class="h-16 w-16 bg-gray-700 rounded-lg flex items-center justify-center text-gray-500 text-xs text-center border border-gray-600">
                                    Sin Foto
                                </div>
                            @endif
                        </td>
                        
                        <td class="px-6 py-4">
                            <p class="text-white font-bold text-lg">{{ $producto->nombre }}</p>
                            <p class="text-xs text-gray-500 truncate w-48">{{ $producto->descripcion }}</p>
                        </td>

                        <td class="px-6 py-4">
                            <span class="bg-gray-700 text-white text-xs px-3 py-1 rounded-full border border-gray-600">
                                {{ $producto->categoria }}
                            </span>
                        </td>

                        <td class="px-6 py-4 font-mono text-green-400 font-bold">
                            ${{ number_format($producto->precio, 2) }}
                        </td>

                        <td class="px-6 py-4 text-center">
                            @if($producto->is_available)
                                <span class="text-green-500 text-xs font-bold"><i class="fas fa-check-circle"></i> Disponible</span>
                            @else
                                <span class="text-red-500 text-xs font-bold"><i class="fas fa-times-circle"></i> Agotado</span>
                            @endif
                        </td>

                        <td class="px-6 py-4 text-right space-x-2">
                            <a href="{{ route('admin.products.edit', $producto->id_producto) }}" class="text-blue-400 hover:text-blue-300 transition" title="Editar">
                                <i class="fas fa-edit fa-lg"></i>
                            </a>

                            <form action="{{ route('admin.products.destroy', $producto->id_producto) }}" method="POST" class="inline-block" onsubmit="return confirm('¿Estás seguro de eliminar este platillo?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-400 hover:text-red-300 transition" title="Eliminar">
                                    <i class="fas fa-trash-alt fa-lg"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-hamburger text-4xl mb-3 opacity-50"></i>
                            <p>No hay productos registrados aún.</p>
                            <p class="text-sm">¡Empieza creando uno nuevo!</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="px-6 py-4 border-t border-gray-700 bg-gray-900">
            {{ $productos->links() }} 
            </div>
    </div>
</div>
@endsection