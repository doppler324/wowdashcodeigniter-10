<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Donor extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'page_id',
        'link',
        'type',
        'authority',
        'anchor',
        'link_type',
        'added_at',
        'is_image_link',
        'status',
        'is_redirect',
        'duration',
        'check_date',
        'placement_type',
        'status_code',
        'price',
        'marketplace',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'authority' => 'integer',
        'added_at' => 'date',
        'is_image_link' => 'boolean',
        'is_redirect' => 'boolean',
        'check_date' => 'date',
        'status_code' => 'integer',
        'price' => 'decimal:2',
    ];

    /**
     * Get the page that the donor belongs to.
     */
    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    /**
     * Get the project that the donor belongs to (through page).
     */
    public function project()
    {
        return $this->page->project;
    }
}
