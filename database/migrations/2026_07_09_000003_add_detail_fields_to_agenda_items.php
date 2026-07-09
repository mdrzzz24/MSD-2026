<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agenda_items', function (Blueprint $table) {
            if (!Schema::hasColumn('agenda_items', 'description')) {
                $table->text('description')->nullable()->after('title');
            }
            if (!Schema::hasColumn('agenda_items', 'speaker_name')) {
                $table->string('speaker_name')->nullable()->after('description');
            }
            if (!Schema::hasColumn('agenda_items', 'speaker_title')) {
                $table->string('speaker_title')->nullable()->after('speaker_name');
            }
            if (!Schema::hasColumn('agenda_items', 'speaker_photo')) {
                $table->string('speaker_photo')->nullable()->after('speaker_title');
            }
            if (!Schema::hasColumn('agenda_items', 'speaker2_name')) {
                $table->string('speaker2_name')->nullable()->after('speaker_photo');
            }
            if (!Schema::hasColumn('agenda_items', 'speaker2_title')) {
                $table->string('speaker2_title')->nullable()->after('speaker2_name');
            }
            if (!Schema::hasColumn('agenda_items', 'speaker2_photo')) {
                $table->string('speaker2_photo')->nullable()->after('speaker2_title');
            }
            if (!Schema::hasColumn('agenda_items', 'key_highlights')) {
                $table->text('key_highlights')->nullable()->after('speaker2_photo');
            }
        });
    }

    public function down(): void
    {
        Schema::table('agenda_items', function (Blueprint $table) {
            $table->dropColumn([
                'description', 'speaker_name', 'speaker_title', 'speaker_photo',
                'speaker2_name', 'speaker2_title', 'speaker2_photo', 'key_highlights',
            ]);
        });
    }
};
