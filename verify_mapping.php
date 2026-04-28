<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Doctor;
use App\Models\Appointment;

echo "=== DOCTOR → HOSPITAL MAPPING VERIFICATION ===\n\n";

// Load doctor with hospital AND user to avoid NO EMAIL issues
$doctors = Doctor::with(['hospital', 'user'])->get();

foreach ($doctors as $d) {
    $hospitalName = $d->hospital ? $d->hospital->name : '⚠️ NO HOSPITAL';
    $userEmail = $d->user ? $d->user->email : '⚠️ NO USER/EMAIL';

    echo "Dr. {$d->first_name} {$d->last_name} ({$userEmail})\n";
    echo "   → Hospital: {$hospitalName}\n\n";
}

echo "=== APPOINTMENT COUNT BY DOCTOR ===\n\n";

foreach ($doctors as $d) {
    $pending = Appointment::where('doctor_id', $d->id)->where('status', 'pending')->count();
    $approved = Appointment::where('doctor_id', $d->id)->where('status', 'approved')->count();
    $completed = Appointment::where('doctor_id', $d->id)->where('status', 'completed')->count();

    echo "Dr. {$d->first_name} {$d->last_name}:\n";
    echo "   - Pending: {$pending}\n";
    echo "   - Approved: {$approved}\n";
    echo "   - Completed: {$completed}\n\n";
}
