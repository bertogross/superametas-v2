<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\{
    HomeController,
    PostController,
    UserController,
    ProfileController,
    UploadController,
    SettingsDatabaseController,
    SettingsAccountController,
    Auth\LoginController
};
use App\Http\Middleware\SetDynamicDatabase;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Authentication Routes
Auth::routes();

// Language Translation
Route::get('index/{locale}', [HomeController::class, 'lang']);

// Root & Dashboard
Route::get('/', [HomeController::class, 'root'])->name('root');

// User Profile & Password Update
/*Route::prefix('user')->middleware('auth')->group(function () {
    Route::post('update-profile/{id}', [HomeController::class, 'updateProfile'])->name('user.updateProfile');
    Route::post('update-password/{id}', [HomeController::class, 'updatePassword'])->name('user.updatePassword');
});*/

// Post routes
Route::middleware(['auth'])->group(function () {

    // User Profile & Password Update
    Route::prefix('user')->group(function () {
        Route::post('update-profile/{id}', [HomeController::class, 'updateProfile'])->name('user.updateProfile');
        Route::post('update-password/{id}', [HomeController::class, 'updatePassword'])->name('user.updatePassword');
    });


    // Goal Sales Routes
    Route::get('/goal-sales', [PostController::class, 'showGoalSales']);
    Route::post('/goal-sales-store', [PostController::class, 'store']);

    // Goal Results Routes
    Route::get('/goal-results', [PostController::class, 'showGoalResults']);

    // User Settings and Profile
    Route::get('/settings-users', [UserController::class, 'index'])->name('settings-users.index');
    Route::post('/settings-users/store', [UserController::class, 'store']);
    //Route::put('/settings-users/update/{id}', [UserController::class, 'update']);
    Route::post('/settings-users/update/{id}', [UserController::class, 'update']);
    Route::get('/profile/{id?}', [UserController::class, 'show'])->name('profile.show');
    Route::get('/profile-settings', [ProfileController::class, 'settings'])->middleware('auth');
    Route::get('/get-user-modal-form/{id?}', [UserController::class, 'getUserModalContent']);

    // Admin Settings
    Route::middleware(['admin'])->group(function () {
        Route::get('/settings-database', [SettingsDatabaseController::class, 'showDatabase'])->name('settings.show');
        Route::put('/settings-departments/update', [SettingsDatabaseController::class, 'updateDepartments'])->name('departments.updateDepartments');
        Route::put('/settings-companies/update', [SettingsDatabaseController::class, 'updateCompanies'])->name('companies.updateCompanies');
        Route::get('/settings-account', [SettingsAccountController::class, 'show'])->name('settings.show');
        Route::post('/settings-account', [SettingsAccountController::class, 'store'])->name('settings.store');
    });

    // File Upload
    Route::post('/upload', [UploadController::class, 'upload'])->name('file.upload');
});

// Authentication with Dynamic Database
Route::middleware([SetDynamicDatabase::class])->group(function () {
    Route::post('/login', [LoginController::class, 'login']);
});

// Logout
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Catch-All Route - should be the last route
Route::get('{any}', [HomeController::class, 'index'])->where('any', '.*')->name('index');
