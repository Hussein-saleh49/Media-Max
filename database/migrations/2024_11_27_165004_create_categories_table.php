<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * تشغيل الهجرة.
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // اسم الفئة (يجب أن يكون فريدًا)
            $table->text('description')->nullable(); // وصف الفئة (اختياري)
            $table->string('icon')->nullable(); // أيقونة الفئة
            $table->integer('priority')->default(0); // ترتيب الفئة (الأولوية)
            $table->integer('product_count')->default(0); // عدد المنتجات داخل الفئة
            $table->timestamps();
        });
    }

    /**
     * إلغاء الهجرة.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
