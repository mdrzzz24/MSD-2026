<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Store clients' decision selections that have NOT been submitted yet.
     * Previously kept in the cache with a 10-minute TTL (so they vanished); now
     * persisted in the DB so they survive until the client submits or removes them.
     */
    public function up(): void
    {
        Schema::create('client_pending_marks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('registrant_id')->constrained('registrants')->cascadeOnDelete();
            $table->string('action'); // approve | reject | waitlist
            $table->text('reason')->nullable(); // required for reject
            $table->timestamps();

            // One client can hold at most one pending selection per registrant.
            $table->unique(['user_id', 'registrant_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_pending_marks');
    }
};
