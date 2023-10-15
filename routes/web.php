<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\{
    HomeController,
    PostController,
    UserController,
    SettingsDatabaseController,
    Auth\LoginController
};
use App\Http\Middleware\SetDynamicDatabase;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Auth::routes();

// Language Translation
Route::get('index/{locale}', [HomeController::class, 'lang']);

// Root & Dashboard
Route::get('/', [HomeController::class, 'root'])->name('root');

// User Profile & Password Update
Route::prefix('user')->middleware('auth')->group(function () {
    Route::post('update-profile/{id}', [HomeController::class, 'updateProfile'])->name('user.updateProfile');
    Route::post('update-password/{id}', [HomeController::class, 'updatePassword'])->name('user.updatePassword');
});


// User Settings and page Profile
Route::get('/settings-users', [UserController::class, 'index'])->name('settings-users.index');
Route::post('/settings-users', [UserController::class, 'store']);
Route::put('/settings-users', [UserController::class, 'update']);
Route::get('/profile/{id?}', [UserController::class, 'show'])->name('profile.show');

// Post routes
Route::middleware(['auth'])->group(function () {
    Route::get('/goal-sales', [PostController::class, 'showGoalSales']);
    Route::get('/goal-results', [PostController::class, 'showGoalResults']);

    /*
    Route::get('/goal-sales/{startYear?}/{endYear?}/{startMonth?}/{endMonth?}', [PostController::class, 'showGoalResults']);
    Route::get('/goal-results/{startYear?}/{endYear?}/{startMonth?}/{endMonth?}', [PostController::class, 'showGoalResults']);
    */

    /*
    Route::get('/goal-sales/{startYear?}/{endYear?}/{startMonth?}/{endMonth?}', [PostController::class, 'showGoalSales'])
    ->where([
        'startYear' => '[0-9]+',
        'endYear' => '[0-9]+',
        'startMonth' => '[0-9]+',
        'endMonth' => '[0-9]+',
    ]);

    Route::get('/goal-results/{startYear?}/{endYear?}/{startMonth?}/{endMonth?}', [PostController::class, 'showGoalResults'])
    ->where([
        'startYear' => '[0-9]+',
        'endYear' => '[0-9]+',
        'startMonth' => '[0-9]+',
        'endMonth' => '[0-9]+',
    ]);
    */


    // Admin Setting routes
    Route::get('/settings-database', [SettingsDatabaseController::class, 'showSettingsDatabase'])->name('settings.show');
    Route::put('/settings-departments/update', [SettingsDatabaseController::class, 'updateDepartments'])->name('departments.updateDepartments');
    Route::put('/settings-companies/update', [SettingsDatabaseController::class, 'updateCompanies'])->name('companies.updateCompanies');

});



// Authentication with Dynamic Database
Route::middleware([SetDynamicDatabase::class])->group(function () {
    Route::post('/login', [LoginController::class, 'login']);
    Route::post('/goal-sales-store', [PostController::class, 'store']);
});

// Logout
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Catch-All Route - should be the last route
Route::get('{any}', [HomeController::class, 'index'])->where('any', '.*')->name('index');
