<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Create speakers table
        if (!Schema::hasTable('speakers')) {
            Schema::create('speakers', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('title')->nullable();
                $table->string('company')->nullable();
                $table->string('photo')->nullable();
                $table->text('bio')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // Create pivot table
        if (!Schema::hasTable('agenda_item_speaker')) {
            Schema::create('agenda_item_speaker', function (Blueprint $table) {
                $table->id();
                $table->foreignId('agenda_item_id')->constrained()->cascadeOnDelete();
                $table->foreignId('speaker_id')->constrained()->cascadeOnDelete();
                $table->integer('order')->default(0);
                $table->timestamps();
                $table->unique(['agenda_item_id', 'speaker_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('agenda_item_speaker');
        Schema::dropIfExists('speakers');
    }
};
