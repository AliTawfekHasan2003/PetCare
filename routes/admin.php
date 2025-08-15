<?php

use App\Http\Controllers\Admin\AnimalController;
use App\Http\Controllers\Admin\ContactMessageController;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

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

// roles and permissions
Route::get('/permissions', [PermissionController::class, 'get_all_permissions']);
Route::get('/permissions/me', [PermissionController::class, 'my_permissions']);
Route::get('/roles/{role}/permissions', [PermissionController::class, 'get_permissions']);
Route::post('/roles/{role}/permissions', [PermissionController::class, 'set_permissions']);
Route::apiResource('roles', RoleController::class);

// users
Route::apiResource('users' , UserController::class);

//animals
Route::get('pending-animals', [AnimalController::class, 'pending_animals']);
Route::post('pending-animals/{animal}/change-animal-status', [AnimalController::class, 'change_animal_status']);
Route::get('adoption-requests', [AnimalController::class, 'adoption_requests']);

//contact-messages
Route::get('/contact-messages', [ContactMessageController::class, 'index']);