<?php

use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\LanguageClassAssignmentController;
use App\Http\Controllers\LanguageClassController;
use App\Http\Controllers\LanguageClassStatisticsController;
use App\Http\Controllers\ProfessorController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
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

// Public routes
Route::middleware(['throttle:auth'])->group(function () {
    Route::post('/register', [RegisteredUserController::class, 'store']);
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->middleware('throttle:login');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store']);
    Route::post('/reset-password', [NewPasswordController::class, 'store']);
});

// Protected routes
// Current authenticated user
Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {
    Route::get('/user', [UserController::class, 'show']);
    Route::put('/user', [UserController::class, 'update']);
    Route::patch('/user/password', [UserController::class, 'updatePassword']);
    Route::delete('/user', [UserController::class, 'destroy']);
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy']);

    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])
        ->middleware('role:admin');
});

// Professors and Students listing and details
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/professors/{id}', [ProfessorController::class, 'show']);
    Route::get('/professors', [ProfessorController::class, 'index']);
    Route::get('/students/{id}', [StudentController::class, 'show']);
    Route::get('/students', [StudentController::class, 'index']);
});

// Language Classes
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/language-classes/{id}', [LanguageClassController::class, 'show']);
    Route::get('/language-classes', [LanguageClassController::class, 'index']);

    Route::middleware(['role:admin'])->group(function () {
        Route::post('/language-classes', [LanguageClassController::class, 'store']);
        Route::put('/language-classes/{id}', [LanguageClassController::class, 'update']);
        Route::delete('/language-classes/{id}', [LanguageClassController::class, 'destroy']);
    });

    Route::middleware(['role:admin,professor'])->group(function () {
        Route::post('/language-classes/{id}/complete', [LanguageClassController::class, 'confirmCompletion']);
    });
});

// Language Class Assignments
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/language-class-assignments/{id}', [LanguageClassAssignmentController::class, 'show']);
    Route::get('/language-class-assignments', [LanguageClassAssignmentController::class, 'index']);

    Route::middleware(['role:admin'])->group(function () {
        Route::post('/language-class-assignments', [LanguageClassAssignmentController::class, 'store']);
        Route::put('/language-class-assignments/{id}', [LanguageClassAssignmentController::class, 'update']);
        Route::delete('/language-class-assignments/{id}', [LanguageClassAssignmentController::class, 'destroy']);
    });
});

// Statistics routes
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::prefix('statistics')->group(function () {
        Route::get('professors', [LanguageClassStatisticsController::class, 'professors']);
        Route::get('students', [LanguageClassStatisticsController::class, 'students']);
        Route::get('daily-classes', [LanguageClassStatisticsController::class, 'dailyClasses']);
    });
});
