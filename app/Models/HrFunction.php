<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HrFunction extends Model
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

    public function agentProfiles(): HasMany
    {
        return $this->hasMany(
            AgentProfile::class,
            'function_id'
        );
    }
}