<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\{
    Auth\LoginController,
    HomeController,
    UserController,
    ProfileController,
    UploadController,
    SettingsDatabaseController,
    SettingsAccountController,
    SettingsStorageController,
    GoalSalesController
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
    Route::get('/goal-sales', [GoalSalesController::class, 'index'])->name('goal-sales.index');
    Route::get('/goal-sales/settings', [GoalSalesController::class, 'getGoalSalesSettingsModalContent']);
    Route::get('/goal-sales/form/{meantime?}/{companyId?}/{purpose?}', [GoalSalesController::class, 'getGoalSalesEditModalContent']);

    Route::post('/goal-sales/post/{meantime?}/{companyId?}', [GoalSalesController::class, 'storeOrUpdateGoals']);

    // Goal Results Routes
    //Route::get('/goal-results', [GoalResultsController::class, 'index'])->name('goal-results.index');

    // User Profile
    Route::get('/profile/{id?}', [UserController::class, 'show'])->name('profile.show');

    // TODO profile-settings.blade.php
    //Route::get('/profile/settings', [ProfileController::class, 'settings'])->middleware('auth');


    // Admin Settings
    Route::middleware(['admin'])->group(function () {
        Route::get('/settings', [SettingsAccountController::class, 'index'])->name('index');
        Route::get('/settings/database', [SettingsDatabaseController::class, 'showDatabase'])->name('settings.database.show');

        Route::put('/settings/departments/update', [SettingsDatabaseController::class, 'updateDepartments'])->name('settings.departments.updateDepartments');
        Route::put('/settings/companies/update', [SettingsDatabaseController::class, 'updateCompanies'])->name('settings.companies.updateCompanies');

        Route::get('/settings/account', [SettingsAccountController::class, 'show'])->name('settings.account.show');
        Route::post('/settings/account/update', [SettingsAccountController::class, 'store'])->name('settings.account.store');

        // User Settings and Profile
        Route::get('/settings/users', [UserController::class, 'index'])->name('settings-users.index');
        Route::post('/settings/users/store', [UserController::class, 'store']);
        Route::post('/settings/users/update/{id}', [UserController::class, 'update']);
        Route::get('/settings/users/modal-form/{id?}', [UserController::class, 'getUserModalContent']);

        Route::get('/settings/storage', [SettingsStorageController::class, 'index'])->name('settings.storage');
        Route::get('/settings/storage/oauth-callback', [SettingsStorageController::class, 'oauthCallback'])->name('settings.storage.callback');
    });

    // File Upload
    Route::post('/upload/avatar', [UploadController::class, 'uploadAvatar']);
    Route::post('/upload/cover', [UploadController::class, 'uploadCover']);
    Route::post('/upload/logo', [UploadController::class, 'uploadCompanyLogo']);
    Route::delete('/upload/logo', [UploadController::class, 'deleteCompanyLogo']);

});

// Authentication with Dynamic Database
Route::middleware([SetDynamicDatabase::class])->group(function () {
    Route::post('/login', [LoginController::class, 'login']);
});

Route::post('/check-databases', [LoginController::class, 'checkDatabases']);


// Logout
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Catch-All Route - should be the last route
Route::get('{any}', [HomeController::class, 'index'])->where('any', '.*')->name('index');
