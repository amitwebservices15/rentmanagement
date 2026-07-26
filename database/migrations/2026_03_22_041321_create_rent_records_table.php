<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rent_records', function (Blueprint $table) {
            $table->id();

            // Foreign Keys
            $table->foreignId('tenant_id')
                  ->constrained()
                  ->onDelete('cascade');

            $table->foreignId('room_id')
                  ->constrained()
                  ->onDelete('cascade');

            $table->foreignId('property_id')
                  ->constrained()
                  ->onDelete('cascade');

            // Month (store as YYYY-MM format)
            $table->string('month'); // e.g. 2026-03

            // Charges
            $table->decimal('rent_amount', 10, 2);
            $table->decimal('electricity_units', 10, 2)->default(0);
            $table->decimal('electricity_charge', 10, 2)->default(0);
            $table->decimal('other_charges', 10, 2)->default(0);

            // Totals
            $table->decimal('total_amount', 10, 2);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->decimal('due_amount', 10, 2);

            // Status
            $table->enum('status', ['paid', 'unpaid', 'partial'])->default('unpaid');

            // Due date
            $table->date('due_date')->nullable();

            $table->timestamps();

            // Prevent duplicate billing for same tenant in same month
            $table->unique(['tenant_id', 'month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rent_records');
    }
};
