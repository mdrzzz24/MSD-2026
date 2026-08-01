<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('utm_links', function (Blueprint $table) {
            $table->unsignedBigInteger('workshop_invitation_id')->nullable()->after('workshop_id');
            $table->foreign('workshop_invitation_id')
                  ->references('id')->on('workshop_invitations')
                  ->nullOnDelete();
            $table->index('workshop_invitation_id');
        });
    }

    public function down(): void
    {
        Schema::table('utm_links', function (Blueprint $table) {
            $table->dropForeign(['workshop_invitation_id']);
            $table->dropIndex(['workshop_invitation_id']);
            $table->dropColumn('workshop_invitation_id');
        });
    }
};
