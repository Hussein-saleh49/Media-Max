<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('info', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // نوع البيانات (about أو contact)
            $table->text('content'); // المحتوى
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('info');
    }
};
