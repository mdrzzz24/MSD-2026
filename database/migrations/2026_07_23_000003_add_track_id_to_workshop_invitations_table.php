<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('workshop_invitations', function (Blueprint $table) {
            if (!Schema::hasColumn('workshop_invitations', 'track_id')) {
                $table->foreignId('track_id')->nullable()->after('workshop_id')->constrained()->cascadeOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('workshop_invitations', function (Blueprint $table) {
            $table->dropForeign(['track_id']);
            $table->dropColumn('track_id');
        });
    }
};
