<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->after('id');
            $table->string('api_url')->nullable()->after('user_id');
            $table->integer('groupby')->default(10)->after('api_url');
            $table->integer('lr')->nullable()->after('groupby');
            $table->string('domain')->default('ru')->after('lr');
            $table->string('lang')->default('ru')->after('domain');
            $table->string('device')->default('desktop')->after('lang');
            $table->integer('page')->default(0)->after('device');
        });
    }

    public function down()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['user_id', 'api_url', 'groupby', 'lr', 'domain', 'lang', 'device', 'page']);
        });
    }
};