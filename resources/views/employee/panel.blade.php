@extends('layouts.employee')

@section('titulo', 'Dashboard Operativo')

@section('contenido')
<div class="container mx-auto p-6 md:p-10 h-full flex flex-col overflow-y-auto custom-scrollbar">
    
    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row justify-between items-end mb-8 gap-4 shrink-0">
        <div>
            <h1 class="text-3xl md:text-4xl font-bold text-white mb-1">Hola, {{ Auth::user()->name }}</h1>
            <p class="text-slate-400">Panel de Control Operativo</p>
        </div>
        <div class="text-right hidden md:block">
            <p class="text-2xl font-bold text-slate-200">{{ now()->format('H:i') }}</p>
            <p class="text-sm text-slate-500 uppercase tracking-wider font-bold">{{ now()->isoFormat('dddd D, MMMM') }}</p>
        </div>
    </div>

    {{-- GRID PRINCIPAL --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pb-10">
        
        {{-- ==========================================
             COLUMNA IZQUIERDA (GIGANTE): VENTA
             ========================================== --}}
        <a href="{{ route('employee.pos') }}" 
           class="md:col-span-2 min-h-[300px] md:min-h-full bg-gradient-to-br from-emerald-600 to-emerald-900 rounded-3xl p-8 relative overflow-hidden group shadow-2xl hover:shadow-emerald-500/20 transition-all hover:-translate-y-1 flex flex-col justify-between border border-emerald-500/30">
            
            <div class="absolute right-0 top-0 p-8 opacity-10 group-hover:opacity-20 transition-opacity duration-500">
                <i class="fas fa-cash-register text-9xl text-emerald-300 transform -rotate-12 translate-x-10 -translate-y-10"></i>
            </div>
            
            <div class="relative z-10">
                <span class="bg-emerald-500/20 text-emerald-200 border border-emerald-400/20 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider mb-4 inline-block">
                    Prioridad Alta
                </span>
                <h2 class="text-4xl md:text-5xl font-bold text-white mb-2 leading-tight">Nueva<br>Venta</h2>
                <p class="text-emerald-100/70 text-lg max-w-sm">
                    Abrir comandera para pedidos en mesa o para llevar.
                </p>
            </div>

            <div class="flex justify-between items-end relative z-10 mt-8">
                <div class="bg-emerald-950/50 backdrop-blur-md rounded-xl p-4 border border-emerald-500/20">
                    <p class="text-emerald-400 text-xs uppercase font-bold mb-1">Sistema POS</p>
                    <p class="text-white text-lg font-bold flex items-center gap-2">
                        <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span> Activo
                    </p>
                </div>
                <div class="bg-white text-emerald-900 rounded-full w-14 h-14 flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-arrow-right text-xl"></i>
                </div>
            </div>
        </a>

        {{-- ==========================================
             COLUMNA DERECHA: CAJA, COCINA, STOCK, REPARTIDOR
             ========================================== --}}
        <div class="flex flex-col gap-6">
            
            {{-- 1. MONITOR DE CAJA (VIOLETA) --}}
            <a href="{{ route('employee.orders.index') }}" 
               class="flex-1 min-h-[160px] bg-gradient-to-br from-violet-900 to-slate-900 border border-violet-500/30 rounded-3xl p-6 relative overflow-hidden group hover:shadow-lg hover:shadow-violet-500/20 transition-all hover:-translate-y-1 flex flex-col justify-center">
                
                <div class="absolute right-0 top-0 opacity-10 group-hover:opacity-20 transition-opacity duration-500">
                    <i class="fas fa-wallet text-8xl text-violet-400 transform -rotate-12 translate-x-4 -translate-y-2"></i>
                </div>

                <div class="relative z-10">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-10 h-10 bg-violet-500/20 rounded-lg flex items-center justify-center text-violet-400 border border-violet-500/20">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <p class="text-violet-400 text-xs font-bold uppercase tracking-wider">Cobranza</p>
                    </div>
                    <h3 class="text-2xl font-bold text-white leading-tight">Monitor de<br>Caja</h3>
                </div>
            </a>

            {{-- 2. MONITOR DE COCINA (NARANJA) --}}
            <a href="{{ route('kitchen.live') }}" 
               class="flex-1 min-h-[160px] bg-gradient-to-br from-orange-900 to-slate-900 border border-orange-500/30 rounded-3xl p-6 relative overflow-hidden group hover:shadow-lg hover:shadow-orange-500/20 transition-all hover:-translate-y-1 flex flex-col justify-center">
                
                <div class="absolute right-0 top-0 opacity-10 group-hover:opacity-20 transition-opacity duration-500">
                    <i class="fas fa-fire text-8xl text-orange-400 transform -rotate-12 translate-x-4 -translate-y-2"></i>
                </div>

                <div class="relative z-10">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-10 h-10 bg-orange-500/20 rounded-lg flex items-center justify-center text-orange-400 border border-orange-500/20">
                            <i class="fas fa-utensils"></i>
                        </div>
                        <p class="text-orange-400 text-xs font-bold uppercase tracking-wider">Producción</p>
                    </div>
                    <h3 class="text-2xl font-bold text-white leading-tight">Pantalla<br>Cocina</h3>
                </div>
            </a>

            {{-- 3. MODO REPARTIDOR (NUEVO - ROSA) --}}
            <a href="{{ route('delivery.scan') }}" 
               class="flex-1 min-h-[160px] bg-gradient-to-br from-pink-900 to-slate-900 border border-pink-500/30 rounded-3xl p-6 relative overflow-hidden group hover:shadow-lg hover:shadow-pink-500/20 transition-all hover:-translate-y-1 flex flex-col justify-center">
                
                <div class="absolute right-0 top-0 opacity-10 group-hover:opacity-20 transition-opacity duration-500">
                    <i class="fas fa-motorcycle text-8xl text-pink-400 transform -rotate-12 translate-x-4 -translate-y-2"></i>
                </div>

                <div class="relative z-10">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-10 h-10 bg-pink-500/20 rounded-lg flex items-center justify-center text-pink-400 border border-pink-500/20">
                            <i class="fas fa-qrcode"></i>
                        </div>
                        <p class="text-pink-400 text-xs font-bold uppercase tracking-wider">Entregas</p>
                    </div>
                    <h3 class="text-2xl font-bold text-white leading-tight">Modo<br>Repartidor</h3>
                    <p class="text-pink-200/40 text-xs mt-1">Escanear QR de pedidos</p>
                </div>
            </a>

            {{-- 4. GESTIÓN DE STOCK (AZUL) --}}
            <a href="{{ route('stock.index') }}" 
               class="flex-1 min-h-[160px] bg-gradient-to-br from-blue-900 to-slate-900 border border-blue-500/30 rounded-3xl p-6 relative overflow-hidden group hover:shadow-lg hover:shadow-blue-500/20 transition-all hover:-translate-y-1 flex flex-col justify-center">
                
                <div class="absolute right-0 top-0 opacity-10 group-hover:opacity-20 transition-opacity duration-500">
                    <i class="fas fa-boxes text-8xl text-blue-400 transform -rotate-12 translate-x-4 -translate-y-2"></i>
                </div>

                <div class="relative z-10">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-10 h-10 bg-blue-500/20 rounded-lg flex items-center justify-center text-blue-400 border border-blue-500/20">
                            <i class="fas fa-clipboard-check"></i>
                        </div>
                        <p class="text-blue-400 text-xs font-bold uppercase tracking-wider">Insumos</p>
                    </div>
                    <h3 class="text-2xl font-bold text-white leading-tight">Control de<br>Stock</h3>
                </div>
            </a>

        </div>
    </div>
</div>
@endsection