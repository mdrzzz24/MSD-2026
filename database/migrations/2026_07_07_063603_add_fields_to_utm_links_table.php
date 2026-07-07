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
        Schema::table('utm_links', function (Blueprint $table) {
            if (!Schema::hasColumn('utm_links', 'name')) {
                $table->string('name')->after('id');
            }
            if (!Schema::hasColumn('utm_links', 'base_url')) {
                $table->string('base_url')->default('http://127.0.0.1:8000/home1')->after('name');
            }
            if (!Schema::hasColumn('utm_links', 'utm_source')) {
                $table->string('utm_source')->after('base_url');
            }
            if (!Schema::hasColumn('utm_links', 'utm_medium')) {
                $table->string('utm_medium')->after('utm_source');
            }
            if (!Schema::hasColumn('utm_links', 'utm_campaign')) {
                $table->string('utm_campaign')->after('utm_medium');
            }
            if (!Schema::hasColumn('utm_links', 'utm_content')) {
                $table->string('utm_content')->nullable()->after('utm_campaign');
            }
            if (!Schema::hasColumn('utm_links', 'full_url')) {
                $table->string('full_url')->nullable()->after('utm_content');
            }
            if (!Schema::hasColumn('utm_links', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('full_url');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('utm_links', function (Blueprint $table) {
            $columns = ['name', 'base_url', 'utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'full_url', 'is_active'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('utm_links', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
