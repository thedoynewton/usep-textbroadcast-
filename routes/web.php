<?php

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

    // Apply the role middleware using the full class name
    Route::middleware([RoleMiddleware::class . ':admin'])->group(function () {
        Route::get('/user-management', [UserManagementController::class, 'index'])->name('user-management');
        Route::post('/user-management/update-role/{id}', [UserManagementController::class, 'updateRole'])->name('user-management.updateRole');
        Route::delete('/user-management/remove-role/{id}', [UserManagementController::class, 'removeRole'])->name('user-management.removeRole');
    });
});

require __DIR__ . '/auth.php';
