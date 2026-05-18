<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/comunidades', [App\Http\Controllers\Api\ComunidadController::class, 'store']);
    Route::post('/comunidades/join', [App\Http\Controllers\Api\ComunidadController::class, 'join']);
    Route::put('/comunidades/{comunidad}/miembros/{user}', [App\Http\Controllers\Api\ComunidadController::class, 'gestionarMiembro']);
    Route::get('/comunidades/{id}/ranking', [App\Http\Controllers\Api\ComunidadController::class, 'ranking']);
    Route::delete('/comunidades/{comunidad}/miembros/{user}', [App\Http\Controllers\Api\ComunidadController::class, 'eliminarMiembro']);

    Route::get('/partidos', [App\Http\Controllers\Api\PartidoController::class, 'index']);
    Route::post('/partidos', [App\Http\Controllers\Api\PartidoController::class, 'store']);
    Route::put('/partidos/{id}/resultado', [App\Http\Controllers\Api\PartidoController::class, 'updateResultado']);
    Route::post('/partidos/importar', [App\Http\Controllers\Api\PartidoController::class, 'importar']);

    Route::post('/predicciones', [App\Http\Controllers\Api\PrediccionController::class, 'store']);
    Route::get('/predicciones', [App\Http\Controllers\Api\PrediccionController::class, 'index']);

    Route::get('/user', function (Request $request) {
        return response()->json([
            'success' => true,
            'user' => $request->user()
        ]);
});
});