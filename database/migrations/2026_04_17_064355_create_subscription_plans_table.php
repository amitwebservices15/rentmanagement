<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');                          // e.g. Basic, Pro
            $table->decimal('price', 10, 2);                 // e.g. 199.00
            $table->integer('validity_days');                // e.g. 30
            $table->integer('message_credits');              // e.g. 20
            $table->integer('max_properties')->default(1);   // properties allowed
            $table->integer('max_rooms')->default(10);       // rooms allowed
            $table->text('features')->nullable();            // JSON array of feature strings
            $table->boolean('is_active')->default(true);
            $table->boolean('is_popular')->default(false);   // highlight badge
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};
