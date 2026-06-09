<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DisciplineController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LogistiqueController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\EncadreurController;
use App\Models\User;

Route::middleware(['guest'])->get('/', function () {
    return view('index');
})->name('login');

Route::prefix('auth')
    ->middleware('guest')
    ->controller(AuthController::class)
    ->name('auth.')
    ->group(function(){

   Route::post('/login','login')->name('login');
   Route::post('logout','logout')->name('logout');

});

Route::prefix('admin')
    ->middleware(['auth','admin'])
    ->controller(AdminController::class)
    ->name('admin.')
    ->group(function(){

    Route::get('/','index')->name('index');

});


Route::prefix('discipline')
   ->middleware(['auth'])
   ->controller(DisciplineController::class)
   ->name('discipline.')
   ->group(function(){
       Route::get('/', 'index')->name('index');
});


Route::prefix('logistique')
    ->middleware(['auth'])
    ->controller(LogistiqueController::class)
    ->name('logistique.')
    ->group(function () {
        Route::get('/', 'index')->name('index');
    
});


Route::prefix('finance')
    ->middleware(['auth'])
    ->controller(FinanceController::class)
    ->name('finance.')
    ->group(function () {
        Route::get('/', 'index')->name('index');
   
});


Route::prefix('encadreur')
    ->middleware(['auth'])
    ->controller(EncadreurController::class)
    ->name('encadreur.')
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/store','store')->name('store');
});