<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::prefix('v1')->group(function() {

    Route::post('/login', [\App\Http\Controllers\Auth\UserController::class, 'login'])->name('login');

    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('logout', [\App\Http\Controllers\Auth\UserController::class, 'logout'])->name('logout');

        //Rutas de Usuario
        Route::prefix('users')->group(function (){
            Route::get('/', [\App\Http\Controllers\Auth\UserController::class, 'index'])->name('users.index');
            Route::get('profile', [\App\Http\Controllers\Auth\UserController::class, 'show'])->name('users.profile');
        });

        //Rutas de Tareas
        Route::prefix('tasks')->group(function (){
        Route::get('/', [\App\Http\Controllers\Auth\TaskController::class, 'index'])->name('tasks.index');
        Route::post('/', [\App\Http\Controllers\Auth\TaskController::class, 'store'])->name('tasks.store');
        Route::get('/{task}', [\App\Http\Controllers\Auth\TaskController::class, 'show'])->name('tasks.show');
        Route::put('/{task}', [\App\Http\Controllers\Auth\TaskController::class, 'update'])->name('tasks.update');
        Route::delete('/{task}', [\App\Http\Controllers\Auth\TaskController::class, 'destroy'])->name('tasks.destroy');
        Route::put('/toggle-complete/{task}', [\App\Http\Controllers\Auth\TaskController::class, 'toggleComplete'])->name('tasks.toggle');
        Route::get('/task/tag-types', [\App\Http\Controllers\Auth\TaskController::class, 'getTagTypes'])->name('task.tag-types');
        Route::get('/task/status-types', [\App\Http\Controllers\Auth\TaskController::class, 'getStatusTypes'])->name('task.status-types');
        });
  });
});
