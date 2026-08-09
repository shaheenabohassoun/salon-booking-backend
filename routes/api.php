<?php

use App\Http\Controllers\Api\Admin\AppointmentController as AdminAppointmentController;
use App\Http\Controllers\Api\Admin\DashboardController;
use App\Http\Controllers\Api\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\Api\Admin\StaffController as AdminStaffController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Public\BookingController;
use App\Http\Controllers\Api\Public\ServiceController as PublicServiceController;
use App\Http\Controllers\Api\Public\StaffController as PublicStaffController;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/health', fn () => response()->json([
    'status' => 'ok',
    'app' => 'Salon Booker API',
    'time' => now()->toIso8601String(),
]));

Route::prefix('public')->group(function () {
    Route::get('/categories', fn () => ServiceCategory::query()->where('is_active', true)->orderBy('sort_order')->get());
    Route::get('/services', [PublicServiceController::class, 'index']);
    Route::get('/services/{slug}', [PublicServiceController::class, 'show']);
    Route::get('/staff', [PublicStaffController::class, 'index']);
    Route::get('/staff/{slug}', [PublicStaffController::class, 'show']);
    Route::get('/availability', [BookingController::class, 'availability']);
    Route::post('/appointments', [BookingController::class, 'store']);
});

Route::post('/auth/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/user', fn (Request $request) => $request->user());

    Route::prefix('admin')->group(function () {
        Route::get('/dashboard', DashboardController::class);
        Route::apiResource('services', AdminServiceController::class);
        Route::apiResource('staff', AdminStaffController::class);
        Route::apiResource('appointments', AdminAppointmentController::class)->only(['index', 'show', 'update']);
        Route::get('/categories', fn () => ServiceCategory::query()->orderBy('sort_order')->get());
    });
});
