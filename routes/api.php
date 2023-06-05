<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TenantBotController;
use App\Http\Controllers\Api\CSVController;

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
Route::post('/auth/register', [AuthController::class, 'createUser']);
Route::post('/auth/login', [AuthController::class, 'loginUser']);
Route::post('/csv/clean', [CSVController::class, 'cleaner']);
Route::post('/csv/clean/twins', [CSVController::class, 'twinsCleaner']);

Route::middleware('auth:sanctum')->group(function () {
    // Protected routes
    Route::post('/bot/whatsapp/stop', [TenantBotController::class, 'stopWhatsapp']);
    Route::post('/bot/whatsapp/start', [TenantBotController::class, 'startWhatsapp']);
    Route::post('/bot/whatsapp/check', [TenantBotController::class, 'checkIfRunning']);
});