<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AgentTraining extends Model
{
    public const TYPE_TRAINING = 'training';
    public const TYPE_SEMINAR = 'seminar';
    public const TYPE_WORKSHOP = 'workshop';
    public const TYPE_CERTIFICATION = 'certification';
    public const TYPE_CONTINUING_EDUCATION = 'continuing_education';
    public const TYPE_OTHER = 'other';

    public const STATUS_PLANNED = 'planned';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'agent_id',
        'created_by',
        'title',
        'organization',
        'training_type',
        'start_date',
        'end_date',
        'status',
        'certificate_reference',
        'certificate_file_path',
        'certificate_original_name',
        'certificate_mime_type',
        'certificate_file_size',
        'description',
        'result',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
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

    public function skills(): HasMany
    {
        return $this->hasMany(
            AgentSkill::class,
            'training_id'
        );
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->training_type) {
            self::TYPE_SEMINAR => 'Séminaire',
            self::TYPE_WORKSHOP => 'Atelier',
            self::TYPE_CERTIFICATION => 'Certification',
            self::TYPE_CONTINUING_EDUCATION => 'Formation continue',
            self::TYPE_OTHER => 'Autre',
            default => 'Formation',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_IN_PROGRESS => 'En cours',
            self::STATUS_COMPLETED => 'Terminée',
            self::STATUS_CANCELLED => 'Annulée',
            default => 'Planifiée',
        };
    }
}