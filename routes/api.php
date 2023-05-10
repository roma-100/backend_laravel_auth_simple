<?php

//use App\Models\Operation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\OperationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

// Public routes
//Route::resource('operations', OperationController::class);
/* ============ Start Public Routes ================= */
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);



Route::get('/operations', [OperationController::class, 'index']);


//Route::get('/operations/total', [OperationController::class, 'total']);
//Route::get('/operations/{id}', [OperationController::class, 'show']);
Route::get('/operations/search/{type}', [OperationController::class, 'search']);

/* Route::get('/operations', [OperationController::class, 'index']);
Route::get('/operations/{id}', [OperationController::class, 'show']);
Route::post('/operations', [OperationController::class, 'store']); */
/* ============ End Public Routes ================= */

/* ============ Start Private Routes ================= */
// Protected routes
Route::group(['middleware' => ['auth:sanctum']], function () {
    //
    Route::post('/user/list',[UserController::class, 'list']);

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::post('/operations', [OperationController::class, 'store']);
    Route::get('/operations/total', [OperationController::class, 'total']);
    Route::put('/operations/{id}', [OperationController::class, 'update']);
    Route::delete('/operations/{id}', [OperationController::class, 'destroy']);
});

/* Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
}); */

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/* ============ End Private Routes ================= */