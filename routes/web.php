<?php

use App\Http\Controllers\AppManagementController;
use App\Http\Controllers\FilterController;
use App\Http\Controllers\MessagesController;
use App\Http\Controllers\MessageTemplateController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\RoleMiddleware; // Import your middleware

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Apply role middleware for both admin and subadmin roles
    Route::middleware([RoleMiddleware::class . ':admin,subadmin'])->group(function () {
        Route::resource('messages', MessagesController::class);
        // Fetch all colleges
        Route::get('/api/colleges/{campusId}', [FilterController::class, 'getCollegesByCampus']);

        // Fetch all programs based on selected college
        Route::get('/api/programs/{collegeId}', [FilterController::class, 'getPrograms']);

        // Fetch all majors based on selected program
        Route::get('/api/majors/{programId}', [FilterController::class, 'getMajors']);

        // Fetch all years
        Route::get('/api/years', [FilterController::class, 'getYears']);

        // Fetch all offices based on selected campus
        Route::get('/api/offices/{campusId}', [FilterController::class, 'getOfficesByCampus']);

        // Fetch all types based on selected office
        Route::get('/api/types/{officeId}', [FilterController::class, 'getTypes']);

        // Fetch recipient count dynamically based on tab and campus
        Route::get('/api/recipient-count', [FilterController::class, 'getRecipientCount']);


    });

    // Apply the role middleware for only admin
    Route::middleware([RoleMiddleware::class . ':admin'])->group(function () {
        Route::get('/user-management', [UserManagementController::class, 'index'])->name('user-management');
        Route::post('/user-management/add', [UserManagementController::class, 'addUser'])->name('user-management.addUser');
        Route::post('/user-management/update-role/{id}', [UserManagementController::class, 'updateRole'])->name('user-management.updateRole');
        Route::delete('/user-management/remove-role/{id}', [UserManagementController::class, 'removeRole'])->name('user-management.removeRole');

        Route::resource('app-management', AppManagementController::class);

        // Message Templates CRUD Routes
        Route::resource('message-templates', MessageTemplateController::class);
    });
});

require __DIR__ . '/auth.php';
