<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UnidadController;
use App\Http\Controllers\BitacoraUnidadController;
use App\Http\Controllers\BitacoraPaqueteriaController;
use App\Http\Controllers\BitacoraDieselController;
use App\Http\Controllers\AforoController;
use App\Http\Controllers\ColaboradorController;
use App\Http\Controllers\RutaController;
use App\Http\Controllers\ExcelController;

// Públicas
Route::post('/login', [AuthController::class, 'login']);

// Protegidas con Sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::get('/unidades', [UnidadController::class, 'index']);
    Route::get('/unidades/{numero}', [UnidadController::class, 'buscarPorNumero']);

    Route::get('/bitacora-unidades', [BitacoraUnidadController::class, 'index']);
    Route::post('/bitacora-unidades', [BitacoraUnidadController::class, 'store']);

    Route::get('/paqueteria', [BitacoraPaqueteriaController::class, 'index']);
    Route::post('/paqueteria', [BitacoraPaqueteriaController::class, 'store']);

    Route::get('/diesel', [BitacoraDieselController::class, 'index']);
    Route::post('/diesel', [BitacoraDieselController::class, 'store']);

    Route::post('/aforo', [AforoController::class, 'store']);
    Route::get('/aforo/reporte-semanal', [AforoController::class, 'reporteSemanal']);

    Route::get('/colaboradores', [ColaboradorController::class, 'index']);
    Route::post('/colaboradores', [ColaboradorController::class, 'store']);
    Route::get('/rutas', [RutaController::class, 'index']);

    Route::get('/excel/diesel', [ExcelController::class, 'exportarDiesel']);
    Route::get('/excel/aforo', [ExcelController::class, 'exportarAforo']);
});