<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BetController;
use App\Http\Controllers\BettorController;
use App\Http\Controllers\CompetitionController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EventBettingController;
use App\Http\Controllers\ParticipationController;
use App\Http\Controllers\PhaseController;
use App\Http\Controllers\SportController;
use App\Http\Controllers\TeamController;
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

Route::prefix('v1')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('validate-registration', [AuthController::class, 'validateRegistration']);
    Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('reset-password', [AuthController::class, 'resetPassword']);

    Route::get('countries', [CountryController::class, 'index']);
    Route::apiResource('sports', SportController::class)->only(['index', 'show']);
    Route::apiResource('competitions', CompetitionController::class)->only(['index', 'show'])->whereUuid('competition');
    Route::apiResource('teams', TeamController::class)->only(['index', 'show'])->whereUuid('team');
    Route::apiResource('participations', ParticipationController::class)->only(['index', 'show'])->whereUuid('participation');
    Route::apiResource('competitions/{competition}/phases', PhaseController::class)->only(['index', 'show'])->whereUuid(['competition', 'phase']);
    Route::apiResource('phases/{phase}/events', EventController::class)->only(['index', 'show'])->whereUuid(['phase', 'event']);
    Route::get('bets/{bet}/bettors', [BettorController::class, 'index'])->whereUuid(['bet', 'bettor']);
});

Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::apiResource('bets', BetController::class)->whereUuid('bet');
    Route::apiResource('bets/{bet}/bettors', BettorController::class)->only(['store', 'destroy'])->whereUuid(['bet', 'bettor']);
    Route::post('bets/{bet}/bettors/{bettor}/resend-email-invitation', [BettorController::class, 'resendEmailInvitation'])->whereUuid(['bet', 'bettor']);
    Route::patch('bets/{bet}/activate', [BetController::class, 'activate']);
    Route::apiResource('/bets/{bet}/bettors/{bettor}/event-bettings', EventBettingController::class)->scoped()->only(['index', 'show', 'store', 'destroy'])->whereUuid(['bet', 'bettor', 'eventBetting']);
});

Route::prefix('v1')->middleware(['auth:sanctum', 'ensureUserIsAdmin'])->group(function () {
    Route::apiResource('countries', CountryController::class)->only(['store', 'update', 'destroy']);
    Route::apiResource('sports', SportController::class)->only(['store', 'update', 'destroy']);
    Route::apiResource('competitions', CompetitionController::class)->only(['store', 'update', 'destroy'])->whereUuid('competition');
    Route::apiResource('teams', TeamController::class)->only(['store', 'update', 'destroy'])->whereUuid('team');
    Route::apiResource('participations', ParticipationController::class)->only(['store', 'update', 'destroy'])->whereUuid('participation');
    Route::apiResource('competitions/{competition}/phases', PhaseController::class)->only(['store', 'update', 'destroy'])->whereUuid(['competition', 'phase']);
    Route::apiResource('phases/{phase}/events', EventController::class)->only(['store', 'update', 'destroy'])->whereUuid(['phase', 'event']);
});
