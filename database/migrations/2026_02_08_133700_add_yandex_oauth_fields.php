<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('yandex_client_id')->nullable()->before('yandex_metrika_token');
            $table->string('yandex_client_secret')->nullable()->after('yandex_client_id');
            $table->string('yandex_redirect_uri')->nullable()->after('yandex_client_secret');
        });
    }

    public function down()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'yandex_client_id',
                'yandex_client_secret',
                'yandex_redirect_uri'
            ]);
        });
    }
};
