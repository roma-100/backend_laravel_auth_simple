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
Route::get('/mk_list_complex/{user_id}', [MkListController::class, 'mk_list_complex']); //+leaders+steps
Route::post('/mk_list_add_mk', [MkListController::class, 'mk_list_add_mk']);
Route::delete('/mk_list_destroy/{mk_list_id}', [MkListController::class, 'destroy']);
Route::delete('/mk_list_delete_all_steps/{mk_list_id}', [MkListController::class, 'mk_list_delete_all_steps']);
Route::post('/mk_list_edit_mk', [MkListController::class, 'mk_list_edit_mk']);
Route::get('/mk_list_import_steps/{mk_list_id_tx}/{mk_list_id_rx}', [MkStepController::class, 'mk_list_import_steps']);
Route::get('/mk_list_join_users_steps/{mk_list_id}', [MkStepController::class, 'mk_list_join_users_steps']);
Route::get('/mk_list_steplers/{mk_list_id}', [MkListController::class, 'mk_list_steplers']);
Route::get('/mk_list_xd', [MkListController::class, 'mk_list_xd']);

Route::get('/mk_step_show/{mk_list_id}', [MkStepController::class, 'mk_step_show']);
Route::post('/mk_step_edit', [MkStepController::class, 'edit']);
Route::post('/mk_step_add', [MkStepController::class, 'store']);
Route::post('/mk_step_delete', [MkStepController::class, 'destroy']);

Route::get('/mk_team/{mk_list_id}', [MkTeamController::class, 'show']);
Route::post('/mk_team/store', [MkTeamController::class, 'store']);
Route::delete('/mk_team/{mk_list_id}', [MkTeamController::class, 'destroy']);
Route::post('/mk_user_steps_move', [UserStepActionController::class, 'store_mk_user_steps_move']);
Route::get('/mk_user_is_initial/{mk_list_id}/{user_id}', [UserStepActionController::class, 'mk_user_is_initial']);
Route::post('/mk_user_state_steps_initial', [UserStepActionController::class, 'mk_user_state_steps_initial']);
//Route::get('/mk_user_state_steps_get_smart/{mk_list_id}/{leader_id}', [UserStepActionController::class, 'mk_user_state_steps_get_smart']);
Route::get('/mk_user_state_steps_get_smart/{mk_list_id}/{leader_id}/{caller_id}', [UserStepActionController::class, 'mk_user_state_steps_get_smart']);
Route::get('/mk_user_state_steps_get/{mk_list_id}/{user_id}', [UserStepActionController::class, 'mk_user_state_steps_get']);
Route::post('/mk_user_state_steps_update', [UserStepActionController::class, 'mk_user_state_steps_update']);
Route::post('/mk_user_steps_failed', [UserStepActionController::class, 'mk_user_steps_failed']);

//for testing
Route::delete('/mk_user_state_steps_destroy/{mk_list_id}/{user_id}', [UserStepActionController::class, 'mk_user_state_steps_destroy']);

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
