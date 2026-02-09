<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'api_url',
        'groupby',
        'lr',
        'domain',
        'lang',
        'device',
        'page',
        'yandex_client_id',
        'yandex_client_secret',
        'yandex_redirect_uri',
        'yandex_metrika_token',
        'yandex_metrika_counter',
        'yandex_metrika_period',
        'yandex_metrika_metrics',
        'yandex_metrika_filters',
        'yandex_metrika_dimensions',
        'yandex_metrika_sort',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}