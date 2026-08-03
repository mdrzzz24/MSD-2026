<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add the waitlisted flag to registrants (idempotent — the column may
     * already exist on some environments, e.g. added directly to a database).
     */
    public function up(): void
    {
        if (! Schema::hasColumn('registrants', 'waitlisted')) {
            Schema::table('registrants', function (Blueprint $table) {
                $table->boolean('waitlisted')->default(false)->after('status');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('registrants', 'waitlisted')) {
            Schema::table('registrants', function (Blueprint $table) {
                $table->dropColumn('waitlisted');
            });
        }
    }
};
