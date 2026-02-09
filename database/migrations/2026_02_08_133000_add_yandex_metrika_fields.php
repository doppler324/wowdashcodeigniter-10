<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('yandex_metrika_token')->nullable()->after('page');
            $table->string('yandex_metrika_counter')->nullable()->after('yandex_metrika_token');
            $table->string('yandex_metrika_period')->nullable()->after('yandex_metrika_counter');
            $table->text('yandex_metrika_metrics')->nullable()->after('yandex_metrika_period');
            $table->text('yandex_metrika_filters')->nullable()->after('yandex_metrika_metrics');
            $table->text('yandex_metrika_dimensions')->nullable()->after('yandex_metrika_filters');
            $table->string('yandex_metrika_sort')->nullable()->after('yandex_metrika_dimensions');
        });
    }

    public function down()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'yandex_metrika_token',
                'yandex_metrika_counter',
                'yandex_metrika_period',
                'yandex_metrika_metrics',
                'yandex_metrika_filters',
                'yandex_metrika_dimensions',
                'yandex_metrika_sort'
            ]);
        });
    }
};
