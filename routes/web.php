<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();
//Language Translation
Route::get('index/{locale}', [App\Http\Controllers\HomeController::class, 'lang']);

Route::get('/', [App\Http\Controllers\HomeController::class, 'root'])->name('root');

//Update User Details
Route::post('/update-profile/{id}', [App\Http\Controllers\HomeController::class, 'updateProfile'])->name('updateProfile');
Route::post('/update-password/{id}', [App\Http\Controllers\HomeController::class, 'updatePassword'])->name('updatePassword');

Route::get('{any}', [App\Http\Controllers\HomeController::class, 'index'])->name('index');

/*
// RELATED TO TENANCY FOR LARAVEL
// Default routes for the main domain (without subdomain)
Route::group(['domain' => env('APP_DOMAIN')], function () {
    Auth::routes();

    // Language Translation
    Route::get('index/{locale}', [App\Http\Controllers\HomeController::class, 'lang']);

    Route::get('/', [App\Http\Controllers\HomeController::class, 'root'])->name('root');

    // Update User Details
    Route::post('/update-profile/{id}', [App\Http\Controllers\HomeController::class, 'updateProfile'])->name('updateProfile');
    Route::post('/update-password/{id}', [App\Http\Controllers\HomeController::class, 'updatePassword'])->name('updatePassword');

    // Other routes for the main domain can go here

    Route::get('{any}', [App\Http\Controllers\HomeController::class, 'index'])->name('index');
});


$subdomainParts = isset($_SERVER['HTTP_HOST']) ? explode('.', $_SERVER['HTTP_HOST']) : '';
$subdomain = is_array($subdomainParts) ? array_shift($subdomainParts) : 'app';

// Wildcard subdomain route
Route::group(['domain' => $subdomain.'.'.env('APP_DOMAIN')], function () {
    Auth::routes();

    // Language Translation
    Route::get('index/{locale}', [App\Http\Controllers\HomeController::class, 'lang']);

    Route::get('/', [App\Http\Controllers\HomeController::class, 'root'])->name('root');

    // Update User Details
    Route::post('/update-profile/{id}', [App\Http\Controllers\HomeController::class, 'updateProfile'])->name('updateProfile');
    Route::post('/update-password/{id}', [App\Http\Controllers\HomeController::class, 'updatePassword'])->name('updatePassword');

    // Other routes specific to the tenant can go here

    Route::get('{any}', [App\Http\Controllers\HomeController::class, 'index'])->name('index');
});
// RELATED TO TENANCY FOR LARAVEL
*/
