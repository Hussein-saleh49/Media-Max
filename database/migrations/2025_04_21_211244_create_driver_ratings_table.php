<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('driver_ratings', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->unsignedBigInteger('driver_id'); // Foreign key column for the driver
            $table->foreign('driver_id')->references('id')->on('drivers')->onDelete('cascade'); // Foreign key constraint with 'drivers' table
            $table->integer('rating')->min(1)->max(5); // Rating, should be between 1 and 5
            $table->text('feedback')->nullable(); // Optional feedback about the driver
            $table->timestamps(); // Timestamps for created_at and updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('driver_ratings'); // Rollback the creation of the table
    }
};
