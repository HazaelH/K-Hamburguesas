@extends('layouts.employee')

@section('titulo', __('employee/delivery/scan.title'))

@section('contenido')

<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

@vite(['resources/css/scanner.css', 'resources/js/employee/scanner.js', 'resources/js/employee/delivery_app.js'])

<div class="min-h-screen bg-slate-950 flex flex-col items-center justify-start pb-20 relative">
    
    <div class="w-full bg-slate-900 border-b border-slate-800 px-6 py-4 flex justify-between items-center sticky top-0 z-10 shadow-lg">
        <div>
            <h1 class="text-xl font-black text-white tracking-wide">{{ __('employee/delivery/scan.brand') }}</h1>
            <p class="text-emerald-400 text-xs font-bold flex items-center gap-1">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> {{ __('employee/delivery/scan.on_route') }}
            </p>
        </div>
        <div class="text-right">
            <p class="text-slate-400 text-xs uppercase font-bold tracking-wider">{{ __('employee/delivery/scan.today') }}</p>
            <p class="text-white font-black text-xl"><i class="fas fa-check-circle text-orange-500"></i> {{ $entregasHoy ?? 0 }}</p>
        </div>
    </div>

    <div class="w-full max-w-md px-4 mt-6">
        
        @if(session('success'))
            <div class="bg-emerald-500/10 border border-emerald-500 text-emerald-400 px-4 py-4 rounded-xl mb-6 flex items-center gap-3 shadow-lg animate-bounce-in">
                <i class="fas fa-check-circle text-2xl"></i>
                <div>
                    <p class="font-bold text-lg">{{ __('employee/delivery/scan.delivery_success') }}</p>
                    <p class="text-xs text-emerald-300">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-500/10 border border-red-500 text-red-400 px-4 py-4 rounded-xl mb-6 flex items-center gap-3 shadow-lg animate-shake">
                <i class="fas fa-times-circle text-2xl"></i>
                <div>
                    <p class="font-bold text-lg">{{ __('employee/delivery/scan.error_title') }}</p>
                    <p class="text-xs text-red-300">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        <div class="bg-slate-900 rounded-3xl p-4 shadow-xl border border-slate-800 mb-8">
            <h2 class="text-white font-bold mb-4 flex items-center gap-2">
                <i class="fas fa-qrcode text-orange-500"></i> {{ __('employee/delivery/scan.scan_receipt') }}
            </h2>
            
            <div class="w-full bg-black rounded-2xl overflow-hidden border border-slate-300 relative aspect-square flex flex-col justify-center items-center">
                <div id="reader" class="w-full h-full absolute inset-0"></div>
                
                <div id="pantalla-inicio-lector" class="absolute inset-0 bg-slate-900/90 flex flex-col items-center justify-center z-10 transition-opacity duration-300">
                    <i class="fas fa-camera text-5xl text-slate-600 mb-4"></i>
                    <button onclick="window.abrirOpcionesLente()" class="bg-orange-700 hover:bg-orange-500 text-white font-bold py-3 px-6 rounded-xl shadow-lg shadow-orange-900/50 transition transform hover:scale-105">
                        {{ __('employee/delivery/scan.activate_camera') }}
                    </button>
                    <p class="text-xs text-slate-500 mt-4">{{ __('employee/delivery/scan.save_battery') }}</p>
                </div>

                <div id="borde-decorativo" class="hidden absolute top-0 left-0 w-full h-full pointer-events-none border-[30px] border-slate-950/50 flex items-center justify-center z-20">
                    <div class="w-48 h-48 border-2 border-orange-500/50 rounded-lg relative">
                        <div class="absolute top-0 left-0 w-4 h-4 border-t-2 border-l-2 border-orange-500"></div>
                        <div class="absolute top-0 right-0 w-4 h-4 border-t-2 border-r-2 border-orange-500"></div>
                        <div class="absolute bottom-0 left-0 w-4 h-4 border-b-2 border-l-2 border-orange-500"></div>
                        <div class="absolute bottom-0 right-0 w-4 h-4 border-b-2 border-r-2 border-orange-500"></div>
                    </div>
                </div>
            </div>

            <form id="scan-form" action="{{ route('repartidor.orders.scan') }}" method="POST" class="hidden">
                @csrf
                <input type="text" name="codigo" id="codigo-input">
            </form>

            <form action="{{ route('repartidor.orders.scan') }}" method="POST" class="mt-4 relative">
                @csrf
                <input type="text" name="codigo" placeholder="{{ __('employee/delivery/scan.manual_code_placeholder') }}" 
                    class="w-full pl-4 pr-24 py-3 border border-slate-300 rounded-xl bg-slate-950 text-white text-sm placeholder-slate-600 focus:outline-none focus:border-orange-500 transition-colors uppercase">
                <button type="submit" class="absolute inset-y-0 right-1 my-1 px-4 bg-orange-700 hover:bg-orange-500 text-white rounded-lg text-xs font-bold transition">
                    {{ __('employee/delivery/scan.btn_validate') }}
                </button>
            </form>
        </div>

        @if(isset($pedidosDisponibles) && $pedidosDisponibles->count() > 0)
        <div class="mb-8">
            <h2 class="text-white font-bold mb-4 flex items-center justify-between">
                <span class="flex items-center gap-2"><i class="fas fa-motorcycle text-orange-500"></i> {{ __('employee/delivery/scan.available_to_pickup') }}</span>
                <span class="bg-orange-700 text-white text-[10px] px-2 py-1 rounded-full">{{ $pedidosDisponibles->count() }}</span>
            </h2>

            <div class="space-y-4">
                @foreach($pedidosDisponibles as $pedido)
                    <div class="bg-slate-900 rounded-2xl p-4 border border-orange-500/30 shadow-[0_0_15px_rgba(234,88,12,0.1)] relative">
                        <div class="flex justify-between items-center mb-2">
                            <div>
                                <p class="text-[10px] text-orange-400 font-bold uppercase tracking-widest">{{ __('employee/delivery/scan.order_number', ['id' => $pedido->id]) }}</p>
                                <p class="text-white font-bold">{{ $pedido->cliente_nombre ?? __('employee/delivery/scan.default_client') }}</p>
                            </div>
                            <span class="text-emerald-400 font-mono font-bold">{{ formatCurrency($pedido->total) }}</span>
                        </div>
                        
                        <p class="text-xs text-slate-400 mb-4 line-clamp-1"><i class="fas fa-map-marker-alt"></i> {{ $pedido->direccion }}</p>
                        
                        <button onclick="window.tomarViaje({{ $pedido->id }})" id="btn-tomar-{{ $pedido->id }}" class="w-full bg-orange-700 hover:bg-orange-500 text-white font-black py-3 rounded-xl shadow-lg transition-all active:scale-95 flex justify-center items-center gap-2">
                            <i class="fas fa-hand-paper"></i> {{ __('employee/delivery/scan.btn_take_trip') }}
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <div>
            <h2 class="text-white font-bold mb-4 flex items-center justify-between">
                <span class="flex items-center gap-2"><i class="fas fa-map-marked-alt text-blue-500"></i> {{ __('employee/delivery/scan.your_active_deliveries') }}</span>
                <span class="bg-blue-600 text-white text-[10px] px-2 py-1 rounded-full">{{ $pedidosEnCamino->count() ?? 0 }}</span>
            </h2>

            <div class="space-y-4">
                @forelse($pedidosEnCamino ?? [] as $pedido)
                    @php
                        $direccion = $pedido->direccion;
                        $telefono = $pedido->telefono;

                        if (empty($direccion) || empty($telefono)) {
                            $bruto = $pedido->datos_entrega;
                            $arregloInfo = is_string($bruto) ? json_decode($bruto, true) : (array) $bruto;
                            if (!is_array($arregloInfo)) $arregloInfo = [];

                            if (empty($direccion)) $direccion = $arregloInfo['direccion'] ?? $arregloInfo['calle'] ?? __('employee/delivery/scan.location_unspecified');
                            if (empty($telefono)) $telefono = $arregloInfo['telefono'] ?? $arregloInfo['celular'] ?? __('employee/delivery/scan.no_contact');
                        }
                        $nombre = $pedido->cliente_nombre ?? ($pedido->user->name ?? __('employee/delivery/scan.general_consumer'));
                    @endphp

                    <div class="bg-slate-900 rounded-2xl p-5 border border-slate-800 shadow-lg relative overflow-hidden">
                        <div class="absolute top-0 left-0 w-1 h-full bg-blue-500"></div>
                        
                        <div class="flex justify-between items-start mb-3 pl-2">
                            <div>
                                <p class="text-xs text-slate-500 font-bold uppercase">{{ __('employee/delivery/scan.order_number', ['id' => $pedido->id]) }}</p>
                                <h3 class="text-white font-black text-lg">{{ $nombre }}</h3>
                            </div>
                            <span class="bg-slate-800 text-slate-300 border border-slate-300 px-2 py-1 rounded text-xs font-bold">
                                {{ formatCurrency($pedido->total) }}
                            </span>
                        </div>

                        <div class="text-sm text-slate-400 pl-2 mb-4 space-y-1">
                            <p class="flex items-start gap-2">
                                <i class="fas fa-map-marker-alt mt-1 text-slate-500"></i> 
                                <span class="leading-tight">{{ $direccion }}</span>
                            </p>
                        </div>

                        <div class="grid grid-cols-2 gap-2 pl-2 mb-2">
                            <button onclick="window.avisarCliente({{ $pedido->id }}, 'en_camino_real', this)" class="bg-indigo-600/20 text-indigo-400 hover:bg-indigo-600 hover:text-white border border-indigo-500/30 py-2 rounded-xl text-[11px] uppercase tracking-wider font-black transition flex items-center justify-center gap-1.5 shadow-sm">
                                <i class="fas fa-rocket"></i> {{ __('employee/delivery/scan.btn_on_my_way') }}
                            </button>
                            
                            <button onclick="window.avisarCliente({{ $pedido->id }}, 'afuera', this)" class="bg-pink-600/20 text-pink-400 hover:bg-pink-600 hover:text-white border border-pink-500/30 py-2 rounded-xl text-[11px] uppercase tracking-wider font-black transition flex items-center justify-center gap-1.5 shadow-sm">
                                <i class="fas fa-map-pin"></i> {{ __('employee/delivery/scan.btn_arrived') }}
                            </button>
                        </div>

                        <div class="flex gap-2 pl-2">
                            <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($direccion) }}" target="_blank" 
                            class="flex-1 bg-blue-600/20 text-blue-400 hover:bg-blue-600 hover:text-white border border-blue-500/30 text-center py-2.5 rounded-xl text-sm font-bold transition flex items-center justify-center gap-2">
                                <i class="fas fa-location-arrow"></i> {{ __('employee/delivery/scan.btn_route') }}
                            </a>
                            
                            @if($telefono !== __('employee/delivery/scan.no_contact'))
                                @php
                                    $telLimpio = preg_replace('/[^0-9]/', '', $telefono);
                                    if(strlen($telLimpio) == 10) $telLimpio = '52' . $telLimpio; 
                                    $msjWA = urlencode(__('employee/delivery/scan.wa_message', ['id' => $pedido->id]));
                                @endphp
                                <a href="https://wa.me/{{ $telLimpio }}?text={{ $msjWA }}" target="_blank"
                                   class="flex-1 bg-emerald-600/20 text-emerald-400 hover:bg-emerald-600 hover:text-white border border-emerald-500/30 text-center py-2.5 rounded-xl text-sm font-bold transition flex items-center justify-center gap-2">
                                    <i class="fab fa-whatsapp text-lg"></i> {{ __('employee/delivery/scan.btn_chat') }}
                                </a>
                            @endif

                            <button onclick="window.abrirModalSOS({{ $pedido->id }})" class="w-12 shrink-0 bg-red-600/20 text-red-400 hover:bg-red-600 hover:text-white border border-red-500/30 text-center py-2.5 rounded-xl text-sm font-bold transition flex items-center justify-center">
                                <i class="fas fa-exclamation-triangle"></i>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-8 text-center border-dashed">
                        <div class="w-16 h-16 bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-box-open text-2xl text-slate-600"></i>
                        </div>
                        <p class="text-white font-bold">{{ __('employee/delivery/scan.no_packages') }}</p>
                        <p class="text-slate-500 text-sm mt-1">{{ __('employee/delivery/scan.go_to_counter') }}</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- MODAL 1: S.O.S --}}
    <div id="modal-sos" class="fixed inset-0 z-[120] hidden flex items-center justify-center bg-black/80 backdrop-blur-sm px-4 transition-opacity duration-300 opacity-0">
        <div class="bg-slate-900 border border-slate-300 w-full max-w-sm rounded-3xl p-6 shadow-2xl transform transition-all duration-300 scale-95" id="panel-sos">
            <div class="w-16 h-16 bg-red-500/20 text-red-500 rounded-full flex items-center justify-center text-3xl mx-auto mb-4 border border-red-500/30">
                <i class="fas fa-bullhorn animate-pulse"></i>
            </div>
            <h3 class="text-xl font-black text-white text-center mb-2">{{ __('employee/delivery/scan.report_problem') }}</h3>
            <p class="text-slate-400 text-xs text-center mb-4">{{ __('employee/delivery/scan.admin_alert_desc') }}<span id="sos-order-id" class="font-black text-white"></span></p>
            
            <textarea id="sos-mensaje" rows="3" placeholder="{{ __('employee/delivery/scan.sos_placeholder') }}" class="w-full bg-slate-800 border border-slate-300 rounded-xl p-3 text-white text-sm focus:outline-none focus:border-red-500 mb-4 resize-none custom-scrollbar"></textarea>
            
            <div class="flex gap-3">
                <button onclick="window.cerrarModalSOS()" class="flex-1 bg-slate-800 hover:bg-slate-700 text-white font-bold py-3 rounded-xl transition">{{ __('employee/delivery/scan.btn_cancel') }}</button>
                <button onclick="window.enviarSOS()" id="btn-enviar-sos" class="flex-1 bg-red-600 hover:bg-red-500 text-white font-bold py-3 rounded-xl shadow-lg transition flex justify-center items-center">
                    {{ __('employee/delivery/scan.btn_send_sos') }}
                </button>
            </div>
        </div>
    </div>

    {{-- MODAL 2: RECIBIR RESPUESTA --}}
    <div id="modal-respuesta-admin" class="fixed inset-0 z-[130] hidden flex items-center justify-center bg-black/80 backdrop-blur-sm px-4 transition-opacity duration-300 opacity-0">
        <div class="bg-blue-950 border border-blue-500/50 w-full max-w-sm rounded-3xl p-6 shadow-[0_0_40px_rgba(59,130,246,0.3)] transform transition-all duration-300 scale-95" id="panel-respuesta-admin">
            <div class="w-16 h-16 bg-blue-500/20 text-blue-400 rounded-full flex items-center justify-center text-3xl mx-auto mb-4 border border-blue-500/50">
                <i class="fas fa-headset animate-bounce"></i>
            </div>
            <h3 class="text-xl font-black text-white text-center mb-2">{{ __('employee/delivery/scan.admin_instruction') }}</h3>
            <p class="text-blue-300 text-xs text-center mb-4">{{ __('employee/delivery/scan.for_order') }}<span id="respuesta-order-id" class="font-black text-white"></span></p>
            
            <div class="bg-slate-900 p-4 rounded-xl border border-blue-500/30 mb-5 relative overflow-hidden">
                <div class="absolute top-0 left-0 w-1 h-full bg-blue-500"></div>
                <p class="text-white text-sm italic font-medium leading-relaxed" id="respuesta-mensaje">"..."</p>
            </div>
            
            <button onclick="window.cerrarModalRespuestaAdmin()" class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-3.5 rounded-xl shadow-lg transition flex justify-center items-center gap-2">
                <i class="fas fa-check-circle"></i> {{ __('employee/delivery/scan.btn_understood') }}
            </button>
        </div>
    </div>

    {{-- CUADRO DE DIÁLOGO: SELECCIÓN DE ÓPTICA --}}
    <div id="dialogo-seleccion-lente" class="fixed inset-0 z-[100] hidden flex items-end justify-center bg-black/80 backdrop-blur-sm sm:items-center">
        <div class="bg-slate-900 border border-slate-300 w-full sm:max-w-sm rounded-t-3xl sm:rounded-3xl p-6 shadow-2xl transform transition-transform translate-y-full sm:translate-y-0" id="panel-lente">
            <h3 class="text-xl font-black text-white text-center mb-6">{{ __('employee/delivery/scan.select_lens') }}</h3>
            
            <div class="grid grid-cols-2 gap-4">
                <button onclick="window.iniciarLectura('environment')" class="bg-slate-800 hover:bg-slate-700 border border-slate-300 rounded-2xl p-6 flex flex-col items-center gap-3 transition">
                    <i class="fas fa-camera text-3xl text-orange-500"></i>
                    <span class="text-white font-bold text-sm">{{ __('employee/delivery/scan.rear_lens') }}</span>
                </button>
                <button onclick="window.iniciarLectura('user')" class="bg-slate-800 hover:bg-slate-700 border border-slate-300 rounded-2xl p-6 flex flex-col items-center gap-3 transition">
                    <i class="fas fa-user-circle text-3xl text-blue-500"></i>
                    <span class="text-white font-bold text-sm">{{ __('employee/delivery/scan.front_lens') }}</span>
                </button>
            </div>
            <button onclick="window.cerrarOpcionesLente()" class="w-full mt-6 py-3 text-slate-400 font-bold hover:text-white transition">{{ __('employee/delivery/scan.btn_cancel') }}</button>
        </div>
    </div>

    <div id="dialogo-procesando" class="fixed inset-0 z-[110] hidden flex flex-col items-center justify-center bg-slate-950/95 backdrop-blur-md">
        <div class="relative w-24 h-24 flex items-center justify-center mb-6">
            <div class="absolute inset-0 border-4 border-slate-800 rounded-full"></div>
            <div class="absolute inset-0 border-4 border-orange-500 rounded-full border-t-transparent animate-spin"></div>
            <i class="fas fa-motorcycle text-3xl text-white"></i>
        </div>
        <h3 class="text-2xl font-black text-white tracking-widest animate-pulse">{{ __('employee/delivery/scan.validating') }}</h3>
        <p class="text-slate-400 mt-2 text-sm">{{ __('employee/delivery/scan.processing_secure') }}</p>
    </div>

