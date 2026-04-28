<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== DOCTOR → HOSPITAL MAPPING VERIFICATION ===\n\n";
$doctors = App\Models\Doctor::with('hospital')->get();
foreach ($doctors as $d) {
    $hospitalName = $d->hospital ? $d->hospital->name : 'NO HOSPITAL';
    $user = $d->user ? $d->user->email : 'NO EMAIL';
    echo "Dr. {$d->first_name} {$d->last_name} ({$user})\n   → Hospital: {$hospitalName}\n\n";
}

echo "\n=== APPOINTMENT COUNT BY DOCTOR ===\n\n";
foreach ($doctors as $d) {
    $pending = App\Models\Appointment::where('doctor_id', $d->id)->where('status', 'pending')->count();
    $approved = App\Models\Appointment::where('doctor_id', $d->id)->where('status', 'approved')->count();
    echo "Dr. {$d->first_name} {$d->last_name}: {$pending} pending, {$approved} approved\n";
}
