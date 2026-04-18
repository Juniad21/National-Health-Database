<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Patient\PatientDashboardController;
use App\Http\Controllers\Doctor\DoctorDashboardController;
use App\Http\Controllers\Hospital\HospitalDashboardController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [PatientDashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    // Patient Routes
    Route::get('/patient/dashboard', [PatientDashboardController::class, 'index'])->name('patient.dashboard');
    Route::get('/patient/scheduling', [PatientDashboardController::class, 'scheduling'])->name('patient.scheduling');
    Route::post('/patient/scheduling', [PatientDashboardController::class, 'storeAppointment'])->name('patient.appointment.store');
    Route::get('/patient/medical-records', [PatientDashboardController::class, 'medicalRecords'])->name('patient.medical_records');
    Route::get('/patient/consents', [PatientDashboardController::class, 'consents'])->name('patient.consents');
    Route::post('/patient/consents', [PatientDashboardController::class, 'updateConsent'])->name('patient.consent.update');
    Route::match(['get', 'post'], '/patient/symptoms', [PatientDashboardController::class, 'symptomAssessment'])->name('patient.symptoms');

    // New Feature Routes for Patient
    Route::post('/patient/access-requests/{id}/approve', [PatientDashboardController::class, 'approveAccessRequest'])->name('patient.access_requests.approve');
    Route::post('/patient/access-requests/{id}/reject', [PatientDashboardController::class, 'rejectAccessRequest'])->name('patient.access_requests.reject');
    Route::post('/patient/vaccinations/{id}/mark-taken', [PatientDashboardController::class, 'markVaccineTaken'])->name('patient.vaccinations.mark_taken');
    Route::post('/patient/emergency/trigger', [PatientDashboardController::class, 'triggerEmergency'])->name('patient.emergency.trigger');
    Route::post('/patient/evaluation', [PatientDashboardController::class, 'storeEvaluation'])->name('patient.evaluation.store');

    // Doctor Routes
    Route::get('/doctor/dashboard', [DoctorDashboardController::class, 'index'])->name('doctor.dashboard');
    Route::get('/doctor/patient/{id}', [DoctorDashboardController::class, 'viewPatient'])->name('doctor.patient.view');
    Route::post('/doctor/patient/{id}/request-access', [DoctorDashboardController::class, 'requestAccess'])->name('doctor.patient.request_access');
    Route::get('/doctor/consultation/{patient_id}', [DoctorDashboardController::class, 'consultation'])->name('doctor.consultation');
    Route::post('/doctor/consultation/{patient_id}', [DoctorDashboardController::class, 'storeConsultation'])->name('doctor.consultation.store');
    Route::post('/doctor/queue/{appointment_id}/visit', [DoctorDashboardController::class, 'markVisited'])->name('doctor.queue.visit');

    // Hospital Routes
    Route::get('/hospital/dashboard', [HospitalDashboardController::class, 'index'])->name('hospital.dashboard');
    Route::post('/hospital/lab-orders/{id}/complete', [HospitalDashboardController::class, 'completeLabOrder'])->name('hospital.lab_orders.complete');
    Route::post('/hospital/resources/{id}/update', [HospitalDashboardController::class, 'updateResource'])->name('hospital.resources.update');
    Route::post('/hospital/emergencies/{id}/dispatch', [HospitalDashboardController::class, 'dispatchAmbulance'])->name('hospital.emergencies.dispatch');
    Route::post('/hospital/emergencies/{id}/resolve', [HospitalDashboardController::class, 'resolveEmergency'])->name('hospital.emergencies.resolve');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';