<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, add the hospital_id column
        Schema::table('medical_records', function (Blueprint $table) {
            $table->foreignId('hospital_id')->nullable()->constrained('hospitals')->onDelete('set null')->after('doctor_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medical_records', function (Blueprint $table) {
            $table->dropForeignKeyIfExists(['hospital_id']);
            $table->dropColumn('hospital_id');
        });
    }
};
