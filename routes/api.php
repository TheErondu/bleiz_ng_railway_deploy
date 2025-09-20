<?php

// routes/api.php
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LoanController;
use App\Http\Controllers\Api\RepaymentController;
use App\Http\Controllers\Api\DashboardController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Authentication routes
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);

        // Dashboard
       // Route::get('/dashboard', [DashboardController::class, 'index']);

        // Loans
        Route::apiResource('loans', LoanController::class);
        Route::get('/loans/{loan}/schedules', [LoanController::class, 'getSchedules']);

        // Repayments
       // Route::post('/loans/{loan}/repayments', [RepaymentController::class, 'store']);
        // Route::get('/loans/{loan}/repayments', [RepaymentController::class, 'index']);
        // Route::get('/repayments/{repayment}/receipt', [RepaymentController::class, 'getReceipt']);
    });
});
