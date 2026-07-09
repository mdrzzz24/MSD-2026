<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registrant_workshop', function (Blueprint $table) {
            if (!Schema::hasColumn('registrant_workshop', 'status')) {
                $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->after('workshop_id');
            }
            if (!Schema::hasColumn('registrant_workshop', 'admin_notes')) {
                $table->text('admin_notes')->nullable()->after('status');
            }
            if (!Schema::hasColumn('registrant_workshop', 'processed_by')) {
                $table->foreignId('processed_by')->nullable()->after('admin_notes')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('registrant_workshop', 'processed_at')) {
                $table->timestamp('processed_at')->nullable()->after('processed_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('registrant_workshop', function (Blueprint $table) {
            $table->dropForeign(['processed_by']);
            $table->dropColumn(['status', 'admin_notes', 'processed_by', 'processed_at']);
        });
    }
};
