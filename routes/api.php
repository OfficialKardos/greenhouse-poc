<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\GreenhouseController;
use App\Http\Controllers\Api\BayController;
use App\Http\Controllers\Api\JobTypeController;
use App\Http\Controllers\Api\JobFieldController;
use App\Http\Controllers\Api\DropdownListController;
use App\Http\Controllers\Api\DropdownValueController;
use App\Http\Controllers\Api\JobEntryController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\UserController;

// Public routes
Route::post('/login', [AuthController::class, 'login']);

// Protected routes with authentication
Route::middleware('auth.api')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    
    // User management (all authenticated users can access their own)
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    
    // Greenhouses
    Route::apiResource('greenhouses', GreenhouseController::class);
    Route::get('/greenhouses/{greenhouse}/bays', [GreenhouseController::class, 'bays']);
    
    // Bays
    Route::apiResource('bays', BayController::class);
    Route::get('/greenhouses/{greenhouse}/bays-list', [BayController::class, 'byGreenhouse']);
    
    // Job Types
    Route::apiResource('job-types', JobTypeController::class);
    Route::get('/job-types/all', [JobTypeController::class, 'all']);
    Route::get('/job-types/{jobType}/fields', [JobTypeController::class, 'fields']);
    
    // Job Fields
    Route::apiResource('job-fields', JobFieldController::class);
    Route::get('/job-types/{jobType}/job-fields', [JobFieldController::class, 'index']);
    Route::post('/job-fields/reorder', [JobFieldController::class, 'reorder']);
    
    // Dropdown Lists
    Route::apiResource('dropdown-lists', DropdownListController::class);
    Route::get('/dropdown-lists/{dropdownList}/values', [DropdownListController::class, 'values']);
    Route::post('/dropdown-lists/{dropdownList}/values', [DropdownListController::class, 'storeValue']);
    
    // Dropdown Values
    Route::apiResource('dropdown-values', DropdownValueController::class);
    Route::post('/dropdown-values/reorder', [DropdownValueController::class, 'reorder']);
    
    // Job Entries
    Route::apiResource('job-entries', JobEntryController::class);
    
    // Reports
    Route::prefix('reports')->group(function () {
        Route::get('/summary', [ReportController::class, 'summary']);
        Route::get('/chemicals', [ReportController::class, 'chemicals']);
        Route::get('/soil', [ReportController::class, 'soil']);
        Route::get('/pests', [ReportController::class, 'pests']);
        Route::get('/growth', [ReportController::class, 'growth']);
        Route::get('/export', [ReportController::class, 'export']);
    });
});

// Admin-only routes with additional middleware
Route::middleware(['auth.api', 'admin.access'])->group(function () {
    // Full user management (create, update, delete) - admin only
    Route::post('/users', [UserController::class, 'store']);
    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);
});