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
use App\Http\Controllers\Govt\GovtAdminDashboardController;

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
    } elseif ($role === 'govt_admin') {
        return redirect()->route('govt_admin.dashboard');
    }

    return redirect()->route('patient.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// 3. Authenticated Routes Group
Route::middleware(['auth', 'verified'])->group(function () {

    // ==========================================
    // PATIENT ROUTES
    // ==========================================
    Route::prefix('patient')->name('patient.')->middleware('role:patient')->group(function () {
        Route::get('/dashboard', [PatientDashboardController::class, 'index'])->name('dashboard');
        Route::get('/scheduling', [PatientDashboardController::class, 'scheduling'])->name('scheduling');
        Route::post('/scheduling', [PatientDashboardController::class, 'storeAppointment'])->name('appointment.store');
        Route::get('/medical-records', [PatientDashboardController::class, 'medicalRecords'])->name('medical_records');
        Route::get('/bills', [PatientDashboardController::class, 'bills'])->name('bills');
        Route::post('/bills/{id}/pay', [PatientDashboardController::class, 'payBill'])->name('bills.pay');
        Route::get('/consents', [PatientDashboardController::class, 'consents'])->name('consents');
        Route::post('/consents', [PatientDashboardController::class, 'updateConsent'])->name('consent.update');
        Route::match(['get', 'post'], '/symptoms', [PatientDashboardController::class, 'symptomAssessment'])->name('symptoms');

        Route::post('/access-requests/{id}/approve', [PatientDashboardController::class, 'approveAccessRequest'])->name('access_requests.approve');
        Route::post('/access-requests/{id}/reject', [PatientDashboardController::class, 'rejectAccessRequest'])->name('access_requests.reject');
        Route::post('/vaccinations/{id}/mark-taken', [PatientDashboardController::class, 'markVaccineTaken'])->name('vaccinations.mark_taken');
        Route::get('/emergency', [PatientDashboardController::class, 'emergencyHistory'])->name('emergency.history');
        Route::get('/emergency/sos', [PatientDashboardController::class, 'emergencySos'])->name('emergency.sos');
        Route::post('/emergency/trigger', [PatientDashboardController::class, 'triggerEmergency'])->name('emergency.trigger');
        Route::get('/emergency/{id}', [PatientDashboardController::class, 'viewEmergency'])->name('emergency.view');
        Route::post('/evaluation', [PatientDashboardController::class, 'storeEvaluation'])->name('evaluation.store');
    });

    // ==========================================
    // DOCTOR ROUTES
    // ==========================================
    Route::prefix('doctor')->name('doctor.')->middleware('role:doctor')->group(function () {
        Route::get('/dashboard', [DoctorDashboardController::class, 'index'])->name('dashboard');
        Route::get('/patient/{id}', [DoctorDashboardController::class, 'viewPatient'])->name('patient.view');
        Route::post('/patient/{id}/request-access', [DoctorDashboardController::class, 'requestAccess'])->name('patient.request_access');
        Route::post('/patient/{patient_id}/medical-record', [DoctorDashboardController::class, 'storeMedicalRecord'])->name('medical_record.store');
        Route::get('/consultation/{patient_id}', [DoctorDashboardController::class, 'consultation'])->name('consultation');
        Route::post('/consultation/{patient_id}', [DoctorDashboardController::class, 'storeConsultation'])->name('consultation.store');
        // ... (remaining doctor routes)
        Route::post('/queue/{appointment_id}/visit', [DoctorDashboardController::class, 'markVisited'])->name('queue.visit');
        Route::post('/appointments/{appointment_id}/approve', [DoctorDashboardController::class, 'approveAppointment'])->name('appointment.approve');
        Route::get('/emergency/{id}', [DoctorDashboardController::class, 'viewEmergency'])->name('emergency.view');
        Route::post('/emergency/{id}/triage', [DoctorDashboardController::class, 'storeTriage'])->name('emergency.triage');
    });

    // ==========================================
    // HOSPITAL ROUTES
    // ==========================================
    Route::prefix('hospital')->name('hospital.')->middleware('role:hospital')->group(function () {
        Route::get('/dashboard', [HospitalDashboardController::class, 'index'])->name('dashboard');
        Route::get('/logs', [HospitalAuditLogController::class, 'index'])->name('logs');
        // ... (remaining hospital routes)
        Route::post('/lab-orders/{id}/complete', [HospitalDashboardController::class, 'completeLabOrder'])->name('lab_orders.complete');
        Route::post('/resources/{id}/update', [HospitalDashboardController::class, 'updateResource'])->name('resources.update');
        Route::get('/emergencies', [HospitalDashboardController::class, 'emergencies'])->name('emergencies.index');
        Route::get('/emergencies/{id}', [HospitalDashboardController::class, 'viewEmergency'])->name('emergencies.view');
        Route::post('/emergencies/{id}/accept', [HospitalDashboardController::class, 'acceptEmergency'])->name('emergencies.accept');
        Route::post('/emergencies/{id}/reject', [HospitalDashboardController::class, 'rejectEmergency'])->name('emergencies.reject');
        Route::post('/emergencies/{id}/dispatch', [HospitalDashboardController::class, 'dispatchAmbulance'])->name('emergencies.dispatch');
        Route::post('/emergencies/{id}/assign-doctor', [HospitalDashboardController::class, 'assignDoctor'])->name('emergencies.assign_doctor');
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

    // ==========================================
    // GOVT ADMIN ROUTES
    // ==========================================
    Route::prefix('govt-admin')->name('govt_admin.')->middleware('role:govt_admin')->group(function () {
        Route::get('/dashboard', [GovtAdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/audit-logs', [\App\Http\Controllers\Govt\AuditLogController::class, 'index'])->name('audit_logs');
        Route::get('/audit-logs/export', [\App\Http\Controllers\Govt\AuditLogController::class, 'exportCsv'])->name('audit_logs.export');
        Route::get('/emergencies', [GovtAdminDashboardController::class, 'emergencies'])->name('emergencies.index');
    });

    // ==========================================
    // AMBULANCE ROUTES
    // ==========================================
    Route::prefix('ambulance')->name('ambulance.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Ambulance\AmbulanceDashboardController::class, 'index'])->name('dashboard');
        Route::post('/emergency/{id}/status', [\App\Http\Controllers\Ambulance\AmbulanceDashboardController::class, 'updateStatus'])->name('emergency.status');
    });
});

// 4. Standard Profile Routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';

