<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TaskController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/auth/resend-otp', [AuthController::class, 'resendOtp']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::get('/auth/google/redirect', [AuthController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::put('/auth/profile', [AuthController::class, 'updateProfile']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/dashboard/stats', [TaskController::class, 'dashboardStats']);
    Route::get('/dashboard/projects', [TaskController::class, 'projects']);
    Route::get('/users', [TaskController::class, 'users']);
    Route::post('/tasks/{task}/comments', [TaskController::class, 'storeComment']);
    Route::apiResource('tasks', TaskController::class);
});
