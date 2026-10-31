<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Daily call counts for the original mobile app endpoints, so the cutover can
 * see when the old app has stopped calling them (php artisan api:legacy-usage).
 * Guarded, so it is a no-op where the table already exists.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('legacy_api_hits')) {
            Schema::create('legacy_api_hits', function (Blueprint $table) {
                $table->id();
                $table->date('day');
                $table->string('path', 191);
                $table->unsignedInteger('hits')->default(0);
                $table->timestamp('last_seen_at')->nullable();
                $table->unique(['day', 'path']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('legacy_api_hits');
    }
};
