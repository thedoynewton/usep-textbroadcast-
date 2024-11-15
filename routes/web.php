<?php

use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\AppManagementController;
use App\Http\Controllers\CreditBalanceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DataImportController;
use App\Http\Controllers\FilterController;
use App\Http\Controllers\MessageCategoryController;
use App\Http\Controllers\MessagesController;
use App\Http\Controllers\MessageTemplateController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TemplatesController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\RoleMiddleware; // Import your middleware

// Redirect root to login
Route::get('/', fn() => redirect()->route('login'));

// Dashboard Route
Route::get('/dashboard', fn() => view('dashboard'))
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Authenticated Routes
Route::middleware('auth')->group(function () {

    // Profile Routes
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

    // Admin and Subadmin Routes (Role-Based)
    Route::middleware([RoleMiddleware::class . ':admin,subadmin'])->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/recipients', [DashboardController::class, 'getRecipients']);
        Route::get('/recipients/{message_log_id}', [DashboardController::class, 'getRecipientsByMessageLog'])->name('recipients.byMessageLog');

        Route::get('/generate-report', [ReportController::class, 'generateReport'])->name('generateReport');
        Route::get('/generate-report', [ReportController::class, 'generateReport'])->name('generateReport');

        // Messages CRUD Routes
        Route::resource('messages', MessagesController::class);
        Route::patch('/messages/cancel/{id}', [MessagesController::class, 'cancel'])->name('messages.cancel');

        Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index')->middleware('auth');

        Route::get('/app-management/search', [AppManagementController::class, 'search'])->name('app-management.search');
        Route::post('/contacts/{id}/update-number', [AppManagementController::class, 'updateContactNumber'])->name('contacts.update-number');
        
        Route::resource('templates', TemplatesController::class);
        
        // Message Templates CRUD Routes
        Route::resource('message-templates', MessageTemplateController::class);
        // Message Categories CRUD Routes
        Route::resource('message-categories', MessageCategoryController::class);

        // FilterController Routes (API)
        Route::prefix('/api')->group(function () {
            Route::get('/colleges/{campusId}', [FilterController::class, 'getCollegesByCampus']);
            Route::get('/programs/{collegeId}', [FilterController::class, 'getPrograms']);
            Route::get('/majors/{programId}', [FilterController::class, 'getMajors']);
            Route::get('/years', [FilterController::class, 'getYears']);
            Route::get('/offices/{campusId}', [FilterController::class, 'getOfficesByCampus']);
            Route::get('/types/{officeId}', [FilterController::class, 'getTypes']);
            Route::get('/recipient-count', [FilterController::class, 'getRecipientCount']);
            Route::get('/statuses', [FilterController::class, 'getStatuses']);
            Route::get('/analytics/message-overview', [AnalyticsController::class, 'getMessageOverviewData']);
            Route::get('/analytics/costs-overview', [AnalyticsController::class, 'getCostsOverviewData']);
        });
    });

    // Admin-Only Routes
    Route::middleware([RoleMiddleware::class . ':admin'])->group(function () {

        // User Management Routes
        Route::prefix('user-management')->group(function () {
            Route::get('/', [UserManagementController::class, 'index'])->name('user-management');
            Route::post('/add', [UserManagementController::class, 'addUser'])->name('user-management.addUser');
            Route::post('/update-role/{id}', [UserManagementController::class, 'updateRole'])->name('user-management.updateRole');
            Route::delete('/remove-role/{id}', [UserManagementController::class, 'removeRole'])->name('user-management.removeRole');
        });

        // App Management Routes
        Route::resource('app-management', AppManagementController::class);

        Route::post('/campuses/add', [DataImportController::class, 'addCampus'])->name('campuses.add');
        Route::post('/campuses/update', [DataImportController::class, 'updateCampus'])->name('campuses.update');

        // Route for retrieving credit balance (for AJAX requests)
        Route::get('/credit-balance/get', [CreditBalanceController::class, 'getCreditBalance'])->name('credit-balance.get');
        // Route for updating the credit balance
        Route::post('/credit-balance/update', [CreditBalanceController::class, 'updateCreditBalance'])->name('credit-balance.update');

        //DB Connection
        Route::get('/data-import', [DataImportController::class, 'showImportForm'])->name('data-import.form');
        Route::post('/import-college', [DataImportController::class, 'importCollegeData'])->name('import.college');
        Route::post('/import-programs', [DataImportController::class, 'importProgramData'])->name('import.programs');
        Route::post('/import-majors', [DataImportController::class, 'importMajorData'])->name('import.majors');
        Route::post('/import-years', [DataImportController::class, 'importYearData'])->name('import.years');
        Route::post('/import-students', [DataImportController::class, 'importStudentData'])->name('import.students');
        Route::get('/app-management/db-connection', [DataImportController::class, 'showDBConnection'])->name('app-management.db-connection');
    });
});

// Authentication Routes
require __DIR__ . '/auth.php';
