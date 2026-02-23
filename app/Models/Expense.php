<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'page_id',
        'donor_id',
        'activity_id',
        'type',
        'amount'
    ];

    protected $casts = [
        'type' => 'string',
        'amount' => 'decimal:2'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function page()
    {
        return $this->belongsTo(Page::class);
    }

    public function donor()
    {
        return $this->belongsTo(Donor::class);
    }

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    public function getTypeNameAttribute()
    {
        $types = [
            'hosting' => 'Хостинг',
            'taxes' => 'Налоги',
            'links' => 'Ссылки',
            'service' => 'Сервис'
        ];

        return $types[$this->type] ?? $this->type;
    }
}
