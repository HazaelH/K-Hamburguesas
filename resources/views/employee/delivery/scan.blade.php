@extends('layouts.employee')

@section('titulo', 'Escáner de Entregas')

@section('contenido')

{{-- Librería externa necesaria para el escáner --}}
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

{{-- Cargamos nuestros estilos y lógica separados --}}
@vite(['resources/css/scanner.css', 'resources/js/employee/scanner.js'])

<div class="min-h-screen bg-slate-950 flex flex-col items-center justify-start py-8 px-4">
    
    {{-- Header --}}
    <div class="text-center mb-6">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-slate-900 rounded-full mb-3 border border-slate-700 shadow-lg shadow-orange-500/10">
            <i class="fas fa-motorcycle text-3xl text-orange-500"></i>
        </div>
        <h1 class="text-2xl font-bold text-white">Modo Repartidor</h1>
        <p class="text-slate-400 text-sm">Escanea el QR del cliente para entregar</p>
    </div>

    {{-- ALERTA DE RESULTADO (Éxito) --}}
    @if(session('success'))
        <div class="w-full max-w-md bg-emerald-500/10 border border-emerald-500 text-emerald-400 px-4 py-4 rounded-xl mb-6 flex items-center gap-3 shadow-lg animate-bounce-in">
            <i class="fas fa-check-circle text-2xl"></i>
            <div>
                <p class="font-bold text-lg">¡Entrega Exitosa!</p>
                <p class="text-xs text-emerald-300">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    {{-- ALERTA DE RESULTADO (Error) --}}
    @if(session('error'))
        <div class="w-full max-w-md bg-red-500/10 border border-red-500 text-red-400 px-4 py-4 rounded-xl mb-6 flex items-center gap-3 shadow-lg animate-shake">
            <i class="fas fa-times-circle text-2xl"></i>
            <div>
                <p class="font-bold text-lg">Error</p>
                <p class="text-xs text-red-300">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    {{-- ZONA DE CÁMARA --}}
    <div class="w-full max-w-md bg-black rounded-2xl overflow-hidden shadow-2xl border border-slate-700 relative">
        {{-- Aquí se renderiza la cámara (Controlado por JS) --}}
        <div id="reader" class="w-full"></div>
        
        {{-- Overlay decorativo --}}
        <div class="absolute top-0 left-0 w-full h-full pointer-events-none border-[30px] border-slate-950/50 flex items-center justify-center">
            <div class="w-64 h-64 border-2 border-orange-500/50 rounded-lg relative">
                <div class="absolute top-0 left-0 w-4 h-4 border-t-2 border-l-2 border-orange-500"></div>
                <div class="absolute top-0 right-0 w-4 h-4 border-t-2 border-r-2 border-orange-500"></div>
                <div class="absolute bottom-0 left-0 w-4 h-4 border-b-2 border-l-2 border-orange-500"></div>
                <div class="absolute bottom-0 right-0 w-4 h-4 border-b-2 border-r-2 border-orange-500"></div>
            </div>
        </div>
    </div>

    <p class="text-slate-500 text-xs mt-4 text-center">
        Apunta la cámara al código QR del cliente.<br>
        Asegúrate de tener buena iluminación.
    </p>

    {{-- FORMULARIO OCULTO (JS lo usa para enviar el código) --}}
    <form id="scan-form" action="{{ route('employee.orders.scan') }}" method="POST" class="hidden">
        @csrf
        <input type="text" name="codigo" id="codigo-input">
    </form>

    {{-- INGRESO MANUAL (Backup por si falla la cámara) --}}
    <div class="mt-8 w-full max-w-md">
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-keyboard text-slate-500"></i>
            </div>
            <form action="{{ route('employee.orders.scan') }}" method="POST">
                @csrf
                <input type="text" name="codigo" placeholder="¿No escanea? Ingresa el código manual" 
                    class="block w-full pl-10 pr-20 py-4 border border-slate-700 rounded-xl bg-slate-900 text-white placeholder-slate-500 focus:outline-none focus:border-orange-500 transition-colors shadow-lg">
                <button type="submit" class="absolute inset-y-0 right-0 px-4 text-orange-500 font-bold hover:text-orange-400">
                    VALIDAR
                </button>
            </form>
        </div>
    </div>

</div>
@endsection