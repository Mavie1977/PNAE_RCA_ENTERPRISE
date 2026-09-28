<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgentLeave extends Model
{
    public const TYPE_ANNUAL = 'annual_leave';
    public const TYPE_SICK = 'sick_leave';
    public const TYPE_MATERNITY = 'maternity_leave';
    public const TYPE_PATERNITY = 'paternity_leave';
    public const TYPE_ADMINISTRATIVE = 'administrative_leave';
    public const TYPE_TRAINING = 'training';
    public const TYPE_AUTHORIZED_ABSENCE = 'authorized_absence';
    public const TYPE_UNJUSTIFIED_ABSENCE = 'unjustified_absence';
    public const TYPE_OTHER = 'other';

    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'agent_id',
        'created_by',
        'decided_by',
        'leave_type',
        'start_date',
        'end_date',
        'days_count',
        'status',
        'reference',
        'reason',
        'decision_reason',
        'decided_at',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
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
        return match ($this->leave_type) {
            self::TYPE_ANNUAL => 'Congé annuel',
            self::TYPE_SICK => 'Congé maladie',
            self::TYPE_MATERNITY => 'Congé maternité',
            self::TYPE_PATERNITY => 'Congé paternité',
            self::TYPE_ADMINISTRATIVE => 'Congé administratif',
            self::TYPE_TRAINING => 'Formation',
            self::TYPE_AUTHORIZED_ABSENCE => 'Absence autorisée',
            self::TYPE_UNJUSTIFIED_ABSENCE => 'Absence injustifiée',
            default => 'Autre',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_APPROVED => 'Validé',
            self::STATUS_REJECTED => 'Refusé',
            default => 'En attente',
        };
    }
}