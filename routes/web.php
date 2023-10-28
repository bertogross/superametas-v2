<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\{
    Auth\LoginController,
    HomeController,
    ProfileController,
    UploadController,
    SettingsUserController,
    SettingsDatabaseController,
    SettingsAccountController,
    GoalSalesController,
    AuditsController,
    SettingsApiKeysController,
    GoogleDriveController,
    DropboxController
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
    Route::post('update-profile/{id}', [HomeController::class, 'updateProfile'])->name('updateProfileURL');
    Route::post('update-password/{id}', [HomeController::class, 'updatePassword'])->name('updatePasswordURL');
});*/

// Post routes
Route::middleware(['auth'])->group(function () {

    // User Profile & Password Update
    Route::prefix('user')->group(function () {
        Route::post('update-profile/{id}', [HomeController::class, 'updateProfile'])->name('updateProfileURL');
        Route::post('update-password/{id}', [HomeController::class, 'updatePassword'])->name('updatePasswordURL');
    });


    // Goal Sales Routes
    Route::get('/goal-sales', [GoalSalesController::class, 'index'])->name('goalSalesIndexURL');
    Route::get('/goal-sales/settings', [GoalSalesController::class, 'getGoalSalesSettingsModalContent']);
    Route::get('/goal-sales/form/{meantime?}/{companyId?}/{purpose?}', [GoalSalesController::class, 'getGoalSalesEditModalContent']);// view form

    Route::post('/goal-sales/post/{meantime?}/{companyId?}', [GoalSalesController::class, 'storeOrUpdateGoals']); // update or store

    Route::post('/goal-sales/analytic-mode', [GoalSalesController::class, 'analyticMode']);
    Route::post('/goal-sales/slide-mode', [GoalSalesController::class, 'slideMode']);
    Route::post('/goal-sales/default-mode', [GoalSalesController::class, 'defaultMode']);

    // Goal Results Routes
    //Route::get('/goal-results', [GoalResultsController::class, 'index'])->name('goalResultsIndexURL');


    // Audits Routes
    Route::get('/audits', [AuditsController::class, 'index'])->name('auditsIndexURL');
    Route::get('/audit/{id?}', [AuditsController::class, 'show'])->name('auditsShowURL');
    //Route::post('/audits', [AuditsController::class, 'store']);
    Route::get('/audits/form/{id?}', [AuditsController::class, 'getAuditEditModalContent']);// view form
    Route::post('/audits/post/{id?}', [AuditsController::class, 'update']); // update or store

    // User Profile
    Route::get('/profile/{id?}', [SettingsUserController::class, 'show'])->name('profileShowURL');

    // TODO profile-settings.blade.php
    //Route::get('/profile/settings', [ProfileController::class, 'settings'])->middleware('auth');


    // Admin Settings
    Route::middleware(['admin'])->group(function () {
        Route::get('/settings', [SettingsAccountController::class, 'index'])->name('settingsIndexURL');

        // Subscription and form related with the primary user
        Route::get('/settings/account', [SettingsAccountController::class, 'show'])->name('settingsAccountShowURL');
        Route::post('/settings/account/store', [SettingsAccountController::class, 'store'])->name('settingsAccountStoreURL');

        // API Key, Files Manager, Sinc Database (sales, companies, departments)
        Route::get('/settings/api-keys', [SettingsApiKeysController::class, 'index'])->name('settingsApiKeysURL');
        //Route::get('/settings/files', [GoogleDriveController::class, 'files'])->name('googleDriveFilesURL');
        Route::get('/settings/files', [DropboxController::class, 'files'])->name('DropboxFilesURL');
        Route::get('/settings/database', [SettingsDatabaseController::class, 'index'])->name('settingsDatabaseIndexURL');
            Route::put('/settings/departments/store', [SettingsDatabaseController::class, 'updateDepartments'])->name('settingsDepartmentsUpdateURL');
            Route::put('/settings/companies/store', [SettingsDatabaseController::class, 'updateCompanies'])->name('settingsCompaniesUpdateURL');

        // User Settings
        Route::get('/settings/users', [SettingsUserController::class, 'index'])->name('settingsUsersIndexURL');
        Route::post('/settings/users/store', [SettingsUserController::class, 'store']);
        Route::post('/settings/users/update/{id}', [SettingsUserController::class, 'update']);
        Route::get('/settings/users/modal-form/{id?}', [SettingsUserController::class, 'getUserModalContent']);

        // TODO Security Settings
        //Route::get('/settings/security', [SettingsSecurityController::class, 'index'])->name('settingsSecurityIndexURL');

    });

    // File Upload
    Route::post('/upload/avatar', [UploadController::class, 'uploadAvatar']);
    Route::post('/upload/cover', [UploadController::class, 'uploadCover']);
    Route::post('/upload/logo', [UploadController::class, 'uploadLogo']);
    Route::delete('/upload/logo', [UploadController::class, 'deleteLogo']);

});

// Authentication with Dynamic Database
Route::middleware([SetDynamicDatabase::class])->group(function () {
    Route::post('/login', [LoginController::class, 'login']);
});

// Login and Logout
Route::post('/check-databases', [LoginController::class, 'checkDatabases']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');


// Catch-All Route - should be the last route
Route::get('{any}', [HomeController::class, 'index'])->where('any', '.*')->name('index');

