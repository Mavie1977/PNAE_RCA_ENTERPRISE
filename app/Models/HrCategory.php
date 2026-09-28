<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HrCategory extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
        ];
    }

    public function grades(): HasMany
    {
        return $this->hasMany(
            HrGrade::class,
            'category_id'
        );
    }

    public function agentProfiles(): HasMany
    {
        return $this->hasMany(
            AgentProfile::class,
            'category_id'
        );
    }
}