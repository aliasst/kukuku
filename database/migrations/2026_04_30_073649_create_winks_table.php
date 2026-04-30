<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('winks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('to_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->boolean('is_viewed')->default(false);
            $table->enum('status', ['pending', 'accepted', 'ignored'])->default('pending');
            $table->timestamps();

            // Уникальная пара: нельзя подмигнуть дважды одному пользователю на одном событии
            $table->unique(['from_user_id', 'to_user_id', 'event_id'], 'unique_wink');

            // Индексы для быстрого поиска
            $table->index(['to_user_id', 'is_viewed']);
            $table->index('event_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('winks');
    }
};
