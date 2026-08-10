<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Audit trail for every scan / badge-print made by the mobile app (and sync).
     * Powers the recent-activity feed and print troubleshooting.
     */
    public function up(): void
    {
        Schema::create('scan_logs', function (Blueprint $table) {
            $table->id();
            $table->string('action', 50);            // registration_scan | agenda_scan | agenda_trackout | booth_scan | workshop_register | mqtt_test
            $table->unsignedBigInteger('registrant_id')->nullable();
            $table->string('registrant_name')->nullable();
            $table->string('qr_token')->nullable();
            $table->unsignedBigInteger('item_id')->nullable();  // agenda / booth / workshop id
            $table->string('item_type', 30)->nullable();        // agenda | booth | workshop | printer | registration
            $table->string('item_label')->nullable();           // title / name for display
            $table->string('source', 20)->default('mobile');    // mobile | sync | web
            $table->string('client_id')->nullable();            // offline sync batch id
            $table->unsignedBigInteger('admin_id')->nullable(); // which admin / printer was targeted
            $table->boolean('success')->default(true);
            $table->boolean('printed')->default(false);         // whether an MQTT badge print was sent
            $table->text('message')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->index(['action']);
            $table->index(['registrant_id']);
            $table->index(['created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scan_logs');
    }
};
