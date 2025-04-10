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
        Schema::create('reminders', function (Blueprint $table) {
            $table->id();  // Auto-incrementing primary key
            $table->foreignId('user_id')->constrained()->onDelete('cascade');  // Foreign key for user_id
            $table->foreignId('appointment_id')->constrained('appointments')->onDelete('cascade');  // Foreign key for appointment_id
            $table->string('medication_name');  // Medication name
            $table->date('reminder_date');  // Reminder date
            $table->time('reminder_time');  // Reminder time
            $table->enum('am_pm', ['AM', 'PM']);  // AM or PM
            $table->text('repeat_days')->nullable();  // Repeat days (e.g., ["monday", "wednesday"])
            $table->string('sound')->nullable();  // Sound for the reminder
            $table->string('label')->nullable();  // Label for the reminder
            $table->integer('ring_duration')->nullable();  // Ring duration
            $table->integer('snooze_duration')->nullable();  // Snooze duration
            $table->integer('snooze_count')->default(0);  // Snooze count
            $table->timestamps();  // Timestamps for created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reminders');
    }
};
