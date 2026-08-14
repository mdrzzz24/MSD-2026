<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('feedback_template_questions', function (Blueprint $table) {
            $table->boolean('allow_other')->default(false)->after('options');
        });

        Schema::table('agenda_item_questions', function (Blueprint $table) {
            $table->boolean('allow_other')->default(false)->after('options');
        });
    }

    public function down(): void
    {
        Schema::table('feedback_template_questions', function (Blueprint $table) {
            $table->dropColumn('allow_other');
        });

        Schema::table('agenda_item_questions', function (Blueprint $table) {
            $table->dropColumn('allow_other');
        });
    }
};
