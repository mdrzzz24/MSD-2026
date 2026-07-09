<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add workshop_id to agenda_items
        Schema::table('agenda_items', function (Blueprint $table) {
            if (!Schema::hasColumn('agenda_items', 'workshop_id')) {
                $table->foreignId('workshop_id')->nullable()->after('agenda_type')->constrained()->nullOnDelete();
            }
        });

        // Add key_highlights to agenda_item_speaker pivot
        Schema::table('agenda_item_speaker', function (Blueprint $table) {
            if (!Schema::hasColumn('agenda_item_speaker', 'key_highlights')) {
                $table->text('key_highlights')->nullable()->after('order');
            }
        });
    }

    public function down(): void
    {
        Schema::table('agenda_items', function (Blueprint $table) {
            $table->dropForeign(['workshop_id']);
            $table->dropColumn('workshop_id');
        });

        Schema::table('agenda_item_speaker', function (Blueprint $table) {
            $table->dropColumn('key_highlights');
        });
    }
};
