<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('medications', function (Blueprint $table) {
            $table->string('image')->nullable()->after('price'); // عمود لتخزين اسم الصورة
            $table->integer('capsules_number')->nullable()->after('image'); // عدد الكبسولات
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('medications', function (Blueprint $table) {
            $table->dropColumn('image');
            $table->dropColumn('capsules_number');
        });
    }
};
