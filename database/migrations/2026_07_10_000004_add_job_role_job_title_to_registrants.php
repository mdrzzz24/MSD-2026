<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('registrants', 'job_role')) {
            Schema::table('registrants', function (Blueprint $table) {
                $table->string('job_role')->nullable()->after('job_title');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('registrants', 'job_role')) {
            Schema::table('registrants', function (Blueprint $table) {
                $table->dropColumn('job_role');
            });
        }
    }
};
