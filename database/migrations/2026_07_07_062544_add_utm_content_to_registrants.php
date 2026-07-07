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
            if (!Schema::hasColumn('registrants', 'utm_source')) {
                $table->string('utm_source')->nullable()->after('qr_token');
            }
            if (!Schema::hasColumn('registrants', 'utm_medium')) {
                $table->string('utm_medium')->nullable()->after('utm_source');
            }
            if (!Schema::hasColumn('registrants', 'utm_campaign')) {
                $table->string('utm_campaign')->nullable()->after('utm_medium');
            }
            if (!Schema::hasColumn('registrants', 'utm_content')) {
                $table->string('utm_content')->nullable()->after('utm_campaign');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('registrants', function (Blueprint $table) {
            $columns = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_content'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('registrants', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
