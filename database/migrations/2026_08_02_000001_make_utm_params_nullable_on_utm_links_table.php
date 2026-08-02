<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Allow workshop UTM links to be created with only a source (or none of the
     * UTM params). Previously utm_source/utm_medium/utm_campaign were NOT NULL,
     * which rejected empty values after the empty-string-to-null middleware.
     */
    public function up(): void
    {
        Schema::table('utm_links', function (Blueprint $table) {
            if (Schema::hasColumn('utm_links', 'utm_source')) {
                $table->string('utm_source')->nullable()->change();
            }
            if (Schema::hasColumn('utm_links', 'utm_medium')) {
                $table->string('utm_medium')->nullable()->change();
            }
            if (Schema::hasColumn('utm_links', 'utm_campaign')) {
                $table->string('utm_campaign')->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('utm_links', function (Blueprint $table) {
            if (Schema::hasColumn('utm_links', 'utm_source')) {
                $table->string('utm_source')->nullable(false)->change();
            }
            if (Schema::hasColumn('utm_links', 'utm_medium')) {
                $table->string('utm_medium')->nullable(false)->change();
            }
            if (Schema::hasColumn('utm_links', 'utm_campaign')) {
                $table->string('utm_campaign')->nullable(false)->change();
            }
        });
    }
};
