<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rent_records', function (Blueprint $table) {
            // Drop old unique constraint on tenant_id + month
            $table->dropUnique(['tenant_id', 'month']);

            // Make tenant_id nullable (record is now room-based)
            $table->foreignId('tenant_id')->nullable()->change();

            // Add meter readings to track on the record itself
            $table->decimal('meter_start', 10, 2)->default(0)->after('other_charges');
            $table->decimal('meter_end', 10, 2)->default(0)->after('meter_start');

            // Store tenant names snapshot (comma-separated) for display
            $table->string('tenant_names')->nullable()->after('meter_end');

            // One record per room per month
            $table->unique(['room_id', 'month']);
        });
    }

    public function down(): void
    {
        Schema::table('rent_records', function (Blueprint $table) {
            $table->dropUnique(['room_id', 'month']);
            $table->dropColumn(['meter_start', 'meter_end', 'tenant_names']);
            $table->foreignId('tenant_id')->nullable(false)->change();
            $table->unique(['tenant_id', 'month']);
        });
    }
};
