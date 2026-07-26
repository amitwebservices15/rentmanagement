<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();

            // Foreign key to properties table
            $table->foreignId('property_id')
                  ->constrained()
                  ->onDelete('cascade');

            $table->string('room_number'); // e.g., 101, A1
            $table->string('floor')->nullable(); // optional
            $table->integer('capacity'); // 1, 2, 3 persons
            $table->decimal('rent_amount', 10, 2); // default rent
            $table->enum('status', ['available', 'occupied'])->default('available');
            $table->text('description')->nullable();

            $table->timestamps();

            // Optional: prevent duplicate room numbers in same property
            $table->unique(['property_id', 'room_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};