<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgentDisciplinaryAction extends Model
{
    public const TYPE_WARNING = 'warning';
    public const TYPE_REPRIMAND = 'reprimand';
    public const TYPE_SUSPENSION = 'suspension';
    public const TYPE_TEMPORARY_EXCLUSION = 'temporary_exclusion';
    public const TYPE_DISMISSAL = 'dismissal';
    public const TYPE_OTHER = 'other';

    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'agent_id',
        'created_by',
        'decided_by',
        'sanction_type',
        'status',
        'effective_at',
        'end_at',
        'reference',
        'reason',
        'decision_reason',
        'decided_at',
    ];

    protected function casts(): array
    {
        return [
            'effective_at' => 'date',
            'end_at' => 'date',
            'decided_at' => 'datetime',
        ];
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function decisionAuthor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'decided_by');
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->sanction_type) {
            self::TYPE_WARNING => 'Avertissement',
            self::TYPE_REPRIMAND => 'Blâme',
            self::TYPE_SUSPENSION => 'Suspension',
            self::TYPE_TEMPORARY_EXCLUSION => 'Exclusion temporaire',
            self::TYPE_DISMISSAL => 'Révocation / radiation',
            default => 'Autre',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_APPROVED => 'Validée',
            self::STATUS_REJECTED => 'Refusée',
            default => 'En attente',
        };
    }
}