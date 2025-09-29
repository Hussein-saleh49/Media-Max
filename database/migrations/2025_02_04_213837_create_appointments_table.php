<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->string('medication_name'); // اسم الدواء المرتبط بالمستخدم فقط (بدون ربط بجدول الأدوية)
            $table->string('title');
            $table->text('description')->nullable();
            $table->dateTime('appointment_time'); // وقت التذكير
            $table->enum('status', ['pending', 'taken', 'skipped'])->default('pending'); // حالة الموعد
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
