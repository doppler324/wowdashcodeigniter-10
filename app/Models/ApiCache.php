<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiCache extends Model
{
    use HasFactory;

    /**
     * Отключаем автоматическое управление timestamps (created_at, updated_at).
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Название таблицы в БД.
     *
     * @var string
     */
    protected $table = 'api_cache';

    /**
     * Первичный ключ таблицы.
     *
     * @var string
     */
    protected $primaryKey = 'request_hash';

    /**
     * Тип первичного ключа (не автоинкремент).
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Отключаем автоинкремент.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Атрибуты, которые можно массово заполнять.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'request_hash',
        'response_json',
        'params_info',
        'updated_at',
    ];

    /**
     * Атрибуты, которые должны быть приведены к определённым типам.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'response_json' => 'array',
        'updated_at' => 'datetime',
    ];
}
