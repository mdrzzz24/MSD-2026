<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('workshop_invitations', function (Blueprint $table) {
            if (!Schema::hasColumn('workshop_invitations', 'slug')) {
                $table->string('slug', 120)->nullable()->unique()->after('token');
            }
        });
    }

    public function down(): void
    {
        Schema::table('workshop_invitations', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn('slug');
        });
    }
};
