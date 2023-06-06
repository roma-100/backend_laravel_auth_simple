<?php

//use App\Models\Operation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\OperationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MkListController;
use App\Http\Controllers\MkStepController;
use App\Http\Controllers\MkTeamController;
use App\Http\Controllers\UserStepActionController;

// Public routes
//Route::resource('operations', OperationController::class);
/* ============ Start Public Routes ================= */
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/edit_user/{id}', [AuthController::class, 'edit_user']);
Route::get('/operations', [OperationController::class, 'index']);
Route::get('/mk_list', [MkListController::class, 'index']);
Route::get('/mk_step_show/{id}', [MkStepController::class, 'mk_step_show']);
Route::post('/mk_step_edit', [MkStepController::class, 'edit']);
Route::post('/mk_step_add', [MkStepController::class, 'store']);
Route::post('/mk_step_delete', [MkStepController::class, 'destroy']);
Route::get('/mk_team/{mk_list_id}', [MkTeamController::class, 'show']);
Route::post('/mk_team/store', [MkTeamController::class, 'store']);
Route::delete('/mk_team/{mk_list_id}', [MkTeamController::class, 'destroy']);
Route::post('/mk_user_steps_move', [UserStepActionController::class, 'store_mk_user_steps_move']);

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
    Route::post('/user/list_active_users',[UserController::class, 'list_active_users']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/delete_user/{id}', [AuthController::class, 'delete_user']);
    
    //Route::get('/mk_list', [MkListController::class, 'index']);
    
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
