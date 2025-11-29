<?php

use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectDataController;
use App\Http\Controllers\TaskLogController;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('auth')->prefix('dashboard')->group(function () {
    Route::resource('projects', ProjectController::class);

    Route::post('projects/{project}/finish-analyzing', [ProjectController::class, 'finishAnalyzing'])->name('projects.finishAnalyzing');
    Route::post('projects/{project}/delete-raw', [ProjectController::class, 'deleteRawData'])->name('projects.deleteRawData');

    Route::post('projects/{project}/upload-json', [ProjectDataController::class, 'uploadBatch'])->name('projects.upload-json');
    Route::post('projects/{project}/start-processing', [ProjectDataController::class, 'startBatchProcessing'])->name('projects.startProcessing');
    Route::get('projects/{project}/analytics', [AnalyticsController::class, 'show'])->name('projects.analytics');
});

Route::get('/dashboard/projects/{taskLog}/progress', [TaskLogController::class, 'progress'])->withoutMiddleware([VerifyCsrfToken::class]);
Route::get('/dashboard/projects/{project}/progress-sse', [TaskLogController::class, 'progressSse'])->name('projects.progress-sse');

require __DIR__ . '/auth.php';
