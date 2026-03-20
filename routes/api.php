<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\CollectorController;

Route::middleware('auth.basic.legacy')->group(function () {
    Route::get('/parametros', [CollectorController::class, 'getParametros']);
    Route::post('/muestras', [CollectorController::class, 'getMuestras']);
    Route::get('/campanas', [CollectorController::class, 'getCampanas']);
    Route::get('/equipos', [CollectorController::class, 'getEquipos']);
    Route::get('/usuarios', [CollectorController::class, 'getUsuarios']);
    Route::get('/metodos', [CollectorController::class, 'getMetodos']);
    Route::get('/matriz_aguas', [CollectorController::class, 'getMatrizAguas']);
});
