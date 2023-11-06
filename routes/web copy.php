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
    SurveysController,
    SurveysComposeController,
    SettingsApiKeysController,
    GoogleDriveController,
    DropboxController,
    ClarifaiImageController,
    ScenexImageController
};
use App\Http\Middleware\SetDynamicDatabase;

/*/****************************************************
 * Web Routes
/*****************************************************/

// Authentication Routes
Auth::routes();

// Language Translation
Route::get('index/{locale}', [HomeController::class, 'lang']);

// Root
Route::get('/', [HomeController::class, 'root'])->name('root');


/****************************************************
 * START Auth routes
****************************************************/
Route::middleware(['auth'])->group(function () {
    /****************************************************
     * START User Profile & Password Update
    ****************************************************/
    Route::prefix('user')->group(function () {
        Route::post('/update-profile/{id}', [HomeController::class, 'updateProfile'])->name('updateProfileURL');
        Route::post('/update-password/{id}', [HomeController::class, 'updatePassword'])->name('updatePasswordURL');
    });
    Route::get('/profile/{id?}', [SettingsUserController::class, 'show'])->name('profileShowURL');
    /*Route::prefix('user')->middleware('auth')->group(function () {
        Route::post('update-profile/{id}', [HomeController::class, 'updateProfile'])->name('updateProfileURL');
        Route::post('update-password/{id}', [HomeController::class, 'updatePassword'])->name('updatePasswordURL');
    });*/

    // TODO profile-settings.blade.php
    //Route::get('/profile/settings', [ProfileController::class, 'settings'])->middleware('auth');
    /****************************************************
     * END User Profile & Password Update
    ****************************************************/

    /****************************************************
     * START Goal Sales Routes
    ****************************************************/
    Route::get('/goal-sales', [GoalSalesController::class, 'index'])->name('goalSalesIndexURL');
    Route::get('/goal-sales/settings', [GoalSalesController::class, 'settings'])->name('goalSalesSettingsEditURL');
    Route::get('/goal-sales/form/{meantime?}/{companyId?}/{purpose?}', [GoalSalesController::class, 'edit'])->name('goalSalesEditURL');// view form

    Route::post('/goal-sales/post/{meantime?}/{companyId?}', [GoalSalesController::class, 'createOrUpdate'])->name('goalSalesCreateOrUpdateURL'); // update or store

    Route::post('/goal-sales/analytic-mode', [GoalSalesController::class, 'analyticMode'])->name('goalSalesAnalyticModeURL');
    Route::post('/goal-sales/slide-mode', [GoalSalesController::class, 'slideMode'])->name('goalSalesslideModeURL');
    Route::post('/goal-sales/default-mode', [GoalSalesController::class, 'defaultMode'])->name('goalSalesDefaultModeURL');
    /****************************************************
     * END Goal Sales Routes
    ****************************************************/

    /****************************************************
     * START Goal Results Routes
    ****************************************************/
    // TODO
    //Route::get('/goal-results', [GoalResultsController::class, 'index'])->name('goalResultsIndexURL');
    /****************************************************
     * END Goal Results Routes
    ****************************************************/

    /****************************************************
     * START Surveys Routes
    ****************************************************/
    Route::get('/surveys/listing', [SurveysController::class, 'index'])->name('surveysIndexURL');
    Route::get('/surveys/{id?}', [SurveysController::class, 'show'])->name('surveysShowURL');

    // Form url to create
    Route::get('/surveys/create', [SurveysController::class, 'create'])->name('surveysCreateURL');
    Route::get('/surveys/edit/{id?}', [SurveysController::class, 'edit'])->name('surveysEditURL');

    // Ajax Store / Update survey
    Route::post('/surveys/store/{id?}', [SurveysController::class, 'createOrUpdate'])->name('surveysCreateOrUpdateURL');

        /****************************************************
         * START Surveys Compose Routes
        ****************************************************/
        // Listing forms
        Route::get('/surveys/compose/listing', [SurveysComposeController::class, 'index'])->name('surveysComposeIndexURL');

        // Form url to create
        Route::get('/surveys/compose/create/{type?}', [SurveysComposeController::class, 'create'])->name('surveysComposeCreateURL')->where('type', 'custom|default');

        // Edit the custom form
        Route::get('/surveys/compose/edit/{id?}', [SurveysComposeController::class, 'edit'])->name('surveysComposeEditURL');

        // Show the custom form
        Route::get('/surveys/compose/show/{id?}', [SurveysComposeController::class, 'show'])->name('surveysComposeShowURL');

        // Ajax Create or Update the custom form
        //Route::post('/surveys/compose/store', [SurveysComposeController::class, 'create'])->name('surveysComposeStoreURL');
        Route::post('/surveys/compose/post/{id?}', [SurveysComposeController::class, 'createOrUpdate'])->name('surveysComposeCreateOrUpdateURL');

        // Ajax toggle status
        Route::post('/surveys/compose/toggle-status/{id?}/{status?}', [SurveysComposeController::class, 'toggleStatus'])->name('surveysComposeToggleStatusURL');
        /****************************************************
         * START Surveys Compose Routes
        ****************************************************/

    /****************************************************
     * END Surveys Routes
    ****************************************************/

    /****************************************************
     * START Admin Settings
    ***************************************************/
    Route::middleware(['admin'])->group(function () {
        Route::get('/settings', [SettingsAccountController::class, 'index'])->name('settingsIndexURL');

        // Subscription and form related with the primary user
        Route::get('/settings/account', [SettingsAccountController::class, 'show'])->name('settingsAccountShowURL');
        Route::post('/settings/account/store', [SettingsAccountController::class, 'storeAccount'])->name('settingsAccountCreateOrUpdateURL');

        // API Key, Files Manager, Sinc Database (sales, companies, departments)
        Route::get('/settings/api-keys', [SettingsApiKeysController::class, 'index'])->name('settingsApiKeysURL');

        Route::get('/settings/database', [SettingsDatabaseController::class, 'index'])->name('settingsDatabaseIndexURL');
            Route::put('/settings/departments/store', [SettingsDatabaseController::class, 'updateDepartments'])->name('settingsDepartmentsUpdateURL');
            Route::put('/settings/companies/store', [SettingsDatabaseController::class, 'updateCompanies'])->name('settingsCompaniesUpdateURL');

        // User Settings
        Route::get('/settings/users', [SettingsUserController::class, 'index'])->name('settingsUsersIndexURL');
        Route::post('/settings/users/store', [SettingsUserController::class, 'create']);
        Route::post('/settings/users/update/{id}', [SettingsUserController::class, 'update']);
        Route::get('/settings/users/modal-form/{id?}', [SettingsUserController::class, 'getUserModalContent']);

        // TODO Security Settings
        //Route::get('/settings/security', [SettingsSecurityController::class, 'index'])->name('settingsSecurityIndexURL');

        // Storage APIs
        //Route::get('/settings/googledrive', [GoogleDriveController::class, 'files'])->name('GoogleDriveFilesURL');
        Route::get('/settings/dropbox', [DropboxController::class, 'index'])->name('DropboxIndexURL');
        Route::get('/settings/dropbox/browse/{path}', [DropboxController::class, 'browseFolder'])->name('DropboxBrowseFolderURL');

        // AI APIs
        Route::post('/settings/clarifai', [ClarifaiImageController::class, 'submit'])->name('ClarifaiSubmitURL');
        Route::post('/settings/scenex', [ScenexImageController::class, 'submit'])->name('ScenexSubmitURL');


    });
    /****************************************************
     * END Admin Settings
    ****************************************************/

    /****************************************************
     * START File Upload
    ***************************************************/
    Route::post('/upload/avatar', [UploadController::class, 'uploadAvatar']);
    Route::post('/upload/cover', [UploadController::class, 'uploadCover']);
    Route::post('/upload/logo', [UploadController::class, 'uploadLogo']);
    Route::delete('/upload/logo', [UploadController::class, 'deleteLogo']);
    /****************************************************
     * END File Upload
    ***************************************************/
});
/****************************************************
 * END Auth routes
****************************************************/

// Authentication with Dynamic Database
Route::middleware([SetDynamicDatabase::class])->group(function () {
    Route::post('/login', [LoginController::class, 'login']);
});

// Login and Logout
Route::post('/login/check-databases', [LoginController::class, 'checkDatabases'])->name('checkDatabasesURL');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Catch-All Route - should be the last route
Route::get('{any}', [HomeController::class, 'index'])->where('any', '.*')->name('index');
