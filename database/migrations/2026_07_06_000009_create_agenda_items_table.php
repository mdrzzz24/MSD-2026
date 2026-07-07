<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agenda_items', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('category')->nullable();   // 'general','workshop','platinum','gold','break'
            $table->string('room')->nullable();        // null = full row across all columns
            $table->time('start_time');
            $table->time('end_time');
            $table->date('date')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agenda_items');
    }
};
