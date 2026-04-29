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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Название
            $table->string('slug')->unique(); // URL-дружественное название
            $table->text('description'); // Краткое описание
            $table->longText('content'); // Полный текст
            $table->date('event_date'); // Дата события
            $table->time('event_time')->nullable(); // Время события
            $table->date('event_end_date')->nullable(); // Дата окончания (для многодневных)
            $table->string('location')->nullable(); // Адрес/место проведения
            $table->decimal('price', 10, 2)->default(0); // Стоимость
            $table->integer('max_participants')->nullable(); // Максимум участников
            $table->integer('current_participants')->default(0); // Текущее количество
            $table->string('image')->nullable(); // Изображение события
            $table->enum('type', ['upcoming', 'past', 'ongoing', 'cancelled'])->default('upcoming'); // ТИП СОБЫТИЯ
            $table->string('status')->default('active'); // active, completed, cancelled
            $table->boolean('is_free')->default(false); // Бесплатно ли
            $table->boolean('is_online')->default(false); // Онлайн или офлайн
            $table->string('online_link')->nullable(); // Ссылка для онлайн-участия
            $table->timestamps();

            // Индексы для быстрого поиска
            $table->index('type');
            $table->index('event_date');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
