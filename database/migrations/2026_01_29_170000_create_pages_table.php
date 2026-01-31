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
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('pages')->onDelete('cascade');
            $table->string('url');
            $table->enum('type', ['home', 'section', 'card']);
            $table->string('title')->nullable();
            $table->integer('incoming_links_count')->default(0);
            $table->text('keywords')->nullable();
            $table->integer('status_code')->nullable();
            $table->boolean('is_indexable')->default(true);
            $table->integer('nesting_level')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
