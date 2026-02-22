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
        Schema::table('donors', function (Blueprint $table) {
            $table->dropForeign(['page_id']);
            $table->dropColumn(['page_id', 'anchor']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('donors', function (Blueprint $table) {
            $table->foreignId('page_id')->after('id')->constrained()->onDelete('cascade');
            $table->string('anchor')->nullable()->after('authority');
        });
    }
};
