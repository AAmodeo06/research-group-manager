<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProjectApiController;
use App\Http\Controllers\Api\PublicationApiController;
use App\Http\Controllers\Api\UserApiController;

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

//Endpoint pubblico per la diagnostica
Route::get('/health', function () {
    return response()->json(['status' => 'ok']);
});

//Route protette
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {

    // Resource API per progetti con nomi dedicati
    Route::apiResource('projects', ProjectApiController::class)->names([
        'index'   => 'api.projects.index',
        'store'   => 'api.projects.store',
        'show'    => 'api.projects.show',
        'update'  => 'api.projects.update',
        'destroy' => 'api.projects.destroy',
    ]);

    // Resource API per pubblicazioni con nomi dedicati
    Route::apiResource('publications', PublicationApiController::class)->names([
        'index'   => 'api.publications.index',
        'store'   => 'api.publications.store',
        'show'    => 'api.publications.show',
        'update'  => 'api.publications.update',
        'destroy' => 'api.publications.destroy',
    ]);

    // Export Utenti
    Route::get('/users', [UserApiController::class, 'index'])->name('api.users.index');

    // Profilo utente autenticato tramite token
    Route::get('/user', function (Request $request) {
        return $request->user();
    })->name('api.user');
});
