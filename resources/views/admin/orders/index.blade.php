@extends('layouts.admin')

@section('title', 'Gestión de Pedidos')

@section('content')

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Gestión de Pedidos</h1>
        <button class="btn btn-sm btn-primary shadow-sm" onclick="location.reload()">
            <i class="fas fa-sync-alt fa-sm text-white-50"></i> Actualizar
        </button>
    </div>

    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pedidos Pendientes</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats->total_pendientes }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-clock fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Items por Preparar</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats->total_items }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-utensils fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Tiempo Espera Prom.</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($stats->tiempo_espera_promedio, 0) }} min
                            </div>
                        </div>
                        <div class="col-auto"><i class="fas fa-stopwatch fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Listado de Pedidos Pendientes</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Hora</th>
                            <th>Cliente</th>
                            <th>Ubicación</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Espera</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pedidos as $pedido)
                            @php
                                $fechaPedido = \Carbon\Carbon::parse($pedido->fecha);
                                $minutosEspera = $fechaPedido->diffInMinutes(now());
                                $badgeColor = $minutosEspera > 30 ? 'badge-danger' : ($minutosEspera > 15 ? 'badge-warning' : 'badge-success');
                            @endphp
                            <tr>
                                <td>#{{ $pedido->id_grupo_pedido }}</td>
                                <td>{{ $fechaPedido->format('H:i') }}</td>
                                <td>{{ $pedido->nombre_cliente }}</td>
                                <td>
                                    {{ $pedido->municipio }} - {{ $pedido->colonia }}
                                    @if($pedido->telefono)
                                        <br><small class="text-muted">📞 {{ $pedido->telefono }}</small>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-info">{{ $pedido->total_items }}</span>
                                </td>
                                <td class="font-weight-bold text-success">${{ number_format($pedido->total_precio, 2) }}</td>
                                <td>
                                    <span class="badge {{ $badgeColor }}">
                                        {{ $minutosEspera }} min
                                    </span>
                                </td>
                                <td>
                                    <a href="#" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i> Ver
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">No hay pedidos pendientes por el momento.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="{{ asset('assets/vendor/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>

    @vite(['resources/js/admin/orders.js'])
@endpush

@push('styles')
    <link href="{{ asset('assets/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
@endpush