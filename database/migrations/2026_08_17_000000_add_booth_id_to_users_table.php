<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Bind a user to a booth (mobile-app "booth account").
 *
 * Idempotent guard so the migration is safe on environments where the column
 * was already applied out-of-band.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'booth_id')) {
                $table->foreignId('booth_id')->nullable()->after('room_id')->constrained('booths')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'booth_id')) {
                $table->dropForeign(['booth_id']);
                $table->dropColumn('booth_id');
            }
        });
    }
};
