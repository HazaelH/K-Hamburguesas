<?php

use Illuminate\Support\Facades\Route;

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
use App\Http\Controllers\Admin\OfferController; // <--- AGREGADO
use App\Http\Controllers\Employee\PosController;

/*
|--------------------------------------------------------------------------
| 1. AUTENTICACIÓN
|--------------------------------------------------------------------------
*/
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/registro', [AuthController::class, 'showRegister'])->name('register');
Route::post('/registro', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| 2. APIS PÚBLICAS (AJAX/Fetch)
|--------------------------------------------------------------------------
*/
Route::get('/api/producto/{id}', [ClientController::class, 'getProductDetails'])->name('api.product.details');
Route::get('/api/categoria/{categoria}', [ClientController::class, 'filterProducts'])->name('api.products.filter');
Route::get('/api/zip-codes/{cp}', [ZipCodeController::class, 'show']);
Route::get('/api/colonias/{nombre}', [ZipCodeController::class, 'searchByColonia']);

/*
|--------------------------------------------------------------------------
| 3. ZONA PÚBLICA / CLIENTE
|--------------------------------------------------------------------------
*/
Route::get('/', [ClientController::class, 'home'])->name('home');      
Route::get('/menu', [ClientController::class, 'menu'])->name('menu');  

// --- NUEVA RUTA DE OFERTAS ---
Route::get('/ofertas', [ClientController::class, 'offers'])->name('offers.index');

// GESTIÓN DEL CARRITO
Route::get('/carrito', [ClientController::class, 'viewCart'])->name('cart.index');
Route::post('/carrito/agregar', [ClientController::class, 'addToCart'])->name('cart.add');
Route::delete('/carrito/eliminar/{row_id}', [ClientController::class, 'removeFromCart'])->name('cart.remove');

// ZONA SEGURA CLIENTE
Route::middleware(['auth', 'role:cliente'])->group(function () {
    Route::get('/pagar', [ClientController::class, 'checkout'])->name('checkout');
    Route::post('/pagar', [ClientController::class, 'processPayment'])->name('checkout.process');
    Route::get('/orden-confirmada/{order}', [ClientController::class, 'orderSuccess'])->name('order.success');
    Route::get('/ticket/{order}', [ClientController::class, 'ticket'])->name('ticket');
    Route::get('/mi-ticket/{id}', [App\Http\Controllers\ClientController::class, 'ticket'])->name('client.ticket');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/perfil', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/perfil', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
});

/*
|--------------------------------------------------------------------------
| 4. ZONA COCINA / EMPLEADO
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:empleado'])->prefix('empleado')->group(function () {
    Route::get('/', [KitchenController::class, 'index'])->name('employee.panel');
    Route::get('/cocina', [KitchenController::class, 'kitchen'])->name('kitchen.live');
    Route::post('/orden/{order}/estado', [KitchenController::class, 'updateStatus'])->name('kitchen.update');
    Route::get('/stock', [App\Http\Controllers\Employee\StockController::class, 'index'])->name('stock.index');
    Route::post('/stock/{id}/toggle', [App\Http\Controllers\Employee\StockController::class, 'toggle'])->name('stock.toggle');
    Route::get('/comandera', [PosController::class, 'index'])->name('employee.pos');
    Route::post('/comandera/store', [PosController::class, 'store'])->name('employee.pos.store');
    Route::get('/ticket/{id}', [PosController::class, 'printTicket'])->name('employee.pos.ticket');
    // CAJA
    Route::get('/caja', [App\Http\Controllers\Employee\OrderController::class, 'index'])->name('employee.orders.index');
    Route::post('/caja/{id}/pagar', [App\Http\Controllers\Employee\OrderController::class, 'markAsPaid'])->name('employee.orders.pay');
    
    // NUEVA RUTA: CANCELAR
    Route::post('/caja/{id}/cancelar', [App\Http\Controllers\Employee\OrderController::class, 'cancel'])->name('employee.orders.cancel');
    Route::get('/repartidor/escanear', [App\Http\Controllers\Employee\OrderController::class, 'vistaRepartidor'])->name('delivery.scan');
    Route::post('/empleado/caja/escanear', [App\Http\Controllers\Employee\OrderController::class, 'escanearCodigo'])->name('employee.orders.scan');
});

/*
|--------------------------------------------------------------------------
| 5. ZONA ADMINISTRADOR
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    
    // A. Dashboard
    Route::get('/', function() { return redirect()->route('admin.dashboard'); });
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/orden/{id}/detalles', [DashboardController::class, 'getOrderDetails'])->name('admin.orders.details');

    // B. Gestión de Ofertas (Admin)
    Route::get('/ofertas', [OfferController::class, 'index'])->name('admin.offers.index');
    Route::post('/ofertas', [OfferController::class, 'store'])->name('admin.offers.store');
    Route::delete('/ofertas/{id}', [OfferController::class, 'destroy'])->name('admin.offers.destroy');
    Route::post('/ofertas/{id}/toggle', [OfferController::class, 'toggle'])->name('admin.offers.toggle');

    // C. Gestión de Pedidos
    Route::get('/pedidos', [AdminOrderController::class, 'index'])->name('admin.orders.index');
    Route::get('/pedidos/{id}', [AdminOrderController::class, 'show'])->name('admin.orders.show');
    Route::post('/pedidos/{id}/completar', [AdminOrderController::class, 'complete'])->name('admin.orders.complete');

    // D. Gestión de Productos (CRUD)
    Route::resource('productos', AdminProductController::class)->names([
        'index' => 'admin.products.index',
        'create' => 'admin.products.create',
        'store' => 'admin.products.store',
        'edit' => 'admin.products.edit',
        'update' => 'admin.products.update',
        'destroy' => 'admin.products.destroy',
    ]);

    // E. Gestión de Usuarios (CORREGIDO: Ahora está dentro del grupo admin)
    Route::resource('usuarios', UserController::class)->names([
        'index'   => 'admin.users.index',
        'create'  => 'admin.users.create',
        'store'   => 'admin.users.store',
        'edit'    => 'admin.users.edit',
        'update'  => 'admin.users.update',
        'destroy' => 'admin.users.destroy',
    ]);

    // F. Reportes
    Route::get('/reporte/diario', [App\Http\Controllers\Admin\ReportController::class, 'dailyReport'])->name('admin.reports.daily');
    Route::get('/reporte/excel', [App\Http\Controllers\Admin\ReportController::class, 'exportExcel'])->name('admin.reports.excel');

});