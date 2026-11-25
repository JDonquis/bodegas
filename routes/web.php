<?php

use App\Http\Controllers\AppController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DebtController;
use App\Http\Controllers\EntryController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\OutputController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TenantUserController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['guest']], function () {
    Route::get('/', [AppController::class, 'login'])->name('login');
    Route::post('/login', [UserController::class, 'login'])->name('login-post');

    Route::get('/olvidar-contraseña', [UserController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/validar-master-password', [UserController::class, 'validateMasterPassword'])->name('password.validate');
    Route::get('/restablecer-contraseña/{ci}', [UserController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/actualizar-contraseña', [UserController::class, 'updatePassword'])->name('password.update');
});

Route::middleware(['auth'])->prefix('home')->group(function () {
    Route::get('/', [AppController::class, 'home'])->name('home');
    Route::get('/statistics', [AppController::class, 'statistics'])->name('statistics');
    Route::get('/perfil', [UserController::class, 'profile'])->name('profile');
    Route::put('/perfil', [UserController::class, 'updateProfile'])->name('profile.update');
    Route::put('/perfil/password', [UserController::class, 'changePassword'])->name('profile.password');

    Route::get('/logout', [UserController::class, 'logout'])->name('logout');

    // ------------------------------- PRODUCTOS
    Route::get('/productos', [ProductController::class, 'index'])->middleware('role_or_permission:admin')->name('products');
    Route::get('/productos/crear', [ProductController::class, 'create'])->middleware('role_or_permission:admin')->name('products.create');
    Route::get('/productos/exportar', [ProductController::class, 'export'])->middleware('role_or_permission:admin')->name('products.export');
    Route::get('/productos/{product}', [ProductController::class, 'show'])->middleware('role_or_permission:admin')->name('products.show');
    Route::post('/productos', [ProductController::class, 'store'])->middleware('role_or_permission:admin')->name('products.store');
    Route::delete('/productos/{product}', [ProductController::class, 'destroy'])->middleware('role_or_permission:admin')->name('products.delete');
    Route::get('/productos/editar/{product}', [ProductController::class, 'edit'])->middleware('role_or_permission:admin')->name('products.edit');
    Route::put('/productos/{product}', [ProductController::class, 'update'])->middleware('role_or_permission:admin')->name('products.update');

    // ------------------------------- ENTRADAS
    Route::get('/entradas', [EntryController::class, 'index'])->middleware('role_or_permission:admin|read-entries')->name('entries');
    Route::get('/entradas/crear', [EntryController::class, 'create'])->middleware('role_or_permission:admin|create-entries')->name('entries.create');
    Route::get('/entradas/search', [EntryController::class, 'search'])->middleware('role_or_permission:admin|read-entries')->name('entries.search');
    Route::get('/entradas/{entry}', [EntryController::class, 'show'])->middleware('role_or_permission:admin|read-entries')->name('entries.show');
    Route::post('/entradas', [EntryController::class, 'store'])->middleware('role_or_permission:admin|create-entries')->name('entries.store');
    Route::delete('/entradas/{entry}', [EntryController::class, 'destroy'])->middleware('role_or_permission:admin|delete-entries')->name('entries.delete');
    Route::get('/entradas/editar/{entry}', [EntryController::class, 'edit'])->middleware('role_or_permission:admin|update-entries')->name('entries.edit');
    Route::put('/entradas/{entry}', [EntryController::class, 'update'])->middleware('role_or_permission:admin|update-entries')->name('entries.update');

    // ------------------------------- SALIDAS
    Route::get('/salidas', [OutputController::class, 'index'])->middleware('role_or_permission:admin|read-outputs')->name('outputs');
    Route::get('/salidas/crear', [OutputController::class, 'create'])->middleware('role_or_permission:admin|create-outputs')->name('outputs.create');
    Route::get('/salidas/search', [OutputController::class, 'search'])->middleware('role_or_permission:admin|read-outputs')->name('outputs.search');
    Route::get('/salidas/{output}', [OutputController::class, 'show'])->middleware('role_or_permission:admin|read-outputs')->name('outputs.show');
    Route::post('/salidas/{json?}', [OutputController::class, 'store'])->middleware('role_or_permission:admin|create-outputs')->name('outputs.store');
    Route::delete('/salidas/{output}', [OutputController::class, 'destroy'])->middleware('role_or_permission:admin|delete-outputs')->name('outputs.delete');
    Route::get('/salidas/editar/{output}', [OutputController::class, 'edit'])->middleware('role_or_permission:admin|update-outputs')->name('outputs.edit');
    Route::put('/salidas/{output}', [OutputController::class, 'update'])->middleware('role_or_permission:admin|update-outputs')->name('outputs.update');

    Route::middleware(['permission:read-inventories'])->group(function () {
        Route::get('/inventario', [InventoryController::class, 'index'])->name('inventory');
        Route::get('/inventario/{inventory}', [InventoryController::class, 'show'])->name('inventory.show');
        Route::get('/inventario/search/{search}', [InventoryController::class, 'search']);
        Route::get('/inventario/search-lots/{search}', [InventoryController::class, 'searchLots']);
        Route::get('/productos/search/{search}', [ProductController::class, 'search']);
    });

    // ------------------------------- CLIENTS
    Route::get('/clientes', [ClientController::class, 'index'])->middleware('role_or_permission:admin')->name('clients');
    Route::get('/clientes/crear', [ClientController::class, 'create'])->middleware('role_or_permission:admin')->name('clients.create');
    Route::get('/clientes/{client}/historial', [ClientController::class, 'history'])->middleware('role_or_permission:admin')->name('clients.history');
    Route::post('/clientes', [ClientController::class, 'store'])->middleware('role_or_permission:admin')->name('clients.store');
    Route::get('/clientes/editar/{client}', [ClientController::class, 'edit'])->middleware('role_or_permission:admin')->name('clients.edit');
    Route::put('/clientes/{client}', [ClientController::class, 'update'])->middleware('role_or_permission:admin')->name('clients.update');
    Route::delete('/clientes/{client}', [ClientController::class, 'destroy'])->middleware('role_or_permission:admin')->name('clients.delete');

    // ------------------------------- DEUDAS
    Route::get('/deudas', [DebtController::class, 'index'])->middleware('role_or_permission:admin')->name('debts');
    Route::post('/deudas', [DebtController::class, 'store'])->middleware('role_or_permission:admin')->name('debts.store');
    Route::post('/deudas/pagos', [DebtController::class, 'storePayment'])->middleware('role_or_permission:admin')->name('debts.payments.store');

    // ------------------------------- GASTOS
    Route::get('/gastos', [ExpenseController::class, 'index'])->middleware('role_or_permission:admin')->name('expenses');
    Route::get('/gastos/crear', [ExpenseController::class, 'create'])->middleware('role_or_permission:admin')->name('expenses.create');
    Route::post('/gastos', [ExpenseController::class, 'store'])->middleware('role_or_permission:admin')->name('expenses.store');
    Route::delete('/gastos/{expense}', [ExpenseController::class, 'destroy'])->middleware('role_or_permission:admin')->name('expenses.delete');

    // ------------------------------- USUARIOS
    Route::get('/usuarios', [TenantUserController::class, 'index'])->middleware('role_or_permission:admin')->name('users');
    Route::get('/usuarios/crear', [TenantUserController::class, 'create'])->middleware('role_or_permission:admin')->name('users.create');
    Route::post('/usuarios', [TenantUserController::class, 'store'])->middleware('role_or_permission:admin')->name('users.store');
    Route::get('/usuarios/editar/{user}', [TenantUserController::class, 'edit'])->middleware('role_or_permission:admin')->name('users.edit');
    Route::put('/usuarios/{user}', [TenantUserController::class, 'update'])->middleware('role_or_permission:admin')->name('users.update');
    Route::delete('/usuarios/{user}', [TenantUserController::class, 'destroy'])->middleware('role_or_permission:admin')->name('users.delete');

    Route::get('/invoice/{outputID}', [InvoiceController::class, 'index'])->middleware('role_or_permission:admin')->name('invoice');
});

Route::prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('/login', [\App\Http\Controllers\SuperAdminController::class, 'showLogin'])->name('login');
    Route::post('/login', [\App\Http\Controllers\SuperAdminController::class, 'login'])->name('login.post');

    Route::middleware('auth:superadmin')->group(function () {
        Route::get('/', [\App\Http\Controllers\SuperAdminController::class, 'dashboard'])->name('dashboard');
        Route::post('/tenants', [\App\Http\Controllers\SuperAdminController::class, 'storeTenant'])->name('tenants.store');
        Route::post('/tenants/{tenant}/toggle', [\App\Http\Controllers\SuperAdminController::class, 'toggleTenant'])->name('tenants.toggle');
        Route::get('/billing', [\App\Http\Controllers\SuperAdminController::class, 'billing'])->name('billing');
        Route::post('/billing/payments', [\App\Http\Controllers\SuperAdminController::class, 'storePayment'])->name('billing.payments.store');
        Route::post('/credentials', [\App\Http\Controllers\SuperAdminController::class, 'updateCredentials'])->name('credentials.update');
        Route::post('/logout', [\App\Http\Controllers\SuperAdminController::class, 'logout'])->name('logout');
    });
});
