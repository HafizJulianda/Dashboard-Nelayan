<?php

use Illuminate\Support\Facades\Route;


use App\Http\Controllers\HomeController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::post('/fakta/store', [HomeController::class, 'store'])->name('fakta.store');
Route::post('/fakta/store', [HomeController::class, 'store'])->name('fakta.store');
