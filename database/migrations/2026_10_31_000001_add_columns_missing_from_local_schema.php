<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Columns the code writes that were missing from the local copy of the database
 * (hwc_new) when the schema baseline was taken. Reconstructed from the code and
 * guarded with hasColumn(), so this is a no-op where they already exist (production).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('order_master', 'mobile_platform')) {
            Schema::table('order_master', function (Blueprint $table) {
                // Written by the mobile app's createorder ("ios" / "android")
                $table->string('mobile_platform', 20)->nullable();
            });
        }
    }

    public function down(): void
    {
        // Intentionally empty: on production this column holds real data.
    }
};
