<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Patient\PatientDashboardController;
use App\Http\Controllers\Doctor\DoctorDashboardController;
use App\Http\Controllers\Hospital\HospitalDashboardController;
use App\Http\Controllers\Hospital\HospitalAuditLogController;
use App\Http\Controllers\Hospital\HospitalBillingController;

// 1. Redirect Home to Login
Route::get('/', function () {
    return redirect()->route('login');
});

// 2. Smart Role-Based Dashboard
Route::get('/dashboard', function () {
    $role = auth()->user()->role;

    if ($role === 'doctor') {
        return redirect()->route('doctor.dashboard');
    } elseif ($role === 'hospital') {
        return redirect()->route('hospital.dashboard');
    }

    return redirect()->route('patient.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// 3. Authenticated Routes Group
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
    Route::post('/doctor/patient/{patient_id}/medical-record', [DoctorDashboardController::class, 'storeMedicalRecord'])->name('doctor.medical_record.store');
    Route::get('/doctor/consultation/{patient_id}', [DoctorDashboardController::class, 'consultation'])->name('doctor.consultation');
    Route::post('/doctor/consultation/{patient_id}', [DoctorDashboardController::class, 'storeConsultation'])->name('doctor.consultation.store');
    Route::post('/doctor/queue/{appointment_id}/visit', [DoctorDashboardController::class, 'markVisited'])->name('doctor.queue.visit');
    Route::post('/doctor/appointments/{appointment_id}/approve', [DoctorDashboardController::class, 'approveAppointment'])->name('doctor.appointment.approve');

    // Hospital Routes
    Route::get('/hospital/dashboard', [HospitalDashboardController::class, 'index'])->name('hospital.dashboard');
    Route::get('/hospital/logs', [HospitalAuditLogController::class, 'index'])->name('hospital.logs');
    Route::post('/hospital/lab-orders/{id}/complete', [HospitalDashboardController::class, 'completeLabOrder'])->name('hospital.lab_orders.complete');
    Route::post('/hospital/resources/{id}/update', [HospitalDashboardController::class, 'updateResource'])->name('hospital.resources.update');
    Route::post('/hospital/emergencies/{id}/dispatch', [HospitalDashboardController::class, 'dispatchAmbulance'])->name('hospital.emergencies.dispatch');
    Route::post('/hospital/emergencies/{id}/resolve', [HospitalDashboardController::class, 'resolveEmergency'])->name('hospital.emergencies.resolve');

    // Hospital Billing Routes
    Route::get('/hospital/billing', [HospitalBillingController::class, 'index'])->name('hospital.billing.index');
    Route::get('/hospital/billing/create', [HospitalBillingController::class, 'create'])->name('hospital.billing.create');
    Route::post('/hospital/billing', [HospitalBillingController::class, 'store'])->name('hospital.billing.store');
    Route::post('/hospital/billing/{id}/payment', [HospitalBillingController::class, 'updatePayment'])->name('hospital.billing.payment.update');
    Route::get('/hospital/billing/claims', [HospitalBillingController::class, 'claims'])->name('hospital.billing.claims');
    Route::post('/hospital/billing/{bill_id}/claim', [HospitalBillingController::class, 'submitClaim'])->name('hospital.billing.claim.submit');
    Route::post('/hospital/billing/claims/{id}/status', [HospitalBillingController::class, 'updateClaimStatus'])->name('hospital.billing.claim.status.update');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';