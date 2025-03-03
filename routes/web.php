<?php

use App\Http\Controllers\AppController;
use App\Http\Controllers\EntryController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\OutputController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;




/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::group(['middleware' => ['guest']], function () {
    Route::get('/', [AppController::class, 'login'])->name('login');
    Route::post('/login', [UserController::class, 'login'])->name('login-post');

});


Route::middleware(['auth'])->prefix('home')->group(function () 
{
    Route::get('/', [AppController::class, 'home'])->name('home');
    Route::get('/perfil', [UserController::class, 'profile'])->name('profile');
    Route::put('/perfil', [UserController::class, 'updateProfile'])->name('profile.update');

    Route::get('/logout', [UserController::class, 'logout'])->name('logout'); 
    
    // ------------------------------- ENTRADAS 
    Route::get('/entradas',[EntryController::class, 'index'])->middleware('role_or_permission:admin|read-entries')->name('entries');
    Route::get('/entradas/crear',[EntryController::class, 'create'])->middleware('role_or_permission:admin|create-entries')->name('entries.create');
    Route::get('/entradas/{entry}',[EntryController::class,'show'])->middleware('role_or_permission:admin|read-entries')->name('entries.show');
    Route::post('/entradas',[EntryController::class, 'store'])->middleware('role_or_permission:admin|create-entries')->name('entries.store');
    Route::delete('/entradas/{entry}',[EntryController::class, 'destroy'])->middleware('role_or_permission:admin|delete-entries')->name('entries.delete');
    Route::get('/entradas/editar/{entry}',[EntryController::class, 'edit'])->middleware('role_or_permission:admin|update-entries')->name('entries.edit');
    Route::put('/entradas/{entry}',[EntryController::class, 'update'])->middleware('role_or_permission:admin|update-entries')->name('entries.update');


    // ------------------------------- SALIDAS 
    Route::get('/salidas',[OutputController::class, 'index'])->middleware('role_or_permission:admin|read-outputs')->name('outputs');
    Route::get('/salidas/crear',[OutputController::class, 'create'])->middleware('role_or_permission:admin|create-outputs')->name('outputs.create');
    Route::get('/salidas/{output}',[OutputController::class,'show'])->middleware('role_or_permission:admin|read-outputs')->name('outputs.show');
    Route::post('/salidas/{json?}',[OutputController::class, 'store'])->middleware('role_or_permission:admin|create-outputs|create-patients')->name('outputs.store');
    Route::delete('/salidas/{output}',[OutputController::class, 'destroy'])->middleware('role_or_permission:admin|delete-outputs')->name('outputs.delete');
    Route::get('/salidas/editar/{output}',[OutputController::class, 'edit'])->middleware('role_or_permission:admin|update-outputs')->name('outputs.edit');
    Route::put('/salidas/{output}',[OutputController::class, 'update'])->middleware('role_or_permission:admin|update-outputs')->name('outputs.update');

    Route::middleware(['permission:read-inventories'])->group(function () {

        Route::get('/inventario',[InventoryController::class,'index'])->name('inventory');
        Route::get('/inventario/{inventory}',[InventoryController::class,'show'])->name('inventory.show');
        Route::get('/inventario/search/{search}',[InventoryController::class,'search']);
        Route::get('/productos/search/{search}',[ProductController::class,'search']);
        Route::post('/productos',[ProductController::class, 'store']);


    });
    
    // ------------------------------- Pacientes 
    Route::get('/pacientes',[PatientController::class, 'index'])->middleware('role_or_permission:admin|read-patients')->name('patients');
    Route::get('/pacientes/crear',[PatientController::class, 'create'])->middleware('role_or_permission:admin|create-patients')->name('patients.create');
    Route::get('/pacientes/search/{search}',[PatientController::class, 'search'])->middleware('role_or_permission:admin|read-patients')->name('patients.search');
    Route::post('/pacientes',[PatientController::class, 'store'])->middleware('role_or_permission:admin|create-patients')->name('patients.store');
    Route::post('/pacientes/casos',[PatientController::class, 'storeCase'])->middleware('role_or_permission:admin|create-patients')->name('patients.case.store');
    Route::delete('/pacientes/casos/{case}',[PatientController::class, 'destroy'])->middleware('role_or_permission:admin|delete-patients')->name('patients.case.delete');


    // ------------------------------- Patologias
    Route::get('/patologias/search/{search}',[PatientController::class, 'searchPathology'])->middleware('role_or_permission:admin|create-patients')->name('patients.pathology.search');
    Route::post('/patologias',[PatientController::class, 'createPathology'])->middleware('role_or_permission:admin|create-patients')->name('patients.pathology.store');
     





});
