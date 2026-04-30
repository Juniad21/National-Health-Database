```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Patient\PatientDashboardController;
use App\Http\Controllers\Doctor\DoctorDashboardController;
use App\Http\Controllers\Hospital\HospitalDashboardController;
use App\Http\Controllers\Hospital\HospitalAuditLogController;
use App\Http\Controllers\Hospital\HospitalBillingController;
use App\Http\Controllers\Admin\DuplicateRecordController;

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

    // ==========================================
    // PATIENT ROUTES
    // ==========================================
    Route::prefix('patient')->name('patient.')->group(function () {
        Route::get('/dashboard', [PatientDashboardController::class, 'index'])->name('dashboard');
        Route::get('/scheduling', [PatientDashboardController::class, 'scheduling'])->name('scheduling');
        Route::post('/scheduling', [PatientDashboardController::class, 'storeAppointment'])->name('appointment.store');
        Route::get('/medical-records', [PatientDashboardController::class, 'medicalRecords'])->name('medical_records');
        Route::get('/consents', [PatientDashboardController::class, 'consents'])->name('consents');
        Route::post('/consents', [PatientDashboardController::class, 'updateConsent'])->name('consent.update');
        Route::match(['get', 'post'], '/symptoms', [PatientDashboardController::class, 'symptomAssessment'])->name('symptoms');

        Route::post('/access-requests/{id}/approve', [PatientDashboardController::class, 'approveAccessRequest'])->name('access_requests.approve');
        Route::post('/access-requests/{id}/reject', [PatientDashboardController::class, 'rejectAccessRequest'])->name('access_requests.reject');
        Route::post('/vaccinations/{id}/mark-taken', [PatientDashboardController::class, 'markVaccineTaken'])->name('vaccinations.mark_taken');
        Route::post('/emergency/trigger', [PatientDashboardController::class, 'triggerEmergency'])->name('emergency.trigger');
        Route::post('/evaluation', [PatientDashboardController::class, 'storeEvaluation'])->name('evaluation.store');
    });

    // ==========================================
    // DOCTOR ROUTES
    // ==========================================
    Route::prefix('doctor')->name('doctor.')->group(function () {
        Route::get('/dashboard', [DoctorDashboardController::class, 'index'])->name('dashboard');
        Route::get('/patient/{id}', [DoctorDashboardController::class, 'viewPatient'])->name('patient.view');
        Route::post('/patient/{id}/request-access', [DoctorDashboardController::class, 'requestAccess'])->name('patient.request_access');
        Route::post('/patient/{patient_id}/medical-record', [DoctorDashboardController::class, 'storeMedicalRecord'])->name('medical_record.store');
        Route::get('/consultation/{patient_id}', [DoctorDashboardController::class, 'consultation'])->name('consultation');
        Route::post('/consultation/{patient_id}', [DoctorDashboardController::class, 'storeConsultation'])->name('consultation.store');
        Route::post('/queue/{appointment_id}/visit', [DoctorDashboardController::class, 'markVisited'])->name('queue.visit');
        Route::post('/appointments/{appointment_id}/approve', [DoctorDashboardController::class, 'approveAppointment'])->name('appointment.approve');
    });

    // ==========================================
    // HOSPITAL ROUTES
    // ==========================================
    Route::prefix('hospital')->name('hospital.')->group(function () {
        Route::get('/dashboard', [HospitalDashboardController::class, 'index'])->name('dashboard');
        Route::get('/logs', [HospitalAuditLogController::class, 'index'])->name('logs');

        Route::post('/lab-orders/{id}/complete', [HospitalDashboardController::class, 'completeLabOrder'])->name('lab_orders.complete');
        Route::post('/resources/{id}/update', [HospitalDashboardController::class, 'updateResource'])->name('resources.update');
        Route::post('/emergencies/{id}/dispatch', [HospitalDashboardController::class, 'dispatchAmbulance'])->name('emergencies.dispatch');
        Route::post('/emergencies/{id}/resolve', [HospitalDashboardController::class, 'resolveEmergency'])->name('emergencies.resolve');

        // Hospital Billing & Claims
        Route::get('/billing', [HospitalBillingController::class, 'index'])->name('billing.index');
        Route::get('/billing/create', [HospitalBillingController::class, 'create'])->name('billing.create');
        Route::post('/billing', [HospitalBillingController::class, 'store'])->name('billing.store');
        Route::post('/billing/{id}/payment', [HospitalBillingController::class, 'updatePayment'])->name('billing.payment.update');

        Route::get('/billing/claims', [HospitalBillingController::class, 'claims'])->name('billing.claims');
        Route::post('/billing/{bill_id}/claim', [HospitalBillingController::class, 'submitClaim'])->name('billing.claim.submit');
        Route::post('/billing/claims/{id}/status', [HospitalBillingController::class, 'updateClaimStatus'])->name('billing.claim.status.update');

        // Duplicate Records
        Route::get('/duplicates', [DuplicateRecordController::class, 'index'])->name('duplicates.index');
        Route::get('/duplicates/compare/{id1}/{id2}', [DuplicateRecordController::class, 'compare'])->name('duplicates.compare');
        Route::post('/duplicates/merge', [DuplicateRecordController::class, 'merge'])->name('duplicates.merge');
    });
});

// 4. Standard Profile Routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';

