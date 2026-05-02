<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// =====================
// TEST
// =====================
Route::get('/', function () {
    return response()->json([
        'message' => 'Tamteen API is working 🚀'
    ]);
});

// =====================
// AUTH (PUBLIC)
// =====================
Route::post('/auth/send-otp', [AuthController::class, 'sendOtp']);
Route::post('/auth/verify-otp', [AuthController::class, 'verifyOtp']);

// =====================
// AUTH (PROTECTED)
// =====================
Route::middleware('auth:api')->group(function () {

    Route::get('/me', function () {
        return auth()->user();
    });

    Route::put('/user/update', [AuthController::class, 'updateProfile']);

    Route::post('/logout', [AuthController::class, 'logout']);
});