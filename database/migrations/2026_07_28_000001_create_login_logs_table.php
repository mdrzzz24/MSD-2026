<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('login_logs', function (Blueprint $table) {
            $table->id();
            $table->string('user_type');           // 'admin' or 'registrant'
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('name');
            $table->string('email');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('login_at')->nullable();
            $table->timestamp('logout_at')->nullable();
            $table->string('session_id', 255)->nullable();
            $table->timestamps();

            $table->index(['user_type', 'user_id']);
            $table->index('login_at');
            $table->index('session_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('login_logs');
    }
};
