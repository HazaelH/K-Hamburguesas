@extends('layouts.admin')

@section('title', isset($producto) ? 'Editar Producto' : 'Nuevo Producto')

@section('content')

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ isset($producto) ? 'Editar Producto' : 'Crear Nuevo Producto' }}</h1>
        <a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Volver
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Información del Producto</h6>
                </div>
                <div class="card-body">
                    
                    {{-- Formulario detecta si es edición (PUT) o creación (POST) --}}
                    <form action="{{ isset($producto) ? route('admin.products.update', $producto->id_producto) : route('admin.products.store') }}" method="POST">
                        @csrf
                        @if(isset($producto))
                            @method('PUT')
                        @endif

                        <div class="form-group">
                            <label for="nombre">Nombre del Producto <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nombre" name="nombre" 
                                   value="{{ old('nombre', $producto->nombre ?? '') }}" required>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="precio">Precio <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">$</span>
                                    </div>
                                    <input type="number" class="form-control" id="precio" name="precio" step="0.01" min="0" 
                                           value="{{ old('precio', $producto->precio ?? '') }}" required>
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="categoria">Categoría</label>
                                <input type="text" class="form-control" id="categoria" name="categoria" 
                                       value="{{ old('categoria', $producto->categoria ?? '') }}" list="lista-categorias">
                                <datalist id="lista-categorias">
                                    <option value="Hamburguesas">
                                    <option value="Bebidas">
                                    <option value="Complementos">
                                </datalist>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="descripcion">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="3">{{ old('descripcion', $producto->descripcion ?? '') }}</textarea>
                        </div>

                        <div class="form-group">
                            <label for="imagen_url">URL de la Imagen</label>
                            <input type="text" class="form-control" id="imagen_url" name="imagen_url" 
                                   value="{{ old('imagen_url', $producto->imagen_url ?? '') }}">
                        </div>

                        <hr>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> {{ isset($producto) ? 'Actualizar' : 'Guardar' }}
                        </button>
                    </form>

                </div>
            </div>
        </div>
    </div>

@endsection