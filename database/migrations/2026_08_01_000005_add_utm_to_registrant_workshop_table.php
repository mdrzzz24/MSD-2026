<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Track which UTM link/source each workshop registration came from
        Schema::table('registrant_workshop', function (Blueprint $table) {
            if (!Schema::hasColumn('registrant_workshop', 'utm_source')) {
                $table->string('utm_source', 100)->nullable()->after('track_id');
            }
            if (!Schema::hasColumn('registrant_workshop', 'utm_medium')) {
                $table->string('utm_medium', 100)->nullable()->after('utm_source');
            }
            if (!Schema::hasColumn('registrant_workshop', 'utm_campaign')) {
                $table->string('utm_campaign', 100)->nullable()->after('utm_medium');
            }
            if (!Schema::hasColumn('registrant_workshop', 'utm_content')) {
                $table->string('utm_content', 100)->nullable()->after('utm_campaign');
            }
        });
    }

    public function down(): void
    {
        Schema::table('registrant_workshop', function (Blueprint $table) {
            foreach (['utm_content', 'utm_campaign', 'utm_medium', 'utm_source'] as $col) {
                if (Schema::hasColumn('registrant_workshop', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
