<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('feedback_template_questions', function (Blueprint $table) {
            $table->unsignedBigInteger('parent_question_id')->nullable()->after('options');
            $table->string('trigger_value')->nullable()->after('parent_question_id');
        });

        Schema::table('agenda_item_questions', function (Blueprint $table) {
            $table->unsignedBigInteger('parent_question_id')->nullable()->after('options');
            $table->string('trigger_value')->nullable()->after('parent_question_id');
        });
    }

    public function down(): void
    {
        Schema::table('feedback_template_questions', function (Blueprint $table) {
            $table->dropColumn(['parent_question_id', 'trigger_value']);
        });

        Schema::table('agenda_item_questions', function (Blueprint $table) {
            $table->dropColumn(['parent_question_id', 'trigger_value']);
        });
    }
};
