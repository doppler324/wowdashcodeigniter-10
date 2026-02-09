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
        Schema::create('api_cache', function (Blueprint $table) {
            $table->string('request_hash', 64)->primary();
            $table->json('response_json');
            $table->timestamp('updated_at')->useCurrent();
            $table->text('params_info')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_cache');
    }
};
