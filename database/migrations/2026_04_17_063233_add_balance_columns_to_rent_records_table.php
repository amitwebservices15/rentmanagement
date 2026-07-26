<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rent_records', function (Blueprint $table) {
            // Carried forward from previous month
            $table->decimal('previous_due', 10, 2)->default(0)->after('other_charges');
            $table->decimal('advance_amount', 10, 2)->default(0)->after('previous_due');
        });
    }

    public function down(): void
    {
        Schema::table('rent_records', function (Blueprint $table) {
            $table->dropColumn(['previous_due', 'advance_amount']);
        });
    }
};
