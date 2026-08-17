<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Which agenda sessions (agenda_items) a mobile-app room account (users) is
 * explicitly assigned to manage / track.
 *
 * A room account with NO rows here manages ALL sessions (default). Once the
 * super admin assigns sessions here, the account can only track these sessions.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agenda_item_room_account', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agenda_item_id')->constrained('agenda_items')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['agenda_item_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agenda_item_room_account');
    }
};
