<?php

use App\Http\Controllers\Api\ProductoController;
use App\Http\Controllers\Api\CategoriaController;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

// PRODUCTOS
Route::get('/productos', [ProductoController::class, 'index']);
Route::get('/productos/{producto}', [ProductoController::class, 'show']);

// CATEGORÍAS PÚBLICAS
Route::get('/categorias', [CategoriaController::class, 'index']);
Route::get('/categorias/{categoria}', [CategoriaController::class, 'show']);
Route::get(
    '/categorias/{categoria}/productos',
    [CategoriaController::class, 'productos']
);

// LOGIN
Route::post('/login', [AuthController::class, 'login']);

// RUTAS PROTEGIDAS
Route::middleware('auth:sanctum')->group(function () {

      // Productos
    Route::post('/productos', [ProductoController::class, 'store']);
    Route::put('/productos/{producto}', [ProductoController::class, 'update']);

    Route::delete('/productos/{producto}', [ProductoController::class, 'destroy']);


    // AUTENTICACIÓN
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // CATEGORÍAS - ADMINISTRACIÓN
    Route::post('/categorias', [CategoriaController::class, 'store']);
    Route::put('/categorias/{categoria}', [CategoriaController::class, 'update']);
    Route::delete('/categorias/{categoria}', [CategoriaController::class, 'destroy']);
});

