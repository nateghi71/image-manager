<?php

use App\Http\Controllers\imageController;
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

Route::get('/' , [imageController::class , 'index']);
Route::post('/store' , [imageController::class , 'store'])->name('image.store');
Route::post('/update' , [imageController::class , 'update'])->name('image.update');
Route::post('/destroy/{id}' , [imageController::class , 'destroy'])->name('image.destroy');
