<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Page extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'project_id',
        'parent_id',
        'url',
        'type',
        'title',
        'keywords',
        'status_code',
        'is_indexable',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($page) {
            $page->nesting_level = $page->calculateNestingLevel($page->parent_id);
        });

        static::updating(function ($page) {
            $page->nesting_level = $page->calculateNestingLevel($page->parent_id);
        });
    }

    /**
     * Calculate nesting level based on parent.
     */
    protected function calculateNestingLevel($parentId)
    {
        if (!$parentId) {
            return 0;
        }

        $parentPage = Page::find($parentId);
        return $parentPage ? $parentPage->nesting_level + 1 : 0;
    }

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'is_indexable' => 'boolean',
        'incoming_links_count' => 'integer',
        'status_code' => 'integer',
        'nesting_level' => 'integer',
    ];

    /**
     * Get the project that owns the page.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the donors for the page.
     */
    public function donors()
    {
        return $this->hasMany(Donor::class);
    }

    /**
     * Get the parent page.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Page::class, 'parent_id');
    }

    /**
     * Get the child pages.
     */
    public function children(): HasMany
    {
        return $this->hasMany(Page::class, 'parent_id');
    }

    /**
     * Check if page has children.
     */
    public function hasChildren(): bool
    {
        return $this->children()->exists();
    }

    /**
     * Get the keywords for the page.
     */
    public function keywords()
    {
        return $this->hasMany(Keyword::class);
    }

    /**
     * Get the main keyword for the page.
     */
    public function mainKeyword()
    {
        return $this->hasOne(Keyword::class)->where('is_main', true);
    }

    /**
     * Get the number of incoming links.
     */
    public function getIncomingLinksCountAttribute()
    {
        return $this->donors()->count();
    }
}
