<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\{
    Auth\LoginController,
    HomeController,
    ProfileController,
    UserUploadController,
    SettingsUserController,
    SettingsDatabaseController,
    SettingsAccountController,
    GoalSalesController,
    SurveysController,
    SurveysTemplatesController,
    SurveyTermsController,
    SurveysResponsesController,
    //SurveysComposeController,
    //SurveyExecutionController,
    //SurveyTermsController,
    SurveysAssignmentsController,
    SettingsApiKeysController,
    SettingsStripeController,
    AttachmentsController,
    StripeWebhookController,
    DropboxController,
    ClarifaiImageController,
    ScenexImageController
};
use App\Http\Middleware\SetDynamicDatabase;

// Authentication Routes
Auth::routes();

// Language Translation
Route::get('index/{locale}', [HomeController::class, 'lang']);

//
Route::middleware(['auth'])->group(function () {

    // Root
    //Route::get('/', [HomeController::class, 'root'])->name('root');
    Route::get('/', [GoalSalesController::class, 'index'])->name('root');

    // User Profile & Password Update
    Route::prefix('user')->group(function () {
        Route::post('/update-profile/{id}', [ProfileController::class, 'updateProfile'])->name('updateProfileURL');
        Route::post('/update-password/{id}', [ProfileController::class, 'updatePassword'])->name('updatePasswordURL');
    });
    Route::get('/profile/{id?}', [ProfileController::class, 'index'])->name('profileShowURL');
    Route::post('/profile/layout-mode', [ProfileController::class, 'ChangeLayoutMode'])->name('profileChangeLayoutModeURL');

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
        Route::get('/', [SurveysController::class, 'index'])->name('surveysIndexURL');
        Route::get('/create', [SurveysController::class, 'create'])->name('surveysCreateURL');
        Route::get('/edit/{id?}', [SurveysController::class, 'edit'])->name('surveysEditURL')->where('id', '[0-9]+');
        Route::get('/show/{id?}', [SurveysController::class, 'show'])->name('surveysShowURL')->where('id', '[0-9]+');
        Route::post('/store/{id?}', [SurveysController::class, 'storeOrUpdate'])->name('surveysStoreOrUpdateURL');
        Route::post('/status', [SurveysController::class, 'changeStatus'])->name('surveysChangeStatusURL');

            //Route::get('/listing', [SurveysTemplatesController::class, 'index'])->name('surveyTemplateIndexURL');
            Route::get('/template/create', [SurveysTemplatesController::class, 'create'])->name('surveysTemplateCreateURL');
            Route::get('/template/edit/{id?}', [SurveysTemplatesController::class, 'edit'])->name('surveysTemplateEditURL')->where('id', '[0-9]+');
            Route::get('/template/show/{id?}', [SurveysTemplatesController::class, 'show'])->name('surveysTemplateShowURL')->where('id', '[0-9]+');
            Route::post('/template/store/{id?}', [SurveysTemplatesController::class, 'storeOrUpdate'])->name('surveysTemplateStoreOrUpdateURL');

            Route::get('/assignment/{id}', [SurveysAssignmentsController::class, 'show'])->name('assignmentShowURL')->where('id', '[0-9]+');

            Route::get('/assignment/surveyor-form/{id?}', [SurveysAssignmentsController::class, 'formSurveyorAssignment'])->name('formSurveyorAssignmentURL')->where('id', '[0-9]+');
            Route::post('/assignment/surveyor-status', [SurveysAssignmentsController::class, 'changeAssignmentSurveyorStatus'])->name('changeAssignmentSurveyorStatusURL');

            Route::get('/assignment/auditor-form/{id?}', [SurveysAssignmentsController::class, 'formAuditorAssignment'])->name('formAuditorAssignmentURL')->where('id', '[0-9]+');
            Route::post('/assignment/auditor-status', [SurveysAssignmentsController::class, 'changeAssignmentAuditorStatus'])->name('changeAssignmentAuditorStatusURL');

            Route::post('/responses/surveyor/store/{id?}', [SurveysResponsesController::class, 'responsesSurveyorStoreOrUpdate'])->name('responsesSurveyorStoreOrUpdateURL');
            Route::post('/responses/auditor/store/{id?}', [SurveysResponsesController::class, 'responsesAuditorStoreOrUpdate'])->name('responsesAusitorStoreOrUpdateURL');

            // Terms Routes
            Route::get('/terms/listing', [SurveyTermsController::class, 'index'])->name('surveysTermsIndexURL');
            Route::get('/terms/create', [SurveyTermsController::class, 'create'])->name('surveysTermsCreateURL');
            Route::get('/terms/form', [SurveyTermsController::class, 'form'])->name('surveysTermsFormURL');
            Route::get('/terms/edit/{id?}', [SurveyTermsController::class, 'edit'])->name('surveysTermsEditURL');
            Route::post('/terms/store/{id?}', [SurveyTermsController::class, 'storeOrUpdate'])->name('surveysTermsStoreOrUpdateURL');
            Route::get('/terms/search', [SurveyTermsController::class, 'search'])->name('surveysTermsSearchURL');



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

            Route::post('/stripe/subscription', [SettingsStripeController::class, 'createStripeSession'])->name('stripeSubscriptionURL');
            Route::post('/stripe/subscription/details', [SettingsStripeController::class, 'updateSubscriptionItem'])->name('stripeSubscriptionDetailsURL');
            Route::post('/stripe/cart/addon', [SettingsStripeController::class, 'addonCart'])->name('stripeCartAddonURL');
            Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook']);

            Route::get('/dropbox', [DropboxController::class, 'index'])->name('DropboxIndexURL');
            Route::get('/dropbox/browse/{path}', [DropboxController::class, 'browseFolder'])->name('DropboxBrowseFolderURL');

            Route::post('/clarifai', [ClarifaiImageController::class, 'submit'])->name('ClarifaiSubmitURL');
            Route::post('/scenex', [ScenexImageController::class, 'submit'])->name('ScenexSubmitURL');


        });
    });

    // File Upload Routes
    Route::prefix('upload')->group(function () {
        Route::post('/avatar', [UserUploadController::class, 'uploadAvatar'])->name('uploadAvatarURL');
        Route::post('/cover', [UserUploadController::class, 'uploadCover'])->name('uploadCoverURL');
        Route::post('/logo', [UserUploadController::class, 'uploadLogo'])->name('uploadLogoURL');
        Route::delete('/delete/logo', [UserUploadController::class, 'deleteLogo']);

        Route::post('/photo', [AttachmentsController::class, 'uploadPhoto'])->name('uploadPhotoURL');
        Route::delete('/delete/photo/{id?}', [AttachmentsController::class, 'deletePhoto'])->name('deletePhotoURL');

    });
});

// Dynamic Database Middleware for Login
Route::middleware([SetDynamicDatabase::class])->group(function () {
    Route::post('/login', [LoginController::class, 'login']);
    // Route::post('/onboard', [OnboardController::class, 'onboard']);
});

// Logout Route
Route::post('/login/check-databases', [LoginController::class, 'checkDatabases'])->name('checkDatabasesURL');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::fallback(function () {
    return response()->view('error.auth-404-basic', [], 404);
});

// Catch-All Route
Route::get('{any}', [HomeController::class, 'index'])->where('any', '.*')->name('index');
