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
        Schema::table('agenda_items', function (Blueprint $table) {
            if (!Schema::hasColumn('agenda_items', 'topic_headline')) {
                $table->string('topic_headline', 255)->nullable()->after('title');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agenda_items', function (Blueprint $table) {
            if (Schema::hasColumn('agenda_items', 'topic_headline')) {
                $table->dropColumn('topic_headline');
            }
        });
    }
};
