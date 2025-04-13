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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();  // Auto-incrementing primary key
            $table->string('medication_name');  // Medication name column
            $table->date('appointment_date');  // Appointment date
            $table->time('appointment_time');  // Appointment time
            $table->enum('status', ['taken', 'skipped'])->default('skipped');  // Status of the appointment
            $table->foreignId('user_id')->constrained()->onDelete('cascade');  // Foreign key for user_id
            $table->timestamps();  // Timestamps for created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
