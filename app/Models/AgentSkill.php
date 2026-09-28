<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgentSkill extends Model
{
    public const LEVEL_BEGINNER = 'beginner';
    public const LEVEL_INTERMEDIATE = 'intermediate';
    public const LEVEL_ADVANCED = 'advanced';
    public const LEVEL_EXPERT = 'expert';

    protected $fillable = [
        'agent_id',
        'training_id',
        'created_by',
        'name',
        'category',
        'level',
        'acquired_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'acquired_at' => 'date',
        ];
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function training(): BelongsTo
    {
        return $this->belongsTo(
            AgentTraining::class,
            'training_id'
        );
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getLevelLabelAttribute(): string
    {
        return match ($this->level) {
            self::LEVEL_BEGINNER => 'Débutant',
            self::LEVEL_ADVANCED => 'Avancé',
            self::LEVEL_EXPERT => 'Expert',
            default => 'Intermédiaire',
        };
    }
}