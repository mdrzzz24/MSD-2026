<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('feedback_template_questions', function (Blueprint $table) {
            $table->unsignedTinyInteger('rating_max')->default(5)->after('allow_other');
        });

        Schema::table('agenda_item_questions', function (Blueprint $table) {
            $table->unsignedTinyInteger('rating_max')->default(5)->after('allow_other');
        });
    }

    public function down(): void
    {
        Schema::table('feedback_template_questions', function (Blueprint $table) {
            $table->dropColumn('rating_max');
        });

        Schema::table('agenda_item_questions', function (Blueprint $table) {
            $table->dropColumn('rating_max');
        });
    }
};
