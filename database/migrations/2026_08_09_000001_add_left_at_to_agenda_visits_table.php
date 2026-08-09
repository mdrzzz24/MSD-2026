<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add track-out timestamp to agenda visits.
     */
    public function up(): void
    {
        Schema::table('agenda_visits', function (Blueprint $table) {
            $table->timestamp('left_at')->nullable()->after('visited_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agenda_visits', function (Blueprint $table) {
            $table->dropColumn('left_at');
        });
    }
};
