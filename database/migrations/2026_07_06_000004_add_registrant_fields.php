<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registrants', function (Blueprint $table) {
            $table->string('first_name')->nullable()->after('name');
            $table->string('last_name')->nullable()->after('first_name');
            $table->string('job_title')->nullable()->after('last_name');
            $table->string('company')->nullable()->after('job_title');
            $table->string('industry')->nullable()->after('organization');
            $table->string('employees')->nullable()->after('industry');
            $table->boolean('gdpr')->default(false)->after('employees');
            $table->string('password')->nullable()->after('gdpr');
            $table->rememberToken()->after('password');
        });
    }

    public function down(): void
    {
        Schema::table('registrants', function (Blueprint $table) {
            $table->dropColumn([
                'first_name', 'last_name', 'job_title', 'company',
                'industry', 'employees', 'gdpr', 'password', 'remember_token',
            ]);
        });
    }
};
