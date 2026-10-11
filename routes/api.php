<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CaseApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — Jajal Medical Case Management
|--------------------------------------------------------------------------
|
| All 7 required REST API endpoints authenticated via Laravel Sanctum:
| 1. POST   /api/login          (Public)
| 2. POST   /api/logout         (Protected)
| 3. GET    /api/cases          (Admin & Viewer)
| 4. POST   /api/cases          (Admin only)
| 5. GET    /api/cases/{id}     (Admin & Viewer)
| 6. PUT    /api/cases/{id}     (Admin only)
| 7. DELETE /api/cases/{id}     (Admin only)
|
*/

// Public Authentication Endpoint
Route::post('/login', [AuthController::class, 'login'])->name('api.login');

// Protected Endpoints (Requires valid Sanctum Bearer Token)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');

    Route::get('/cases', [CaseApiController::class, 'index'])->name('api.cases.index');
    Route::post('/cases', [CaseApiController::class, 'store'])->name('api.cases.store');
    Route::get('/cases/{id}', [CaseApiController::class, 'show'])->name('api.cases.show');
    Route::put('/cases/{id}', [CaseApiController::class, 'update'])->name('api.cases.update');
    Route::delete('/cases/{id}', [CaseApiController::class, 'destroy'])->name('api.cases.destroy');
});
