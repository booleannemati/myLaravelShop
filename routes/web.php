<?php

use App\Http\Controllers\Admin\AttributeController;
use App\Http\Controllers\Admin\BrandController;
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


Route::get('/admin-panel/dashboard', function () {
    return view('admin.dashboard');
})->name('dashboard');

Route::prefix('admin-panel/management')->name('admin.')->group(function(){

    Route::resource('brands', BrandController::class);
    Route::resource('attributes', AttributeController::class);
});
