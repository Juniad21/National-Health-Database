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
        Schema::create('blood_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hospital_id')->constrained('hospitals')->cascadeOnDelete();
            $table->string('hospital_name')->nullable();
            $table->string('district')->nullable();
            $table->string('blood_group'); // A+, A-, B+, B-, AB+, AB-, O+, O-
            $table->integer('available_units')->default(0);
            $table->integer('reserved_units')->default(0);
            $table->integer('minimum_required_units')->default(0);
            $table->foreignId('last_updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['hospital_id', 'blood_group']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blood_stocks');
    }
};
