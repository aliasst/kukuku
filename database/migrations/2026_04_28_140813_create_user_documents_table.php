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
        Schema::create('user_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title'); // Название документа
            $table->string('file_path'); // Путь к файлу
            $table->string('file_name'); // Оригинальное имя файла
            $table->string('file_type'); // MIME тип (pdf, doc, jpg)
            $table->integer('file_size'); // Размер в байтах
            $table->string('category')->nullable(); // Категория: pass, snils, med_card и т.д.
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_documents');
    }
};
