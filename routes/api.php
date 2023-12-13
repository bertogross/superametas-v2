<?php

use Illuminate\Http\Request;
use App\Models\SettingsDatabase;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ZohoController;
use App\Http\Controllers\DropboxController;
use App\Http\Controllers\SurveysController;
use App\Http\Controllers\GoogleDriveController;
use App\Http\Controllers\ClarifaiImageController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\SettingsStripeController;
use App\Http\Controllers\SettingsDatabaseController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Stripe API
Route::post('/stripe/subscription', [SettingsStripeController::class, 'createStripeSession'])->name('stripeSubscriptionURL');
Route::post('/stripe/subscription/details', [SettingsStripeController::class, 'updateSubscriptionItem'])->name('stripeSubscriptionDetailsURL');
Route::post('/stripe/cart/addon', [SettingsStripeController::class, 'addonCart'])->name('stripeCartAddonURL');
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook']);

// Sysmo API
Route::get('/process-sysmo-api/{meantime?}/{database?}', [SettingsDatabase::class, 'updateSalesFromSysmo'])->name('updateSalesFromSysmoURL');

// ZOHO server-based applications
// https://www.zoho.com/mail/help/api/overview.html
Route::get('/zoho/auth', [ZohoController::class, 'authenticate'])->name('authenticateURL');
Route::get('/zoho/callback', [ZohoController::class, 'callback'])->name('callbackURL');

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
    // TODO

// SceneX API
    // TODO
