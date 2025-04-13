<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('payment_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('card_holder_name');
            $table->string('card_number'); // سيتم تشفيره
            $table->string('expiry_date');
            $table->string('cvv'); // سيتم تشفيره
            $table->string('card_type')->nullable(); // VISA, MasterCard
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payment_cards');
    }
};
