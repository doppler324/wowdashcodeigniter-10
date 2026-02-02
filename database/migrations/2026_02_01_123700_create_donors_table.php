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
        Schema::create('donors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained()->onDelete('cascade');
            $table->string('link'); // Ссылка
            $table->string('type'); // Тип: Статья, форум, каталог
            $table->integer('authority')->nullable()->default(0); // Авторитетность
            $table->string('anchor')->nullable(); // Анкор
            $table->string('link_type')->default('dofollow'); // Тип ссылки: dofollow или nofollow
            $table->date('added_at')->nullable(); // Дата добавления
            $table->boolean('is_image_link')->default(false); // Является ли ссылка картинкой
            $table->string('status')->default('active'); // Статус ссылки: Активна, неактивна, удалена
            $table->boolean('is_redirect')->default(false); // Ведет ли ссылка напрямую или через редирект
            $table->string('duration')->nullable(); // Срок действия ссылки
            $table->date('check_date')->nullable(); // Дата проверки ссылки
            $table->string('placement_type')->nullable(); // Как размещена ссылка: статья, обзор, контекстная
            $table->integer('status_code')->nullable(); // Код ответа страницы-донора
            $table->decimal('price', 8, 2)->nullable(); // Цена
            $table->string('marketplace')->nullable(); // Площадка закупки: Miralinks, Collaborator, Gogetlinks, прямой аутрич
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donors');
    }
};
