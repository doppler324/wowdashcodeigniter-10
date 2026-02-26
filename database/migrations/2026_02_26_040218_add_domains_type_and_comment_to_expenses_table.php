<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('expenses', function (Blueprint $table) {
            // Изменяем enum для добавления 'domains'
            $table->enum('type', ['hosting', 'taxes', 'links', 'service', 'domains'])->change();
            // Добавляем поле comment
            $table->text('comment')->nullable()->after('amount');
        });
    }

    public function down()
    {
        Schema::table('expenses', function (Blueprint $table) {
            // Возвращаем старый enum (удаляем 'domains')
            $table->enum('type', ['hosting', 'taxes', 'links', 'service'])->change();
            // Удаляем поле comment
            $table->dropColumn('comment');
        });
    }
};