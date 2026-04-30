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
        Schema::create('disease_reports', function (Blueprint $create) {
            $create->id();
            $create->string('disease_name');
            $create->string('district');
            $create->foreignId('hospital_id')->nullable()->constrained('hospitals')->onDelete('cascade');
            $create->string('hospital_name')->nullable();
            $create->foreignId('reported_by')->nullable()->constrained('users')->onDelete('set null');
            $create->integer('suspected_cases')->default(0);
            $create->integer('confirmed_cases')->default(0);
            $create->integer('recovered_cases')->default(0);
            $create->integer('death_cases')->default(0);
            $create->string('severity_level'); // Low, Medium, High, Critical
            $create->string('status')->default('New'); // New, Monitoring, Notice Sent, Hospital Alerted, Resolved
            $create->text('notes')->nullable();
            $create->date('report_date');
            $create->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disease_reports');
    }
};
