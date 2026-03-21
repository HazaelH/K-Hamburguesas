<?php

use Illuminate\Support\Facades\Route;
<<<<<<< HEAD
use Illuminate\Foundation\Auth\EmailVerificationRequest; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schedule;

// --- PAQUETE DE IDIOMAS ---
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

// --- CONTROLADORES GENERALES ---
use App\Http\Controllers\ClientController;
use App\Http\Controllers\KitchenController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\ZipCodeController;

// --- CONTROLADORES DE ADMINISTRACIÓN ---
use App\Http\Controllers\Admin\AdminController as DashboardController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\OfferController;
use App\Http\Controllers\Employee\PosController;

/*
|--------------------------------------------------------------------------
| RUTAS EXCLUIDAS DEL IDIOMA (APIs y AJAX en segundo plano)
|--------------------------------------------------------------------------
*/
Route::get('/api/producto/{id}', [ClientController::class, 'getProductDetails'])->name('api.product.details');
Route::get('/api/categoria/{categoria}', [ClientController::class, 'filterProducts'])->name('api.products.filter');
Route::get('/api/zip-codes/{cp}', [ZipCodeController::class, 'show']);
Route::get('/api/colonias/{nombre}', [ZipCodeController::class, 'searchByColonia']);

Route::get('/api/cliente/tracker', function () {
    if (!auth()->check()) return response()->json(['active' => false]);
    
    $pedidoActivo = \App\Models\Order::where('user_id', auth()->id())
        ->whereNotIn('status', ['entregado', 'cancelado'])
        ->latest()
        ->first();
        
    if (!$pedidoActivo) return response()->json(['active' => false]);
    
    $datos = is_string($pedidoActivo->datos_entrega) ? json_decode($pedidoActivo->datos_entrega, true) : ($pedidoActivo->datos_entrega ?? []);
    
    return response()->json([
        'active' => true,
        'id' => $pedidoActivo->id,
        'status' => $pedidoActivo->status,
        'notificacion' => $datos['notificacion_cliente'] ?? null
    ]);
})->name('api.cliente.tracker');


