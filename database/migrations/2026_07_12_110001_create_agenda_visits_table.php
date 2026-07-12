<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('agenda_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agenda_item_id')->constrained()->onDelete('cascade');
            $table->foreignId('registrant_id')->constrained()->onDelete('cascade');
            $table->timestamp('visited_at')->nullable();
            $table->timestamps();

            $table->unique(['agenda_item_id', 'registrant_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agenda_visits');
    }
};
