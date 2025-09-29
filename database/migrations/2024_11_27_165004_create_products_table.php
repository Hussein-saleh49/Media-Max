<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // اسم المنتج
            $table->text('description')->nullable(); // وصف المنتج
            $table->decimal('price', 10, 2); // سعر المنتج
            $table->decimal('discount', 10, 2)->default(0); // قيمة الخصم
            $table->decimal('final_price', 10, 2); // السعر النهائي بعد الخصم
            $table->string('image')->nullable(); // صورة المنتج
            $table->integer('rating')->default(0); // تقييم المنتج
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('products');
    }
};
