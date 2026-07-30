<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registrants', function (Blueprint $table) {
            $table->text('client_remark')->nullable()->after('admin_notes');
            $table->string('client_remark_action')->nullable()->after('client_remark');
            $table->foreignId('client_remarked_by')->nullable()->after('client_remark_action')->constrained('users')->nullOnDelete();
            $table->timestamp('client_remarked_at')->nullable()->after('client_remarked_by');
        });
    }

    public function down(): void
    {
        Schema::table('registrants', function (Blueprint $table) {
            $table->dropColumn(['client_remark', 'client_remark_action', 'client_remarked_by', 'client_remarked_at']);
        });
    }
};
