<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Keyword extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'keyword',
        'page_id',
        'is_main',
        'volume',
        'volume_exact',
        'cpc',
        'difficulty',
        'current_position',
        'best_position',
        'start_position',
        'trend',
        'region',
        'actual_url',
        'last_tracked_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_main' => 'boolean',
        'volume' => 'integer',
        'volume_exact' => 'integer',
        'cpc' => 'decimal:2',
        'difficulty' => 'integer',
        'current_position' => 'integer',
        'best_position' => 'integer',
        'start_position' => 'integer',
        'trend' => 'integer',
        'last_tracked_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the page that owns the keyword.
     */
    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }
}