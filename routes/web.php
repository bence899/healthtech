<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Doctor\DoctorDashboardController;
use App\Http\Controllers\Patient\PatientDashboardController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\Admin\AppointmentController as AdminAppointmentController;
use App\Http\Controllers\Admin\DoctorController as AdminDoctorController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\Doctor\DoctorScheduleController;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Role-specific dashboards
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Admin Routes
    Route::middleware('auth.admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::resource('doctors', AdminDoctorController::class);
        Route::resource('appointments', AdminAppointmentController::class);
        Route::patch('/appointments/{appointment}/status', [AdminAppointmentController::class, 'updateStatus'])
            ->name('appointments.update-status');
    });

    // Doctor Routes
    Route::middleware(['auth.doctor'])->prefix('doctor')->name('doctor.')->group(function () {
        Route::get('/dashboard', [DoctorDashboardController::class, 'index'])->name('dashboard');
        Route::get('/schedule', [DoctorScheduleController::class, 'index'])->name('schedule.index');
        Route::post('/schedule', [DoctorScheduleController::class, 'store'])->name('schedule.store');
        Route::put('/schedule/{schedule}', [DoctorScheduleController::class, 'update'])->name('schedule.update');
        Route::delete('/schedule/{schedule}', [DoctorScheduleController::class, 'destroy'])->name('schedule.destroy');
        Route::post('/appointments/{appointment}/respond', [DoctorDashboardController::class, 'respondToAppointment'])
            ->name('appointments.respond');
        Route::get('/appointments', [DoctorDashboardController::class, 'appointments'])
            ->name('appointments.index');
    });

    // Patient Routes
    Route::middleware(['auth.patient'])->prefix('patient')->name('patient.')->group(function () {
        Route::get('/dashboard', [PatientDashboardController::class, 'index'])->name('dashboard');
        // ... other patient routes
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Appointment routes
    Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments.index');
    Route::get('/appointments/create', [AppointmentController::class, 'create'])->name('appointments.create');
    Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
    Route::patch('/appointments/{appointment}/cancel', [AppointmentController::class, 'cancel'])->name('appointments.cancel');

    // Document routes
    Route::resource('documents', DocumentController::class);
    Route::get('documents/{document}/download', [DocumentController::class, 'download'])
        ->name('documents.download');
});
require __DIR__.'/auth.php';
