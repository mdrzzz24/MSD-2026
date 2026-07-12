<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('utm_link_user', function (Blueprint $table) {
            $table->foreignId('utm_link_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('utm_link_user', function (Blueprint $table) {
            $table->dropForeign(['utm_link_id']);
            $table->dropForeign(['user_id']);
            $table->dropColumn(['utm_link_id', 'user_id']);
        });
    }
};
