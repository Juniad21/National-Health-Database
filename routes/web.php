<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Patient\PatientDashboardController;
use App\Http\Controllers\Doctor\DoctorDashboardController;
use App\Http\Controllers\Hospital\HospitalDashboardController;
use App\Http\Controllers\Hospital\HospitalAuditLogController;
use App\Http\Controllers\Hospital\HospitalBillingController;
use App\Http\Controllers\Doctor\DoctorReferralController;
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
        Route::post('/appointment/{appointment}/review', [PatientDashboardController::class, 'submitReview'])->name('submit_review');

        // Profile Management
        Route::get('/profile', [\App\Http\Controllers\Patient\PatientProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [\App\Http\Controllers\Patient\PatientProfileController::class, 'update'])->name('profile.update');

        // Health Analytics
        Route::get('/health-analytics', [\App\Http\Controllers\Patient\PatientProfileController::class, 'healthAnalytics'])->name('health_analytics');
        Route::post('/health-metrics', [\App\Http\Controllers\Patient\PatientProfileController::class, 'storeMetric'])->name('health_metrics.store');
        Route::delete('/health-metrics/{id}', [\App\Http\Controllers\Patient\PatientProfileController::class, 'destroyMetric'])->name('health_metrics.destroy');

        // Vaccination Tracking
        Route::get('/dashboard/vaccinations', [PatientDashboardController::class, 'vaccinations'])->name('vaccinations');
        
        // Referrals
        Route::get('/referrals', [PatientDashboardController::class, 'referrals'])->name('referrals');
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
        
        // Vaccination Prescription
        Route::post('/patient/{patient}/vaccine', [DoctorDashboardController::class, 'prescribeVaccine'])->name('prescribe_vaccine');
        Route::get('/reviews', [DoctorDashboardController::class, 'reviews'])->name('reviews');
        // ... (remaining doctor routes)
        Route::post('/queue/{appointment_id}/visit', [DoctorDashboardController::class, 'markVisited'])->name('queue.visit');
        Route::post('/appointments/{appointment_id}/approve', [DoctorDashboardController::class, 'approveAppointment'])->name('appointment.approve');
        Route::get('/emergency/{id}', [DoctorDashboardController::class, 'viewEmergency'])->name('emergency.view');
        Route::post('/emergency/{id}/triage', [DoctorDashboardController::class, 'storeTriage'])->name('emergency.triage');

        // Profile Management
        Route::get('/profile', [\App\Http\Controllers\Doctor\DoctorProfileController::class, 'show'])->name('profile.show');
        Route::get('/profile/edit', [\App\Http\Controllers\Doctor\DoctorProfileController::class, 'edit'])->name('profile.edit');
        Route::post('/profile', [\App\Http\Controllers\Doctor\DoctorProfileController::class, 'update'])->name('profile.update');

        // Referrals
        Route::get('/referrals', [DoctorReferralController::class, 'index'])->name('referrals.index');
        Route::get('/patients/{patient}/refer', [DoctorReferralController::class, 'create'])->name('referrals.create');
        Route::post('/patients/{patient}/refer', [DoctorReferralController::class, 'store'])->name('referrals.store');
        Route::get('/referrals/{referral}', [DoctorReferralController::class, 'show'])->name('referrals.show');
        Route::patch('/referrals/{referral}/status', [DoctorReferralController::class, 'updateStatus'])->name('referrals.status');
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

        // Disease Reporting
        Route::prefix('disease-reports')->name('disease_reports.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Hospital\HospitalDiseaseReportController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Hospital\HospitalDiseaseReportController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Hospital\HospitalDiseaseReportController::class, 'store'])->name('store');
            Route::get('/{id}', [\App\Http\Controllers\Hospital\HospitalDiseaseReportController::class, 'show'])->name('show');
        });

        // Ambulance Fleet
        Route::prefix('ambulance-fleet')->name('ambulance_fleet.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Hospital\AmbulanceFleetController::class, 'index'])->name('index');
            Route::post('/', [\App\Http\Controllers\Hospital\AmbulanceFleetController::class, 'store'])->name('store');
            Route::put('/{id}', [\App\Http\Controllers\Hospital\AmbulanceFleetController::class, 'update'])->name('update');
            Route::get('/history', [\App\Http\Controllers\Hospital\AmbulanceFleetController::class, 'history'])->name('history');
            Route::post('/mission/{id}/status', [\App\Http\Controllers\Hospital\AmbulanceFleetController::class, 'updateAssignmentStatus'])->name('assignment.status');
            Route::post('/{id}/reset', [\App\Http\Controllers\Hospital\AmbulanceFleetController::class, 'resetStatus'])->name('reset');
        });

        // Blood Bank
        Route::prefix('blood-bank')->name('blood_bank.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Hospital\BloodBankController::class, 'index'])->name('index');
            Route::post('/stock', [\App\Http\Controllers\Hospital\BloodBankController::class, 'updateStock'])->name('stock.update');
            Route::post('/request', [\App\Http\Controllers\Hospital\BloodBankController::class, 'storeRequest'])->name('request.store');
            Route::post('/request/{id}/cancel', [\App\Http\Controllers\Hospital\BloodBankController::class, 'cancelRequest'])->name('request.cancel');
        });
    });

    // ==========================================
    // GOVT ADMIN ROUTES
    // ==========================================
    Route::prefix('govt-admin')->name('govt_admin.')->middleware('role:govt_admin')->group(function () {
        Route::get('/dashboard', [GovtAdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/audit-logs', [\App\Http\Controllers\Govt\AuditLogController::class, 'index'])->name('audit_logs');
        Route::get('/audit-logs/export', [\App\Http\Controllers\Govt\AuditLogController::class, 'exportCsv'])->name('audit_logs.export');
        Route::get('/emergencies', [GovtAdminDashboardController::class, 'emergencies'])->name('emergencies.index');
        Route::post('/emergencies/{id}/dispatch', [GovtAdminDashboardController::class, 'dispatchEmergency'])->name('emergencies.dispatch');

        // Disease Monitoring
        Route::get('/disease-monitoring', [\App\Http\Controllers\Govt\DiseaseMonitoringController::class, 'index'])->name('disease_monitoring.index');
        Route::post('/disease-monitoring/{id}/status', [\App\Http\Controllers\Govt\DiseaseMonitoringController::class, 'updateStatus'])->name('disease_monitoring.update_status');

        // Doctor Verification
        Route::get('/doctors', [\App\Http\Controllers\Govt\GovtAdminDashboardController::class, 'doctors'])->name('doctors.index');
        Route::get('/doctors/{id}', [\App\Http\Controllers\Govt\GovtAdminDashboardController::class, 'showDoctor'])->name('doctors.show');
        Route::post('/doctors/{id}/verify', [\App\Http\Controllers\Govt\GovtAdminDashboardController::class, 'verifyDoctor'])->name('doctors.verify');

        // Hospital Monitoring
        Route::get('/hospitals', [\App\Http\Controllers\Govt\GovtAdminDashboardController::class, 'hospitals'])->name('hospitals.index');

        // Ambulance Monitoring
        Route::get('/ambulances', [\App\Http\Controllers\Govt\GovtAdminDashboardController::class, 'ambulances'])->name('ambulances.index');

        // National Blood Bank
        Route::prefix('blood-bank')->name('blood_bank.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Govt\NationalBloodBankController::class, 'index'])->name('index');
            Route::post('/request/{id}/status', [\App\Http\Controllers\Govt\NationalBloodBankController::class, 'updateRequestStatus'])->name('request.status');
            Route::post('/request/{id}/match', [\App\Http\Controllers\Govt\NationalBloodBankController::class, 'matchHospital'])->name('request.match');
            Route::post('/request/{id}/note', [\App\Http\Controllers\Govt\NationalBloodBankController::class, 'updateAdminNote'])->name('request.note');
            Route::post('/transfer', [\App\Http\Controllers\Govt\NationalBloodBankController::class, 'transferStock'])->name('transfer');
        });
    });

    // Public Doctor Profiles
    Route::get('/doctor-profile/{id}', [\App\Http\Controllers\Doctor\DoctorProfileController::class, 'publicShow'])->name('doctor.public_profile');


});

// 4. Standard Profile Routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
