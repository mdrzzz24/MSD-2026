<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tracks', function (Blueprint $table) {
            if (!Schema::hasColumn('tracks', 'workshop_id')) {
                $table->foreignId('workshop_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('tracks', function (Blueprint $table) {
            $table->dropForeign(['workshop_id']);
            $table->dropColumn('workshop_id');
        });
    }
};
