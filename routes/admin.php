<?php

use App\Http\Controllers\Admin\AdoptionRequestController;
use App\Http\Controllers\Admin\AnimalController;
use App\Http\Controllers\Admin\ContactMessageController;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\StatisticController;
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

// users
Route::apiResource('users' , UserController::class);

//animals
Route::get('animals', [AnimalController::class, 'index']);
Route::get('animals/{animal}', [AnimalController::class, 'show']);
Route::post('pending-animals/{animal}/change-animal-status', [AnimalController::class, 'change_animal_status']);

//adoption_requests
Route::get('adoption-requests', [AdoptionRequestController::class, 'index']);
Route::get('adoption-requests/{adoption_request}', [AdoptionRequestController::class, 'show']);
Route::post('pending-adoption-requests/{adoption_request}/change-adoption-request-status', [AdoptionRequestController::class, 'change_adoption_request_status']);

//contact-messages
Route::get('/contact-messages', [ContactMessageController::class, 'index']);

//statistics
Route::get('statistics', [StatisticController::class, 'index']);