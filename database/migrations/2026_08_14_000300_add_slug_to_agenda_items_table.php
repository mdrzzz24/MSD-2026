<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agenda_items', function (Blueprint $table) {
            $table->string('slug', 191)->nullable()->after('title');
            $table->unique('slug');
        });

        // Backfill slugs for existing agenda items (unique per title).
        $used = [];
        foreach (DB::table('agenda_items')->orderBy('id')->get(['id', 'title']) as $item) {
            $base = Str::slug((string) $item->title) ?: 'session';
            $slug = $base;
            $i = 2;
            while (isset($used[$slug])) {
                $slug = $base . '-' . $i++;
            }
            $used[$slug] = true;
            DB::table('agenda_items')->where('id', $item->id)->update(['slug' => $slug]);
        }
    }

    public function down(): void
    {
        Schema::table('agenda_items', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn('slug');
        });
    }
};
