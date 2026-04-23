<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$nids = App\Models\ValidNid::all()->pluck('nid_number');
foreach ($nids as $nid) {
    echo $nid . PHP_EOL;
}
?>