/*
|==========================================================================
| ZONA MULTI-IDIOMA (Afecta las URLs visibles del navegador)
|==========================================================================
*/
Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => [ 'localeSessionRedirect', 'localizationRedirect', 'localeViewPath' ]
], function() {

    /*
    |--------------------------------------------------------------------------
    | 1. AUTENTICACIÓN
    |--------------------------------------------------------------------------
    */
    Route::middleware(['guest'])->group(function () {
        Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [AuthController::class, 'login'])->name('login.post');
        Route::get('/registro', [AuthController::class, 'showRegister'])->name('register');
        Route::post('/registro', [AuthController::class, 'register'])->name('register.post');

        Route::get('/olvide-mi-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
        Route::post('/olvide-mi-password', [AuthController::class, 'sendResetLink'])->name('password.email');
        Route::get('/resetear-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
        Route::post('/resetear-password', [AuthController::class, 'resetPassword'])->name('password.update');
    });

    Route::middleware(['auth'])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    });

    /*
    |--------------------------------------------------------------------------
    | 1.1 VERIFICACIÓN DE CORREO
    |--------------------------------------------------------------------------
    */
    Route::get('/email/verify', function () {
        return view('auth.verify-email'); 
    })->middleware('auth')->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect()->route('home')->with('status', '¡Correo verificado correctamente!');
    })->middleware(['auth', 'signed'])->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('status', 'verification-link-sent');
    })->middleware(['auth', 'throttle:6,1'])->name('verification.send');

    /*
    |--------------------------------------------------------------------------
    | 2. ZONA PÚBLICA / CLIENTE
    |--------------------------------------------------------------------------
    */
    Route::get('/', [ClientController::class, 'home'])->name('home');      
    Route::get('/menu', [ClientController::class, 'menu'])->name('menu');  
    Route::get('/ofertas', [ClientController::class, 'offers'])->name('offers.index');
    Route::get('/terminos-y-condiciones', [ClientController::class, 'terms'])->name('terms');
    Route::get('/aviso-de-privacidad', [ClientController::class, 'privacy'])->name('privacy');

    Route::get('/carrito', [ClientController::class, 'viewCart'])->name('cart.index');
    Route::post('/carrito/agregar', [ClientController::class, 'addToCart'])->name('cart.add');
    Route::patch('/carrito/actualizar/{row_id}', [ClientController::class, 'updateCart'])->name('cart.update');
    Route::delete('/carrito/eliminar/{row_id}', [ClientController::class, 'removeFromCart'])->name('cart.remove');

    Route::middleware(['auth', 'role:cliente', 'verified'])->group(function () {
        Route::get('/pagar', [ClientController::class, 'checkout'])->name('checkout');
        Route::post('/pagar', [ClientController::class, 'processPayment'])->name('checkout.process');
        Route::get('/orden-confirmada/{order}', [ClientController::class, 'orderSuccess'])->name('order.success');
        Route::get('/ticket/{order}', [ClientController::class, 'ticket'])->name('ticket');
        Route::get('/mi-ticket/{id}', [App\Http\Controllers\ClientController::class, 'ticket'])->name('client.ticket');
    });

    Route::middleware(['auth'])->group(function () {
        Route::get('/perfil', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/perfil', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile/destroy', [App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');
        Route::get('/profile/confirm-delete/{id}', [App\Http\Controllers\ProfileController::class, 'confirmDeletion'])->name('profile.confirm_delete');
    });

    /*
    |--------------------------------------------------------------------------
    | 3. ZONA EMPLEADOS
    |--------------------------------------------------------------------------
    */
    Route::prefix('empleado')->middleware(['auth'])->group(function () {
        
        Route::middleware(['role:admin,cocinero,mesero,cajero,repartidor'])->group(function () {
            Route::get('/', [KitchenController::class, 'index'])->name('employee.panel');
        });

        Route::middleware(['role:admin,cocinero'])->group(function () {
            Route::get('/cocina', [KitchenController::class, 'kitchen'])->name('kitchen.live');
            Route::post('/orden/{order}/estado', [KitchenController::class, 'updateStatus'])->name('kitchen.update');
            Route::post('/orden/{id}/solicitar-cancelacion', [KitchenController::class, 'solicitarCancelacion'])->name('kitchen.cancel.request');
            
            Route::get('/stock', [App\Http\Controllers\Employee\StockController::class, 'index'])->name('stock.index');
            Route::post('/stock/{id}/toggle', [App\Http\Controllers\Employee\StockController::class, 'toggle'])->name('stock.toggle');
            Route::post('/stock/{id}/solicitar-cambio', [App\Http\Controllers\Employee\StockController::class, 'requestStatusChange'])->name('stock.request-change');
        });

        Route::middleware(['role:admin,mesero,cajero'])->group(function () {
            Route::get('/comandera', [PosController::class, 'index'])->name('employee.pos');
            Route::post('/comandera/store', [PosController::class, 'store'])->name('employee.pos.store');
            Route::get('/ticket/{id}', [PosController::class, 'printTicket'])->name('employee.pos.ticket');
        });

        Route::middleware(['role:admin,cajero'])->group(function () {
            Route::get('/caja', [App\Http\Controllers\Employee\OrderController::class, 'index'])->name('employee.orders.index');
            Route::post('/caja/{id}/pagar', [App\Http\Controllers\Employee\OrderController::class, 'markAsPaid'])->name('employee.orders.pay');
            Route::post('/caja/{id}/cancelar', [App\Http\Controllers\Employee\OrderController::class, 'cancel'])->name('employee.orders.cancel');
            Route::post('/caja/escanear', [App\Http\Controllers\Employee\OrderController::class, 'escanearCodigo'])->name('employee.orders.scan');
        });

        Route::middleware(['role:admin,repartidor'])->group(function () {
            Route::get('/repartidor/escanear', [App\Http\Controllers\Employee\OrderController::class, 'vistaRepartidor'])->name('delivery.scan');
            Route::post('/repartidor/escanear-codigo', [App\Http\Controllers\Employee\OrderController::class, 'escanearCodigo'])->name('repartidor.orders.scan');
            Route::get('/repartidor/api/nuevos-pedidos', [App\Http\Controllers\Employee\OrderController::class, 'checkNuevosPedidos'])->name('repartidor.api.nuevos');
            Route::post('/repartidor/orden/{id}/sos', [App\Http\Controllers\Employee\OrderController::class, 'reportarProblema'])->name('repartidor.orders.sos');
            Route::post('/repartidor/orden/{id}/sos-leido', [App\Http\Controllers\Employee\OrderController::class, 'marcarSOSLeido'])->name('repartidor.orders.sos-leido');
            Route::post('/repartidor/orden/{id}/tomar', [App\Http\Controllers\Employee\OrderController::class, 'tomarPedido'])->name('repartidor.orders.take');
            Route::post('/repartidor/orden/{id}/notificar-cliente', [App\Http\Controllers\Employee\OrderController::class, 'notificarCliente'])->name('repartidor.orders.notify');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | 4. ZONA ADMINISTRADOR
    |--------------------------------------------------------------------------
    */
    Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
        
        Route::get('/', function() { return redirect()->route('admin.dashboard'); });
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
        Route::get('/orden/{id}/detalles', [DashboardController::class, 'getOrderDetails'])->name('admin.orders.details');
        
        Route::get('/api/alertas', [DashboardController::class, 'checkAlerts'])->name('admin.alerts.check');
        Route::post('/orden/{id}/resolver-cancelacion', [DashboardController::class, 'resolverCancelacion'])->name('admin.alerts.resolve');
        Route::post('/api/alertas/stock/{id}/resolver', [DashboardController::class, 'resolverAlertaInsumo'])->name('admin.alerts.resolve-supply');
        Route::post('/orden/{id}/resolver-sos', [DashboardController::class, 'resolverSOS'])->name('admin.alerts.resolve-sos');

        Route::get('/ofertas', [OfferController::class, 'index'])->name('admin.offers.index');
        Route::post('/ofertas', [OfferController::class, 'store'])->name('admin.offers.store');
        Route::get('/ofertas/{id}/editar', [OfferController::class, 'edit'])->name('admin.offers.edit');
        Route::put('/ofertas/{id}', [OfferController::class, 'update'])->name('admin.offers.update');
        Route::delete('/ofertas/{id}', [OfferController::class, 'destroy'])->name('admin.offers.destroy');
        Route::post('/ofertas/{id}/toggle', [OfferController::class, 'toggle'])->name('admin.offers.toggle');

        Route::get('/pedidos', [AdminOrderController::class, 'index'])->name('admin.orders.index');
        Route::get('/pedidos/{id}', [AdminOrderController::class, 'show'])->name('admin.orders.show');
        Route::post('/pedidos/{id}/completar', [AdminOrderController::class, 'complete'])->name('admin.orders.complete');

        Route::patch('/productos/{id}/toggle-status', [App\Http\Controllers\Admin\ProductController::class, 'toggleStatus'])->name('admin.products.toggle-status');
        Route::post('/productos/{id}/restaurar', [App\Http\Controllers\Admin\ProductController::class, 'restore'])->name('admin.products.restore');
        Route::delete('/productos/{id}/force', [App\Http\Controllers\Admin\ProductController::class, 'forceDestroy'])->name('admin.products.force-delete');

        Route::resource('productos', AdminProductController::class)->names([
            'index' => 'admin.products.index',
            'create' => 'admin.products.create',
            'store' => 'admin.products.store',
            'edit' => 'admin.products.edit',
            'update' => 'admin.products.update',
            'destroy' => 'admin.products.destroy',
        ]);

        Route::get('/categorias', [\App\Http\Controllers\Admin\CategoriaController::class, 'index'])->name('admin.categorias.index');
        Route::post('/categorias', [\App\Http\Controllers\Admin\CategoriaController::class, 'store'])->name('admin.categorias.store');
        Route::put('/categorias/{id}', [\App\Http\Controllers\Admin\CategoriaController::class, 'update'])->name('admin.categorias.update');
        Route::delete('/categorias/{id}', [\App\Http\Controllers\Admin\CategoriaController::class, 'destroy'])->name('admin.categorias.destroy');

        Route::post('/usuarios/{id}/restaurar', [UserController::class, 'restore'])->name('admin.users.restore');
        Route::delete('/usuarios/{id}/force', [UserController::class, 'forceDestroy'])->name('admin.users.forceDestroy');
        Route::resource('usuarios', UserController::class)->names([
            'index'   => 'admin.users.index',
            'create'  => 'admin.users.create',
            'store'   => 'admin.users.store',
            'edit'    => 'admin.users.edit',
            'update'  => 'admin.users.update',
            'destroy' => 'admin.users.destroy', 
        ]);

        Route::get('/reporte/diario', [App\Http\Controllers\Admin\ReportController::class, 'dailyReport'])->name('admin.reports.daily');
        Route::get('/reporte/excel', [App\Http\Controllers\Admin\ReportController::class, 'exportExcel'])->name('admin.reports.excel');
    });

    Schedule::command('currency:update')->dailyAt('03:00');

}); // CIERRE DEL GRUPO DE IDIOMAS
=======

Route::get('/', function () {
    return view('welcome');
});
>>>>>>> 433b97d2585cb68ef73e2952cfbbe7259e4eeed7
