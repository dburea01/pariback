<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\SportController;
use App\Http\Controllers\CompetitionController;

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

Route::prefix('v1')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('validate-registration', [AuthController::class, 'validateRegistration']);
    Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('reset-password', [AuthController::class, 'resetPassword']);

    Route::get('countries', [CountryController::class, 'index']);
    Route::apiResource('sports', SportController::class)->only(['index', 'show']);
    Route::apiResource('competitions', CompetitionController::class)->only(['index', 'show'])->whereUuid('competition');
});

Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);

    Route::apiResource('countries', CountryController::class)->except('index');
    Route::apiResource('sports', SportController::class)->only(['store', 'update', 'destroy']);
    Route::apiResource('competitions', CompetitionController::class)->only(['store', 'update', 'destroy'])->whereUuid('competition');
});
