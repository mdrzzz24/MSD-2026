<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Rollback of the workshop-target UTM feature — UTM links are home-only again.
        Schema::table('utm_links', function (Blueprint $table) {
            if (Schema::hasColumn('utm_links', 'workshop_id')) {
                $table->dropForeign(['workshop_id']);
                $table->dropColumn('workshop_id');
            }
            if (Schema::hasColumn('utm_links', 'target_type')) {
                $table->dropColumn('target_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('utm_links', function (Blueprint $table) {
            if (!Schema::hasColumn('utm_links', 'target_type')) {
                $table->string('target_type', 20)->default('home')->after('base_url');
            }
            if (!Schema::hasColumn('utm_links', 'workshop_id')) {
                $table->foreignId('workshop_id')->nullable()->after('target_type')->constrained()->cascadeOnDelete();
            }
        });
    }
};
