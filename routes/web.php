<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\AdminController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Public routes
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/tailwind-test', function () {
    return view('tailwind-test');
});

// Authentication routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Protected routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Chat routes
    Route::get('/chat', [ChatController::class, 'show'])->name('chat.show');
    Route::post('/chat/send', [ChatController::class, 'sendMessage'])->name('chat.send');
    Route::get('/chat/history/{sessionId}', [ChatController::class, 'getChatHistory'])->name('chat.history');
    
    // Admin routes
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('index');
        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::put('/users/{id}', [AdminController::class, 'updateUser'])->name('update-user');
        Route::delete('/users/{id}', [AdminController::class, 'deleteUser'])->name('delete-user');
        Route::get('/urgency-management', [AdminController::class, 'urgencyManagement'])->name('urgency-management');
        Route::post('/diseases', [AdminController::class, 'addDisease'])->name('add-disease');
        Route::delete('/diseases/{id}', [AdminController::class, 'deleteDisease'])->name('delete-disease');
        Route::post('/diseases/reset', [AdminController::class, 'resetDiseases'])->name('reset-diseases');
        Route::get('/consultation-history', [AdminController::class, 'consultationHistory'])->name('consultation-history');
        Route::get('/consultation-history/{id}', [AdminController::class, 'viewConsultation'])->name('view-consultation');
        Route::delete('/consultation-history/{id}', [AdminController::class, 'deleteConsultation'])->name('delete-consultation');
    });
});
