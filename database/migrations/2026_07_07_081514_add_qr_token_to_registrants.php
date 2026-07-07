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
        Schema::table('registrants', function (Blueprint $table) {
            if (!Schema::hasColumn('registrants', 'qr_token')) {
                $table->string('qr_token', 16)->nullable()->unique()->after('plain_password');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('registrants', function (Blueprint $table) {
            if (Schema::hasColumn('registrants', 'qr_token')) {
                $table->dropColumn('qr_token');
            }
        });
    }
};
