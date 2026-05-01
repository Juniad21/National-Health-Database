<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('emergencies', function (Blueprint $table) {
            $table->foreignId('ambulance_id')->nullable()->constrained('ambulances')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('emergencies', function (Blueprint $table) {
            $table->dropConstrainedForeignId('ambulance_id');
        });
    }
};
