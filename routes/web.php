<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use App\Http\Middleware\SetDynamicDatabase;
use App\Http\Controllers\Auth\LoginController;


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
Route::get('index/{locale}', [HomeController::class, 'lang']);


//Call home dashboard template resources\views\index.blade.php
Route::get('/', [HomeController::class, 'root'])->name('root');


//Update User Details
Route::post('/update-profile/{id}', [HomeController::class, 'updateProfile'])->name('updateProfile');
Route::post('/update-password/{id}', [HomeController::class, 'updatePassword'])->name('updatePassword');


//Call diferent template views with path related
Route::get('{any}', [HomeController::class, 'index'])->name('index');// ->middleware(SetDynamicDatabase::class)


/*Route::middleware(['set-dynamic-database'])->group(function () {
   //Route::post('/login', [LoginController::class, 'login'])->middleware(SetDynamicDatabase::class);
    Route::get('{any}', [HomeController::class, 'index'])->name('index')->middleware(SetDynamicDatabase::class);
});*/


Route::post('/login', [LoginController::class, 'login'])->middleware(SetDynamicDatabase::class);

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');


Route::post('/goal-sales-create', [PostController::class, 'createPost'])->middleware(SetDynamicDatabase::class);

