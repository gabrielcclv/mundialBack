<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\ViewController;
use App\Http\Controllers\Api\PartidoController;

// Pantalla de bienvenida por defecto
Route::get('/', function () { return view('welcome'); });

// Rutas de invitados (Login / Registro)
Route::middleware('guest')->group(function () {
    Route::get('/login', function () { return view('auth.login'); })->name('login');
    Route::post('/login', [ViewController::class, 'login']);
    Route::get('/register', function () { return view('auth.register'); });
    Route::post('/register', [ViewController::class, 'register']);
});

// Rutas protegidas de la aplicación por sesión web
Route::middleware('auth')->group(function () {
    Route::post('/logout', [ViewController::class, 'logout']);
    
    // Panel de control general de predicciones
    Route::get('/dashboard', [ViewController::class, 'dashboard']);
    Route::post('/predicciones', [ViewController::class, 'storePrediccion']);

    // Gestión de ligas competitivas
    Route::get('/comunidades', [ViewController::class, 'comunidadesIndex']);
    Route::post('/comunidades', [ViewController::class, 'storeComunidad']);
    Route::post('/comunidades/join', [ViewController::class, 'joinComunidad']);
    Route::get('/comunidades/{id}', [ViewController::class, 'comunidadesShow']);
    Route::put('/comunidades/{comunidad}/miembros/{user}', [ViewController::class, 'gestionarMiembro']);

    // Zonas de Gestión del Administrador
    Route::get('/admin/dashboard', [ViewController::class, 'adminDashboard']);
    Route::post('/partidos', [PartidoController::class, 'store']);
    Route::put('/partidos/{id}/resultado', [PartidoController::class, 'updateResultado']);
});