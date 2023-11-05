<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DropboxController;
use App\Http\Controllers\GoogleDriveController;
use App\Http\Controllers\ClarifaiImageController;
use App\Http\Controllers\SettingsDatabaseController;


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Sysmo API
Route::get('/process-sysmo-api/{meantime}/{database?}', [SettingsDatabaseController::class, 'updateSales'])->name('updateSales');

// Google Drive API
Route::get('/google-drive/redirect', [GoogleDriveController::class, 'redirect'])->name('GoogleDriveRedirectURL');
Route::get('/google-drive/callback', [GoogleDriveController::class, 'callback'])->name('GoogleDriveCallbackURL');
Route::get('/google-drive/deauthorize', [GoogleDriveController::class, 'deauthorize'])->name('GoogleDriveDeauthorizeURL');

Route::post('/google-drive/upload', [GoogleDriveController::class, 'upload'])->name('GoogleDriveUploadURL');
Route::delete('/google-drive/delete/{fileId}', [GoogleDriveController::class, 'delete'])->name('GoogleDriveDeleteURL');

// Dropbox API
//Route::get('/dropbox/redirect', [DropboxController::class, 'authorizeDropbox'])->name('DropboxRedirectURL');
Route::get('/dropbox/callback', [DropboxController::class, 'callback'])->name('DropboxCallbackURL');
Route::get('/dropbox/authorize', [DropboxController::class, 'authorizeDropbox'])->name('DropboxAuthorizeURL');
Route::get('/dropbox/deauthorize', [DropboxController::class, 'deauthorizeDropbox'])->name('DropboxDeauthorizeURL');
Route::post('/dropbox/upload', [DropboxController::class, 'uploadFile'])->name('DropboxUploadURL');
Route::post('/dropbox/delete', [DropboxController::class, 'deleteFile'])->name('DropboxDeleteURL');
Route::get('/dropbox/delete-folder/{path?}', [DropboxController::class, 'deleteFolder'])->name('DropboxDeleteFolderURL');

// Clarifai API
// SceneX API
