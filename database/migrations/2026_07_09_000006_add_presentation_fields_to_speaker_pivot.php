<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agenda_item_speaker', function (Blueprint $table) {
            if (!Schema::hasColumn('agenda_item_speaker', 'presentation_title')) {
                $table->string('presentation_title')->nullable()->after('key_highlights');
            }
            if (!Schema::hasColumn('agenda_item_speaker', 'presentation_description')) {
                $table->text('presentation_description')->nullable()->after('presentation_title');
            }
        });
    }

    public function down(): void
    {
        Schema::table('agenda_item_speaker', function (Blueprint $table) {
            $table->dropColumn(['presentation_title', 'presentation_description']);
        });
    }
};
