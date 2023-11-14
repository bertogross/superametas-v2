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
    SurveysTemplatesController,
    //SurveysComposeController,
    //SurveyExecutionController,
    //SurveyTermController,
    SettingsApiKeysController,
    DropboxController,
    ClarifaiImageController,
    ScenexImageController
};
use App\Http\Middleware\SetDynamicDatabase;

// Authentication Routes
Auth::routes();

// Language Translation
Route::get('index/{locale}', [HomeController::class, 'lang']);

// Root
Route::get('/', [HomeController::class, 'root'])->name('root');

Route::middleware(['auth'])->group(function () {
    // User Profile & Password Update
    Route::prefix('user')->group(function () {
        Route::post('/update-profile/{id}', [ProfileController::class, 'updateProfile'])->name('updateProfileURL');
        Route::post('/update-password/{id}', [ProfileController::class, 'updatePassword'])->name('updatePasswordURL');
    });
    Route::get('/profile/{id?}', [ProfileController::class, 'show'])->name('profileShowURL');

    // Goal Sales Routes
    Route::prefix('goal-sales')->group(function () {
        Route::get('/', [GoalSalesController::class, 'index'])->name('goalSalesIndexURL');
        Route::get('/settings', [GoalSalesController::class, 'settings'])->name('goalSalesSettingsEditURL');
        Route::get('/edit/{meantime?}/{companyId?}/{purpose?}', [GoalSalesController::class, 'edit'])->name('goalSalesEditURL');
        Route::post('/store/{meantime?}/{companyId?}', [GoalSalesController::class, 'storeOrUpdate'])->name('goalSalesStoreOrUpdateURL');

        Route::post('/analytic-mode', [GoalSalesController::class, 'analyticMode'])->name('goalSalesAnalyticModeURL');
        Route::post('/slide-mode', [GoalSalesController::class, 'slideMode'])->name('goalSalesSlideModeURL');
        Route::post('/default-mode', [GoalSalesController::class, 'defaultMode'])->name('goalSalesDefaultModeURL');
    });

    // Surveys Routes
    Route::prefix('surveys')->group(function () {
        Route::get('/index', [SurveysController::class, 'index'])->name('surveysIndexURL');
        Route::get('/create', [SurveysController::class, 'create'])->name('surveysCreateURL');
        Route::get('/edit/{id?}', [SurveysController::class, 'edit'])->name('surveysEditURL')->where('id', '[0-9]+');
        Route::get('/{id?}', [SurveysController::class, 'show'])->name('surveysShowURL')->where('id', '[0-9]+');
        Route::post('/store/{id?}', [SurveysController::class, 'storeOrUpdate'])->name('surveysStoreOrUpdateURL');

            //Route::get('/listing', [SurveysTemplatesController::class, 'index'])->name('surveyTemplateIndexURL');
            Route::get('/template/create', [SurveysTemplatesController::class, 'create'])->name('surveysTemplateCreateURL');
            Route::get('/template/edit/{id?}', [SurveysTemplatesController::class, 'edit'])->name('surveyTemplateEditURL')->where('id', '[0-9]+');
            Route::get('/template/show/{id?}', [SurveysTemplatesController::class, 'show'])->name('surveyTemplateShowURL')->where('id', '[0-9]+');
            Route::post('/template/store/{id?}', [SurveysTemplatesController::class, 'storeOrUpdate'])->name('surveysTemplateStoreOrUpdateURL');

            // Form Compose Routes
            /*
            Route::get('/compose/listing', [SurveysComposeController::class, 'index'])->name('surveysComposeIndexURL');
            Route::get('/compose/create/{type?}', [SurveysComposeController::class, 'create'])->name('surveysComposeCreateURL')->where('type', 'custom|default');
            Route::get('/compose/edit/{id?}', [SurveysComposeController::class, 'edit'])->name('surveysComposeEditURL');
            Route::get('/compose/show/{id?}', [SurveysComposeController::class, 'show'])->name('surveysComposeShowURL');
            Route::post('/compose/store/{id?}', [SurveysComposeController::class, 'storeOrUpdate'])->name('surveysComposeStoreOrUpdateURL');
            Route::post('/compose/toggle-status/{id?}/{status?}', [SurveysComposeController::class, 'toggleStatus'])->name('surveysComposeToggleStatusURL');
            */

        // Term Routes
        /*
        Route::get('/terms/listing', [SurveyTermController::class, 'index'])->name('surveysTermsIndexURL');
        Route::get('/terms/create', [SurveyTermController::class, 'create'])->name('surveysTermsCreateURL');
        Route::get('/terms/edit/{id?}', [SurveyTermController::class, 'edit'])->name('surveysTermsEditURL');
        Route::post('/terms/store', [SurveyTermController::class, 'storeOrUpdate'])->name('surveysTermsStoreOrUpdateURL');
        Route::get('/terms/search', [SurveyTermController::class, 'search'])->name('surveysTermsSearchURL');
        */
    });


    // Admin Settings
    Route::middleware(['admin'])->group(function () {
        Route::prefix('settings')->group(function () {
            Route::get('/', [SettingsAccountController::class, 'index'])->name('settingsIndexURL');
            Route::get('/account', [SettingsAccountController::class, 'show'])->name('settingsAccountShowURL');
                Route::post('/account/store', [SettingsAccountController::class, 'storeOrUpdate'])->name('settingsAccountStoreOrUpdateURL');

            Route::get('/api-keys', [SettingsApiKeysController::class, 'index'])->name('settingsApiKeysURL');

            Route::get('/database', [SettingsDatabaseController::class, 'index'])->name('settingsDatabaseIndexURL');
                Route::put('/departments/store', [SettingsDatabaseController::class, 'updateDepartments'])->name('settingsDepartmentsUpdateURL');
                Route::put('/companies/store', [SettingsDatabaseController::class, 'updateCompanies'])->name('settingsCompaniesUpdateURL');

            Route::get('/users', [SettingsUserController::class, 'index'])->name('settingsUsersIndexURL');
            Route::post('/users/store', [SettingsUserController::class, 'create']);
                Route::post('/users/update/{id}', [SettingsUserController::class, 'update']);
                Route::get('/users/modal-form/{id?}', [SettingsUserController::class, 'getUserModalContent']);

            Route::get('/dropbox', [DropboxController::class, 'index'])->name('DropboxIndexURL');
            Route::get('/dropbox/browse/{path}', [DropboxController::class, 'browseFolder'])->name('DropboxBrowseFolderURL');

            Route::post('/clarifai', [ClarifaiImageController::class, 'submit'])->name('ClarifaiSubmitURL');
            Route::post('/scenex', [ScenexImageController::class, 'submit'])->name('ScenexSubmitURL');
        });
    });

    // File Upload Routes
    Route::prefix('upload')->group(function () {
        Route::post('/avatar', [UploadController::class, 'uploadAvatar'])->name('uploadAvatarURL');
        Route::post('/cover', [UploadController::class, 'uploadCover'])->name('uploadCoverURL');
        Route::post('/logo', [UploadController::class, 'uploadLogo'])->name('uploadLogoURL');
        Route::delete('/logo', [UploadController::class, 'deleteLogo'])->name('deleteLogoURL');
    });
});

// Dynamic Database Middleware for Login
Route::middleware([SetDynamicDatabase::class])->group(function () {
    Route::post('/login', [LoginController::class, 'login']);
});

// Logout Route
Route::post('/login/check-databases', [LoginController::class, 'checkDatabases'])->name('checkDatabasesURL');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::fallback(function () {
    return response()->view('error.auth-404-basic', [], 404);
});

// Catch-All Route
Route::get('{any}', [HomeController::class, 'index'])->where('any', '.*')->name('index');
