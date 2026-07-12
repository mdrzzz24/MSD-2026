<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feedback_template_questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('template_id');
            $table->string('question_text');
            $table->string('question_type'); // text, rating, choice, yes_no
            $table->json('options')->nullable(); // for choice type
            $table->unsignedSmallInteger('order')->default(0);
            $table->boolean('required')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedback_template_questions');
    }
};
