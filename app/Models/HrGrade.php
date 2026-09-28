<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HrGrade extends Model
{
    protected $fillable = [
        'category_id',
        'code',
        'name',
        'description',
        'level',
        'active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(
            HrCategory::class,
            'category_id'
        );
    }

    public function agentProfiles(): HasMany
    {
        return $this->hasMany(
            AgentProfile::class,
            'grade_id'
        );
    }
}