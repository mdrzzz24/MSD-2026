<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add a track_id to workshop UTM links so a single custom-slug invitation
     * can serve multiple tracks (the invitation page resolves the track from the UTM link).
     */
    public function up(): void
    {
        Schema::table('utm_links', function (Blueprint $table) {
            $table->foreignId('track_id')
                ->nullable()
                ->after('workshop_invitation_id')
                ->constrained('tracks')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('utm_links', function (Blueprint $table) {
            $table->dropForeign(['track_id']);
            $table->dropColumn('track_id');
        });
    }
};
