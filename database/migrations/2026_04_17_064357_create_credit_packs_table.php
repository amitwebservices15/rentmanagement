<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('credit_packs', function (Blueprint $table) {
            $table->id();
            $table->string('name');                  // e.g. Starter Pack
            $table->decimal('price', 10, 2);         // e.g. 99.00
            $table->integer('credits');              // e.g. 100
            $table->string('tag')->nullable();       // e.g. "Best Value", "Popular"
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('credit_packs');
    }
};
