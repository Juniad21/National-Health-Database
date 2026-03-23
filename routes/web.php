<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

use App\Http\Controllers\PatientDashboardController;

Route::get('/dashboard', [PatientDashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/patient/dashboard', [PatientDashboardController::class, 'index'])->name('patient.dashboard');
    Route::get('/patient/scheduling', [PatientDashboardController::class, 'scheduling'])->name('patient.scheduling');
    Route::post('/patient/scheduling', [PatientDashboardController::class, 'storeAppointment'])->name('patient.appointment.store');
    Route::get('/patient/medical-records', [PatientDashboardController::class, 'medicalRecords'])->name('patient.medical_records');
    Route::get('/patient/consents', [PatientDashboardController::class, 'consents'])->name('patient.consents');
    Route::post('/patient/consents', [PatientDashboardController::class, 'updateConsent'])->name('patient.consent.update');
    Route::match(['get', 'post'], '/patient/symptoms', [PatientDashboardController::class, 'symptomAssessment'])->name('patient.symptoms');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
