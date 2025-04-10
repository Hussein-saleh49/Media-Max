<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('driver_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // المستخدم الذي قام بالتقييم
            $table->foreignId('driver_id')->constrained('users')->onDelete('cascade'); // السائق الذي تم تقييمه
            $table->integer('rating'); // التقييم (من 1 إلى 5)
            $table->text('feedback')->nullable(); // تعليق اختياري
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('driver_ratings');
    }
};
