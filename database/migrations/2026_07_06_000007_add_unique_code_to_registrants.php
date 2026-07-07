<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registrants', function (Blueprint $table) {
            $table->string('unique_code', 20)->nullable()->unique()->after('gdpr');
        });
    }

    public function down(): void
    {
        Schema::table('registrants', function (Blueprint $table) {
            $table->dropColumn('unique_code');
        });
    }
};
