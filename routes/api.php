<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('earth-quakes/years', [\App\Http\Controllers\EarthQuakesController::class, 'getYears']);
Route::get('earth-quakes/months', [\App\Http\Controllers\EarthQuakesController::class, 'getMonths']);
Route::get('earth-quakes/days', [\App\Http\Controllers\EarthQuakesController::class, 'getDays']);



Route::apiResource('earth-quakes', \App\Http\Controllers\EarthQuakesController::class)
    ->only('index', 'show');
