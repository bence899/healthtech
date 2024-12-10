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
        // Create patient records for existing appointments
        DB::statement('
            INSERT INTO patients (user_id, created_at, updated_at)
            SELECT DISTINCT a.patient_id, NOW(), NOW()
            FROM appointments a
            LEFT JOIN patients p ON p.user_id = a.patient_id
            WHERE p.id IS NULL
        ');

        // Add temporary column
        Schema::table('appointments', function (Blueprint $table) {
            $table->unsignedBigInteger('new_patient_id')->nullable();
        });

        // Update new column with patient IDs
        DB::statement('
            UPDATE appointments a
            JOIN patients p ON p.user_id = a.patient_id
            SET a.new_patient_id = p.id
        ');

        // Drop old foreign key and column, then rename new column
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropForeign(['patient_id']);
            $table->dropColumn('patient_id');
        });

        Schema::table('appointments', function (Blueprint $table) {
            $table->renameColumn('new_patient_id', 'patient_id');
        });

        Schema::table('appointments', function (Blueprint $table) {
            $table->foreign('patient_id')
                  ->references('id')
                  ->on('patients')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // First, add temporary column
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropForeign(['patient_id']);
            $table->unsignedBigInteger('old_patient_id')->nullable();
        });

        // Update the temporary column with user IDs
        DB::statement('
            UPDATE appointments a
            JOIN patients p ON p.id = a.patient_id
            SET a.old_patient_id = p.user_id
        ');

        // Drop patient_id and rename old_patient_id
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn('patient_id');
        });

        Schema::table('appointments', function (Blueprint $table) {
            $table->renameColumn('old_patient_id', 'patient_id');
        });

        Schema::table('appointments', function (Blueprint $table) {
            $table->foreign('patient_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }
};
