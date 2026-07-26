<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();

            // Owner (important for SaaS)
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');

            // Property Details
            $table->string('property_name'); // required
            $table->enum('property_type', ['hostel', 'pg', 'rooms', 'flats', 'commercial'])->nullable();
            $table->integer('total_rooms'); // required

            // Address
            $table->string('address_line_1'); // required
            $table->string('address_line_2')->nullable();
            $table->string('city'); // required
            $table->string('state'); // required
            $table->string('pincode', 10)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};