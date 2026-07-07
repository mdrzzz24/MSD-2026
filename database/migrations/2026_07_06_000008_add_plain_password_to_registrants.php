<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registrants', function (Blueprint $table) {
            $table->string('plain_password', 100)->nullable()->after('password');
        });
    }

    public function down(): void
    {
        Schema::table('registrants', function (Blueprint $table) {
            $table->dropColumn('plain_password');
        });
    }
};
