<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('hospital_id')->nullable(); // Can be constrained later when hospitals table is ready, but typically doctors belong to one or more. For this schema, simple foreignId.
            $table->string('bmdc_number')->unique();
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('specialty');
            $table->text('qualifications')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};
