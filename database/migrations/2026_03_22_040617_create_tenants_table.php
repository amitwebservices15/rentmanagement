<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('mobile'); // important for WhatsApp
            $table->string('email')->nullable();
            $table->string('id_proof')->nullable(); // file path to ID proof document e.g., Aadhar, PAN 
            $table->string('id_proof_number')->nullable(); //  ID number from the document
            $table->text('address')->nullable();
            $table->string('photo')->nullable(); // image path
            $table->enum('status', ['active', 'inactive'])->default('active');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
