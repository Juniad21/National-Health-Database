<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

// Configure Laravel app
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

// Load doctors
$doctors = App\Models\Doctor::with(['hospital', 'user'])->take(5)->get();

if ($doctors->isEmpty()) {
    echo "No doctors found in database.\n";
} else {
    echo "Found " . $doctors->count() . " doctors:\n\n";
    foreach ($doctors as $doctor) {
        $name = $doctor->user->name ?? 'Unknown';
        $specialty = $doctor->specialty ?? 'General';
        $hospital = $doctor->hospital->name ?? 'Unknown';
        echo "Dr. $name ($specialty) - $hospital\n";
    }
}
