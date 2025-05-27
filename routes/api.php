<?php

use Illuminate\Http\Request;
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

Route::middleware('auth:sanctum')->group(function () {
    // Email routes
    Route::get('/emails', [\App\Http\Controllers\Api\EmailController::class, 'index']);
    Route::post('/emails/{email}/assign', [\App\Http\Controllers\Api\EmailController::class, 'assign']);
    Route::post('/emails/{email}/reply', [\App\Http\Controllers\Api\EmailController::class, 'reply']);
    
    // Report routes
    Route::get('/reports/summary', [\App\Http\Controllers\Api\ReportController::class, 'summary']);
    Route::get('/reports/response-times', [\App\Http\Controllers\Api\ReportController::class, 'responseTimes']);
    
    // User routes
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    // Settings routes
    Route::put('/settings/profile', [\App\Http\Controllers\Api\SettingController::class, 'updateProfile']);
    Route::put('/settings/password', [\App\Http\Controllers\Api\SettingController::class, 'updatePassword']);
    Route::put('/settings/notifications', [\App\Http\Controllers\Api\SettingController::class, 'updateNotifications']);
});

// Public routes
Route::post('/login', [\App\Http\Controllers\Api\AuthController::class, 'login']);
Route::post('/logout', [\App\Http\Controllers\Api\AuthController::class, 'logout'])->middleware('auth:sanctum');
