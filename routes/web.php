<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\MicrosoftAuthController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\KnowledgeBaseController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// OAuth Routes
Route::get('/auth/microsoft', [MicrosoftAuthController::class, 'redirect'])->name('auth.microsoft');
Route::get('/auth/microsoft/callback', [MicrosoftAuthController::class, 'callback']);
Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback']);

// Authenticated Routes
Route::middleware(['auth'])->group(function () {
    // Email Management
    Route::get('/dashboard', [EmailController::class, 'index'])->name('dashboard');
    Route::post('/emails/{email}/assign', [EmailController::class, 'assign'])->name('emails.assign');
    Route::post('/emails/{email}/status/{status}', [EmailController::class, 'updateStatus'])->name('emails.status');
    Route::post('/emails/{email}/reply', [EmailController::class, 'reply'])->name('emails.reply');
    
    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::post('/reports/generate', [ReportController::class, 'generate'])->name('reports.generate');
    
    // Knowledge Base
    Route::resource('knowledge-base', KnowledgeBaseController::class);
});
