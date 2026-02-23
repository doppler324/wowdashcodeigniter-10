<?php

use App\Http\Controllers\ProjectsController;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\KeywordsController;
use App\Http\Controllers\ActivitiesController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\YandexMetrikaController;
use App\Http\Controllers\DonorController;
use App\Http\Controllers\ExpensesController;
use Illuminate\Support\Facades\Route;

// Home route
Route::get('/', function () {
    return redirect()->route('projects.index');
});

// Auth routes
Route::get('/login', [App\Http\Controllers\AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [App\Http\Controllers\AuthController::class, 'login'])->name('login-post');
Route::get('/register', [App\Http\Controllers\AuthController::class, 'showRegisterForm'])->name('signUp');
Route::post('/register', [App\Http\Controllers\AuthController::class, 'register']);
Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout'])->name('logout');

// Protected routes
Route::middleware(['auth'])->group(function () {
    // Projects
    Route::resource('projects', ProjectsController::class);

    // Pages
    Route::prefix('projects/{project}')->group(function () {
        Route::get('/pages', [PagesController::class, 'index'])->name('projects.pages.index');
        Route::get('/pages/create', [PagesController::class, 'create'])->name('projects.pages.create');
        Route::post('/pages', [PagesController::class, 'store'])->name('projects.pages.store');
        Route::get('/pages/{page}', [PagesController::class, 'show'])->name('projects.pages.show');
        Route::get('/pages/{page}/edit', [PagesController::class, 'edit'])->name('projects.pages.edit');
        Route::put('/pages/{page}', [PagesController::class, 'update'])->name('projects.pages.update');
        Route::delete('/pages/{page}', [PagesController::class, 'destroy'])->name('projects.pages.destroy');
        Route::post('/pages/import', [PagesController::class, 'import'])->name('projects.pages.import');

        // Keywords
        Route::get('/pages/{page}/keywords', [KeywordsController::class, 'index'])->name('projects.pages.keywords.index');
        Route::get('/pages/{page}/keywords/create', [KeywordsController::class, 'create'])->name('projects.pages.keywords.create');
        Route::post('/pages/{page}/keywords', [KeywordsController::class, 'store'])->name('projects.pages.keywords.store');
        Route::get('/pages/{page}/keywords/{keyword}', [KeywordsController::class, 'show'])->name('projects.pages.keywords.show');
        Route::get('/pages/{page}/keywords/{keyword}/edit', [KeywordsController::class, 'edit'])->name('projects.pages.keywords.edit');
        Route::put('/pages/{page}/keywords/{keyword}', [KeywordsController::class, 'update'])->name('projects.pages.keywords.update');
        Route::delete('/pages/{page}/keywords/{keyword}', [KeywordsController::class, 'destroy'])->name('projects.pages.keywords.destroy');

        // Keywords
        Route::get('/keywords', [KeywordsController::class, 'all'])->name('projects.keywords.index');

        // Activities
        Route::get('/activities', [ActivitiesController::class, 'index'])->name('projects.activities.index');
        Route::get('/activities/create', [ActivitiesController::class, 'create'])->name('projects.activities.create');
        Route::post('/activities', [ActivitiesController::class, 'store'])->name('projects.activities.store');
        Route::get('/activities/{activity}', [ActivitiesController::class, 'show'])->name('projects.activities.show');
        Route::get('/activities/{activity}/edit', [ActivitiesController::class, 'edit'])->name('projects.activities.edit');
        Route::put('/activities/{activity}', [ActivitiesController::class, 'update'])->name('projects.activities.update');
        Route::delete('/activities/{activity}', [ActivitiesController::class, 'destroy'])->name('projects.activities.destroy');

        // Settings
        Route::get('/settings', [App\Http\Controllers\ProjectSettingController::class, 'index'])->name('projects.settings.index');
        Route::get('/settings/create', [App\Http\Controllers\ProjectSettingController::class, 'create'])->name('projects.settings.create');
        Route::post('/settings', [App\Http\Controllers\ProjectSettingController::class, 'store'])->name('projects.settings.store');
        Route::get('/settings/{setting}', [App\Http\Controllers\ProjectSettingController::class, 'show'])->name('projects.settings.show');
        Route::get('/settings/{setting}/edit', [App\Http\Controllers\ProjectSettingController::class, 'edit'])->name('projects.settings.edit');
        Route::put('/settings/{setting}', [App\Http\Controllers\ProjectSettingController::class, 'update'])->name('projects.settings.update');
        Route::delete('/settings/{setting}', [App\Http\Controllers\ProjectSettingController::class, 'destroy'])->name('projects.settings.destroy');

        // Donors
        Route::get('/donors', [DonorController::class, 'index'])->name('projects.donors.index');
        Route::prefix('pages/{page}')->group(function () {
            Route::get('/donors/create', [DonorController::class, 'create'])->name('projects.pages.donors.create');
            Route::post('/donors', [DonorController::class, 'store'])->name('projects.pages.donors.store');
            Route::get('/donors/{donor}', [DonorController::class, 'show'])->name('projects.pages.donors.show');
            Route::get('/donors/{donor}/edit', [DonorController::class, 'edit'])->name('projects.pages.donors.edit');
            Route::put('/donors/{donor}', [DonorController::class, 'update'])->name('projects.pages.donors.update');
            Route::delete('/donors/{donor}', [DonorController::class, 'destroy'])->name('projects.pages.donors.destroy');
            Route::get('/donors', [DonorController::class, 'indexForPage'])->name('projects.pages.donors.index');
        });

        // Expenses
        Route::get('/expenses', [ExpensesController::class, 'index'])->name('projects.expenses.index');
        Route::get('/expenses/create', [ExpensesController::class, 'create'])->name('projects.expenses.create');
        Route::post('/expenses', [ExpensesController::class, 'store'])->name('projects.expenses.store');
        Route::get('/expenses/{expense}', [ExpensesController::class, 'show'])->name('projects.expenses.show');
        Route::get('/expenses/{expense}/edit', [ExpensesController::class, 'edit'])->name('projects.expenses.edit');
        Route::put('/expenses/{expense}', [ExpensesController::class, 'update'])->name('projects.expenses.update');
        Route::delete('/expenses/{expense}', [ExpensesController::class, 'destroy'])->name('projects.expenses.destroy');

        // Chart data
        Route::get('/chart-data', [ProjectsController::class, 'getChartDataByDateRange'])->name('projects.chart-data');
    });

    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');

    // Yandex Metrika OAuth
    Route::get('/yandex/oauth', [YandexMetrikaController::class, 'oauth'])->name('yandex.oauth');
    Route::get('/yandex/callback', [YandexMetrikaController::class, 'callback'])->name('yandex.callback');

    // Donors
    Route::resource('donors', DonorController::class);
});
