<?php

use App\Http\Controllers\AnimalController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BreedController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ContactMessageController;
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
use Illuminate\Support\Facades\Storage;

Route::get('/list-files', function () {
    $files = Storage::disk('public')->allFiles('animals');
    return response()->json($files);
});
//Auth
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::post('logout', [AuthController::class, 'logout']);

Route::put('user', [AuthController::class, 'edit_profile']);
Route::get('user', [AuthController::class, 'get_profile']);

//animals
Route::get('animals', [AnimalController::class, 'index']);
Route::post('animals', [AnimalController::class, 'store']);
Route::get('animals/{animal}', [AnimalController::class, 'show']);
Route::post('animals/{animal}/adoption-request', [AnimalController::class, 'store_adoption_request']);

//categories
Route::get('categories', [CategoryController::class, 'index']);

//breeds
Route::get('breeds', [BreedController::class, 'index']);

//contact-messages
Route::post('/contact-messages', [ContactMessageController::class, 'store']);

//dashboard-login
Route::post('dashboard-login', [AuthController::class, 'dashboard_login']);
