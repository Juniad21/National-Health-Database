<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For SQLite, we need to handle enum differently since it doesn't support modifying enums
        if (DB::connection()->getDriverName() === 'sqlite') {
            // SQLite workaround: Recreate the table with the correct schema
            DB::statement("ALTER TABLE medical_records RENAME TO medical_records_old");
            
            Schema::create('medical_records', function (Blueprint $table) {
                $table->id();
                $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
                $table->foreignId('doctor_id')->constrained('doctors')->onDelete('cascade');
                $table->foreignId('hospital_id')->nullable()->constrained('hospitals')->onDelete('set null');
                $table->string('record_type'); // Changed from enum to string to support lab_test
                $table->string('diagnosis')->nullable();
                $table->text('medications_or_results')->nullable();
                $table->string('document_path')->nullable();
                $table->string('status')->default('completed');
                $table->date('date')->nullable();
                $table->timestamps();
            });
            
            // Copy data from old table
            DB::statement("INSERT INTO medical_records (id, patient_id, doctor_id, hospital_id, record_type, diagnosis, medications_or_results, document_path, status, date, created_at, updated_at) 
                          SELECT id, patient_id, doctor_id, NULL, record_type, diagnosis, medications_or_results, document_path, COALESCE(status, 'completed'), date, created_at, updated_at FROM medical_records_old");
            
            // Drop old table
            DB::statement("DROP TABLE medical_records_old");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // For SQLite, recreate the old table structure
        if (DB::connection()->getDriverName() === 'sqlite') {
            DB::statement("ALTER TABLE medical_records RENAME TO medical_records_new");
            
            Schema::create('medical_records', function (Blueprint $table) {
                $table->id();
                $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
                $table->foreignId('doctor_id')->constrained('doctors')->onDelete('cascade');
                $table->enum('record_type', ['prescription', 'lab', 'document', 'vaccination']);
                $table->string('diagnosis')->nullable();
                $table->text('medications_or_results')->nullable();
                $table->string('document_path')->nullable();
                $table->date('date')->nullable();
                $table->timestamps();
            });
            
            DB::statement("INSERT INTO medical_records (id, patient_id, doctor_id, record_type, diagnosis, medications_or_results, document_path, date, created_at, updated_at) 
                          SELECT id, patient_id, doctor_id, record_type, diagnosis, medications_or_results, document_path, date, created_at, updated_at FROM medical_records_new WHERE record_type IN ('prescription', 'lab', 'document', 'vaccination')");
            
            DB::statement("DROP TABLE medical_records_new");
        }
    }
};

