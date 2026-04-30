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
        Schema::create('gift_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gift_id')->constrained()->onDelete('cascade');
            $table->foreignId('from_user_id')->constrained('users')->onDelete('cascade'); // Кто дарит
            $table->foreignId('to_user_id')->constrained('users')->onDelete('cascade');   // Кому дарят
            $table->foreignId('event_id')->constrained()->onDelete('cascade');            // На каком событии
            $table->text('message')->nullable();    // Сообщение к подарку
            $table->timestamps();

            $table->index(['event_id', 'from_user_id', 'to_user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gift_user');
    }
};
