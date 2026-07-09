<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add registrable fields to agenda_items
        Schema::table('agenda_items', function (Blueprint $table) {
            if (!Schema::hasColumn('agenda_items', 'is_registrable')) {
                $table->boolean('is_registrable')->default(false)->after('colspan');
            }
            if (!Schema::hasColumn('agenda_items', 'capacity')) {
                $table->integer('capacity')->default(0)->after('is_registrable');
            }
            if (!Schema::hasColumn('agenda_items', 'registration_open')) {
                $table->boolean('registration_open')->default(true)->after('capacity');
            }
            if (!Schema::hasColumn('agenda_items', 'agenda_type')) {
                $table->string('agenda_type', 50)->nullable()->after('category')->comment('track, workshop, keynote, etc.');
            }
        });

        // Create pivot table for agenda item registrations
        if (!Schema::hasTable('agenda_item_registrant')) {
            Schema::create('agenda_item_registrant', function (Blueprint $table) {
                $table->id();
                $table->foreignId('agenda_item_id')->constrained()->cascadeOnDelete();
                $table->foreignId('registrant_id')->constrained()->cascadeOnDelete();
                $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
                $table->text('admin_notes')->nullable();
                $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('processed_at')->nullable();
                $table->timestamps();
                $table->unique(['agenda_item_id', 'registrant_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('agenda_item_registrant');

        Schema::table('agenda_items', function (Blueprint $table) {
            $table->dropColumn(['is_registrable', 'capacity', 'registration_open', 'agenda_type']);
        });
    }
};
