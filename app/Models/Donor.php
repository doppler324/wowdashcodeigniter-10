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
        'link',
        'type',
        'link_type',
        'added_at',
        'is_image_link',
        'status',
        'is_redirect',
        'check_date',
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
        'added_at' => 'date',
        'is_image_link' => 'boolean',
        'is_redirect' => 'boolean',
        'check_date' => 'date',
        'status_code' => 'integer',
        'price' => 'decimal:2',
    ];

    /**
     * Get the pages that the donor links to.
     */
    public function pages()
    {
        return $this->belongsToMany(Page::class, 'donor_page_anchor')
            ->withPivot('anchor')
            ->withTimestamps();
    }

    /**
     * Get the project that the donor belongs to (through pages).
     */
    public function project()
    {
        return $this->pages->first()->project;
    }
}
