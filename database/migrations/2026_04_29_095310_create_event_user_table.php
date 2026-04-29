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
        Schema::create('event_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Статус участия
            $table->enum('status', [
                'registered',    // Зарегистрирован (просто записался)
                'confirmed',     // Подтвердил участие
                'attended',      // Принял участие (посетил)
                'cancelled',     // Отменил запись
                'no_show'        // Не пришел (записался но не пришел)
            ])->default('registered');

            // Дополнительные поля
            $table->string('ticket_number')->nullable(); // Номер билета
            $table->datetime('registered_at')->nullable(); // Дата регистрации
            $table->datetime('confirmed_at')->nullable(); // Дата подтверждения
            $table->datetime('attended_at')->nullable(); // Дата участия
            $table->text('notes')->nullable(); // Заметки

            $table->timestamps();

            // Уникальная пара (пользователь не может дважды зарегистрироваться на одно событие)
            $table->unique(['event_id', 'user_id']);

            // Индексы для быстрого поиска
            $table->index('status');
            $table->index('registered_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_user');
    }
};
