<?php

use Illuminate\Support\Facades\Route;

Route::get('/', [App\Http\Controllers\StandingsController::class, 'index'])->name('bang-xep-hang');
Route::get('/api/leagues/search', [App\Http\Controllers\StandingsController::class, 'searchLeagues'])->name('api.leagues.search');
Route::get('/api/standings', [App\Http\Controllers\StandingsController::class, 'getStandingsData'])->name('api.standings');

Route::get('/bang-xep-hang-bong-da', function () {
    return redirect()->route('bang-xep-hang');
});

Route::get('/lich-thi-dau', [App\Http\Controllers\ScheduleController::class, 'index'])->name('lich-thi-dau');

Route::get('/ket-qua', [App\Http\Controllers\ResultsController::class, 'index'])->name('ket-qua');

Route::get('/top-ghi-ban', [App\Http\Controllers\TopScorersController::class, 'index'])->name('top-ghi-ban');
Route::get('/api/top-scorers/search', [App\Http\Controllers\TopScorersController::class, 'searchLeagues'])->name('api.top-scorers.search');
Route::get('/api/top-scorers', [App\Http\Controllers\TopScorersController::class, 'getTopScorersData'])->name('api.top-scorers');

Route::get('/tin-the-thao', [App\Http\Controllers\PostController::class, 'indexSportsNews'])->name('tin-the-thao');
Route::get('/nhan-dinh-bong-da', [App\Http\Controllers\PostController::class, 'indexPredictions'])->name('nhan-dinh-bong-da');
Route::get('/bai-viet/{slug}', [App\Http\Controllers\PostController::class, 'show'])->name('post.show');

// Admin Routes
Route::prefix('admin')->name('admin.')->group(function () {
    // Auth routes (public)
    Route::get('/login', [App\Http\Controllers\Admin\AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [App\Http\Controllers\Admin\AuthController::class, 'login']);
    Route::post('/logout', [App\Http\Controllers\Admin\AuthController::class, 'logout'])->name('logout');
    
    // Protected routes
    Route::middleware('admin.auth')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
        
        // Posts management
        Route::resource('posts', App\Http\Controllers\Admin\PostController::class);
        
        // API Configuration
        Route::get('/api-config', [App\Http\Controllers\Admin\ApiConfigController::class, 'index'])->name('api-config.index');
        Route::put('/api-config', [App\Http\Controllers\Admin\ApiConfigController::class, 'update'])->name('api-config.update');
        
        // Image Upload for TinyMCE
        Route::post('/upload-image', [App\Http\Controllers\Admin\PostController::class, 'uploadImage'])->name('upload-image');
    });
});
