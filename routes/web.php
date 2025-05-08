<?php

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


Route::get('images/{id}.png', [\App\Http\Controllers\MapController::class, 'imageShowId']);
Route::get('images/date/{year}/{month}.png', [\App\Http\Controllers\MapController::class, 'imageShowDate']);
Route::get('images/date/{year}/{month}/{day}.png', [\App\Http\Controllers\MapController::class, 'imageShowDate']);
