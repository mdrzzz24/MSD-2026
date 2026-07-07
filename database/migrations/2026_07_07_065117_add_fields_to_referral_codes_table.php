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
        // Add columns to referral_codes table
        Schema::table('referral_codes', function (Blueprint $table) {
            if (!Schema::hasColumn('referral_codes', 'code')) {
                $table->string('code', 50)->unique()->after('id');
            }
            if (!Schema::hasColumn('referral_codes', 'owner_name')) {
                $table->string('owner_name', 255)->nullable()->after('code');
            }
            if (!Schema::hasColumn('referral_codes', 'description')) {
                $table->text('description')->nullable()->after('owner_name');
            }
            if (!Schema::hasColumn('referral_codes', 'max_uses')) {
                $table->unsignedInteger('max_uses')->default(0)->after('description');
            }
            if (!Schema::hasColumn('referral_codes', 'uses_count')) {
                $table->unsignedInteger('uses_count')->default(0)->after('max_uses');
            }
            if (!Schema::hasColumn('referral_codes', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('uses_count');
            }
        });

        // Add referral_code_id FK to registrants
        Schema::table('registrants', function (Blueprint $table) {
            if (!Schema::hasColumn('registrants', 'referral_code_id')) {
                $table->foreignId('referral_code_id')->nullable()->constrained('referral_codes')->nullOnDelete()->after('referral_code');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('registrants', function (Blueprint $table) {
            if (Schema::hasColumn('registrants', 'referral_code_id')) {
                $table->dropForeign(['referral_code_id']);
                $table->dropColumn('referral_code_id');
            }
        });

        Schema::table('referral_codes', function (Blueprint $table) {
            $columns = ['code', 'owner_name', 'description', 'max_uses', 'uses_count', 'is_active'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('referral_codes', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
