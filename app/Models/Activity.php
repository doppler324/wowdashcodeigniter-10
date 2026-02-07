<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'page_id',
        'event_date',
        'category',
        'title',
        'description',
    ];

    protected $casts = [
        'event_date' => 'datetime',
    ];

    protected $appends = ['formatted_date'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function page()
    {
        return $this->belongsTo(Page::class);
    }

    public function getFormattedDateAttribute()
    {
        return $this->event_date->format('d.m.Y H:i');
    }
}
