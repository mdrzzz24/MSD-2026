<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tracks', function (Blueprint $table) {
            if (!Schema::hasColumn('tracks', 'start_time')) {
                $table->string('start_time')->nullable()->after('description');
            }
            if (!Schema::hasColumn('tracks', 'end_time')) {
                $table->string('end_time')->nullable()->after('start_time');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tracks', function (Blueprint $table) {
            $table->dropColumn(['start_time', 'end_time']);
        });
    }
};
