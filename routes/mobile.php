<?php
// routes/mobile.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Mobile\WorkerController;

// All mobile routes should be under 'mobile' prefix
Route::prefix('mobile')->name('mobile.')->group(function () {
    // Public routes
    Route::get('/login', [WorkerController::class, 'showLogin'])->name('login');
    Route::post('/login', [WorkerController::class, 'login'])->name('login.submit');

    // Protected routes
    Route::get('/dashboard', [WorkerController::class, 'dashboard'])->name('dashboard');
    Route::get('/jobs/{greenhouseId}', [WorkerController::class, 'jobs'])->name('jobs');
    Route::get('/bays/{greenhouseId}/{jobTypeId}', [WorkerController::class, 'bays'])->name('bays');
    Route::get('/form/{greenhouseId}/{bayId}/{jobTypeId}', [WorkerController::class, 'form'])->name('form');
    Route::post('/submit-job', [WorkerController::class, 'submit'])->name('submit');
    Route::post('/logout', [WorkerController::class, 'logout'])->name('logout');

    Route::get('/submitted', [WorkerController::class, 'submitted'])->name('submitted');
    Route::get('/submitted/filter', [WorkerController::class, 'filterSubmitted'])->name('submitted.filter');
    Route::get('/jobs/{id}/edit', [WorkerController::class, 'edit'])->name('jobs.edit');
    Route::put('/jobs/{id}', [WorkerController::class, 'update'])->name('jobs.update');
    Route::get('/jobs/{id}/view', [WorkerController::class, 'view'])->name('jobs.view');
});