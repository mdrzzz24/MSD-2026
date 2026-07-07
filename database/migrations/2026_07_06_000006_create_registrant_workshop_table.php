<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registrant_workshop', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registrant_id')->constrained()->onDelete('cascade');
            $table->foreignId('workshop_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['registrant_id', 'workshop_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registrant_workshop');
    }
};
