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
        // Add created_by to utm_links
        Schema::table('utm_links', function (Blueprint $table) {
            if (!Schema::hasColumn('utm_links', 'created_by')) {
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete()->after('is_active');
            }
        });

        // Add created_by to referral_codes
        Schema::table('referral_codes', function (Blueprint $table) {
            if (!Schema::hasColumn('referral_codes', 'created_by')) {
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete()->after('is_active');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('utm_links', function (Blueprint $table) {
            if (Schema::hasColumn('utm_links', 'created_by')) {
                $table->dropForeign(['created_by']);
                $table->dropColumn('created_by');
            }
        });

        Schema::table('referral_codes', function (Blueprint $table) {
            if (Schema::hasColumn('referral_codes', 'created_by')) {
                $table->dropForeign(['created_by']);
                $table->dropColumn('created_by');
            }
        });
    }
};
