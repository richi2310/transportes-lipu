<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UnidadController;
use App\Http\Controllers\BitacoraUnidadController;
use App\Http\Controllers\BitacoraPaqueteriaController;

// Públicas
Route::post('/login', [AuthController::class, 'login']);

// Protegidas con Sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::get('/unidades/{numero}', [UnidadController::class, 'buscarPorNumero']);

    Route::get('/bitacora-unidades', [BitacoraUnidadController::class, 'index']);
    Route::post('/bitacora-unidades', [BitacoraUnidadController::class, 'store']);

    Route::get('/paqueteria', [BitacoraPaqueteriaController::class, 'index']);
    Route::post('/paqueteria', [BitacoraPaqueteriaController::class, 'store']);
});