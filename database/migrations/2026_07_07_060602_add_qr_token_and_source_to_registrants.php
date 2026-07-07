<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('registrants', function (Blueprint $table) {
            if (!Schema::hasColumn('registrants', 'attended_before')) {
                $table->boolean('attended_before')->default(false)->after('industry');
            }
            if (!Schema::hasColumn('registrants', 'checked_in_at')) {
                $table->timestamp('checked_in_at')->nullable()->after('attended_before');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('registrants', function (Blueprint $table) {
            $table->dropColumn(['attended_before', 'checked_in_at']);
        });
    }
};
