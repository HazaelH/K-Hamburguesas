@extends('layouts.admin')

@section('title', 'Detalle del Pedido #' . $id)

@section('content')

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detalle del Pedido #{{ $id }}</h1>
        <a href="{{ route('admin.orders.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Volver
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Información del Cliente</h6>
                </div>
                <div class="card-body">
                    <p><strong>Cliente:</strong> {{ $infoPedido->nombre_cliente }}</p>
                    <p><strong>Email:</strong> {{ $infoPedido->email_cliente }}</p>
                    <p><strong>Fecha:</strong> {{ $infoPedido->fecha_pedido }}</p>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">📦 Dirección de Entrega</h6>
                </div>
                <div class="card-body">
                    @if($infoPedido->calle)
                        <p><strong>Dirección:</strong> 
                            {{ $infoPedido->calle }} #{{ $infoPedido->numero_exterior }}
                            @if($infoPedido->numero_interior) Int. {{ $infoPedido->numero_interior }} @endif
                        </p>
                        <p><strong>Colonia:</strong> {{ $infoPedido->colonia }} - {{ $infoPedido->municipio }}</p>
                        <p><strong>C.P.:</strong> {{ $infoPedido->codigo_postal }}</p>
                        <p><strong>Teléfono:</strong> {{ $infoPedido->telefono }}</p>
                        @if($infoPedido->referencias)
                            <p class="text-muted"><em>Ref: {{ $infoPedido->referencias }}</em></p>
                        @endif
                    @else
                        <p class="text-muted">No se registró dirección (Recoger en tienda)</p>
                    @endif
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Items del Pedido</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th>Producto</th>
                                    <th>Precio</th>
                                    <th>Cant.</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $granTotal = 0; @endphp
                                @foreach($items as $item)
                                    <tr>
                                        <td>{{ $item->nombre_producto }}</td>
                                        <td>${{ number_format($item->precio_unitario, 2) }}</td>
                                        <td>{{ $item->cantidad }}</td>
                                        <td>${{ number_format($item->subtotal, 2) }}</td>
                                    </tr>
                                    @php $granTotal += $item->subtotal; @endphp
                                @endforeach
                                <tr class="table-success font-weight-bold">
                                    <td colspan="3" class="text-right">GRAN TOTAL:</td>
                                    <td>${{ number_format($granTotal, 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 text-right">
                        <form action="{{ route('admin.orders.complete', $id) }}" method="POST" class="form-confirm-complete d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-check"></i> Marcar como Entregado
                            </button>
                        </form>
                    </div>

                </div>
            </div>

        </div>
    </div>

@endsection

@push('scripts')
    @vite(['resources/js/admin/orders.js'])
@endpush