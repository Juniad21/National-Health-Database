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
        Schema::table('access_logs', function (Blueprint $table) {
            $table->string('module')->nullable()->after('action');
            $table->text('old_value')->nullable()->after('description');
            $table->text('new_value')->nullable()->after('old_value');
            $table->string('severity')->default('low')->after('new_value');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('access_logs', function (Blueprint $table) {
            $table->dropColumn(['module', 'old_value', 'new_value', 'severity']);
        });
    }
};
