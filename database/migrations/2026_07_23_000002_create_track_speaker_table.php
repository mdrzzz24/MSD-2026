<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('track_speaker')) {
            Schema::create('track_speaker', function (Blueprint $table) {
                $table->id();
                $table->foreignId('track_id')->constrained()->cascadeOnDelete();
                $table->foreignId('speaker_id')->constrained()->cascadeOnDelete();
                $table->unsignedInteger('order')->default(0);
                $table->timestamps();
                $table->unique(['track_id', 'speaker_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('track_speaker');
    }
};
