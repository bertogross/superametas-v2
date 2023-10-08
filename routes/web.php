<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Middleware\SetDynamicDatabase;


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

/*Route::middleware(['set-dynamic-database'])->group(function () {
   //Route::post('/login', [LoginController::class, 'login'])->middleware(SetDynamicDatabase::class);
    Route::get('{any}', [App\Http\Controllers\HomeController::class, 'index'])->name('index')->middleware(SetDynamicDatabase::class);
});*/

Route::post('/login', [LoginController::class, 'login'])->middleware(SetDynamicDatabase::class);


Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
