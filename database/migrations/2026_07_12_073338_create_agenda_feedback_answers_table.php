<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agenda_feedback_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agenda_feedback_id')->constrained('agenda_feedback')->cascadeOnDelete();
            $table->unsignedBigInteger('agenda_item_question_id');
            $table->text('answer_value')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agenda_feedback_answers');
    }
};
