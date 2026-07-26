<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('room_tenant_assignments', function (Blueprint $table) {
            $table->id();

            // Foreign Keys
            $table->foreignId('room_id')
                  ->constrained()
                  ->onDelete('cascade');

            $table->foreignId('tenant_id')
                  ->constrained()
                  ->onDelete('cascade');

            // Rent override
            $table->decimal('rent_amount', 10, 2)->nullable();

            // Electricity tracking
            $table->decimal('electricity_meter_start', 10, 2)->nullable();
            $table->decimal('electricity_meter_end', 10, 2)->nullable();

            // Stay duration
            $table->date('start_date');
            $table->date('end_date')->nullable(); // null = currently staying

            // Status
            $table->enum('status', ['active', 'vacated'])->default('active');

            $table->timestamps();

            // Optional: Prevent duplicate active assignment for same tenant
            // (Handled logically in app, but you can also enforce partial index in DB if needed)
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_tenant_assignments');
    }
};