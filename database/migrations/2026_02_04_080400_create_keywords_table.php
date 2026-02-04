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
        Schema::create('keywords', function (Blueprint $table) {
            $table->id();
            $table->string('keyword');
            $table->foreignId('page_id')->constrained('pages')->cascadeOnDelete();
            $table->boolean('is_main')->default(false);
            $table->integer('volume')->nullable();
            $table->integer('volume_exact')->nullable();
            $table->decimal('cpc', 10, 2)->nullable();
            $table->integer('difficulty')->nullable();
            $table->integer('current_position')->nullable();
            $table->integer('best_position')->nullable();
            $table->integer('start_position')->nullable();
            $table->integer('trend')->nullable();
            $table->string('region')->nullable();
            $table->string('actual_url')->nullable();
            $table->datetime('last_tracked_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('keywords');
    }
};