</div>

<script>
    window.DELIVERY_LANG = {
        new_order_title: `{{ __('employee/delivery/scan.js_new_order_title') }}`,
        new_order_desc: `{{ __('employee/delivery/scan.js_new_order_desc') }}`,
        assigning: `{{ __('employee/delivery/scan.js_assigning') }}`,
        conn_error: `{{ __('employee/delivery/scan.js_conn_error') }}`,
        sos_empty: `{{ __('employee/delivery/scan.js_sos_empty') }}`,
        sos_sent: `{{ __('employee/delivery/scan.js_sos_sent') }}`,
        notifying: `{{ __('employee/delivery/scan.js_notifying') }}`,
        notified: `{{ __('employee/delivery/scan.js_notified') }}`,
        customer_notified: `{{ __('employee/delivery/scan.js_customer_notified') }}`,
        cam_access_error: `{{ __('employee/delivery/scan.js_cam_access_error') }}`
    };

    window.APP_CONFIG = {
        csrfToken: '{{ csrf_token() }}',
        rutas: {
            radarNuevos: '{{ route("repartidor.api.nuevos") }}',
            tomarPedido: (id) => `/empleado/repartidor/orden/${id}/tomar`,
            enviarSOS: (id) => `/empleado/repartidor/orden/${id}/sos`,
            marcarLeido: (id) => `/empleado/repartidor/orden/${id}/sos-leido`
        },
        ordenesConocidas: {!! json_encode(isset($pedidosDisponibles) ? $pedidosDisponibles->pluck('id')->toArray() : []) !!}
    };
</script>

@endsection