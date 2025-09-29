<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->decimal('discount', 10, 2);
            $table->enum('type', ['fixed', 'percentage']); // fixed = مبلغ ثابت، percentage = نسبة مئوية
            $table->integer('usage_limit')->default(1); // عدد مرات الاستخدام
            $table->integer('used_count')->default(0); // عدد مرات الاستخدام الحالية
            $table->dateTime('expiration_date')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('vouchers');
    }
};
