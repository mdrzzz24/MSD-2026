<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Add a short link code for each agenda item so feedback pages can be
     * reached via a compact URL like /f/ab12cd instead of the long slug URL.
     */
    public function up(): void
    {
        Schema::table('agenda_items', function (Blueprint $table) {
            $table->string('short_code', 12)->nullable()->after('slug');
            $table->unique('short_code');
        });

        // Backfill a unique short code for every existing agenda item.
        $taken = [];
        foreach (DB::table('agenda_items')->orderBy('id')->pluck('id') as $id) {
            do {
                $code = Str::lower(Str::random(6));
            } while (in_array($code, $taken, true));
            $taken[] = $code;
            DB::table('agenda_items')->where('id', $id)->update(['short_code' => $code]);
        }
    }

    public function down(): void
    {
        Schema::table('agenda_items', function (Blueprint $table) {
            $table->dropUnique(['short_code']);
            $table->dropColumn('short_code');
        });
    }
};
