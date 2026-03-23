<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\CollectorController;
use App\Http\Controllers\Api\SyncMonitoreoController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::middleware('auth.basic.legacy')->group(function () {
    Route::get('/parametros', [CollectorController::class, 'getParametros']);
    Route::post('/muestras', [CollectorController::class, 'getMuestras']);
    Route::get('/campanas', [CollectorController::class, 'getCampanas']);
    Route::get('/equipos', [CollectorController::class, 'getEquipos']);
    Route::get('/usuarios', [CollectorController::class, 'getUsuarios']);
    Route::get('/metodos', [CollectorController::class, 'getMetodos']);
    Route::get('/matriz_aguas', [CollectorController::class, 'getMatrizAguas']);
    Route::post('/sync/monitoreos', [SyncMonitoreoController::class, 'sync']);
});
