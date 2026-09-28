<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgentRhDocument extends Model
{
    public const TYPE_RECRUITMENT = 'recruitment_order';
    public const TYPE_APPOINTMENT = 'appointment_decision';
    public const TYPE_ASSIGNMENT = 'assignment_decision';
    public const TYPE_TRANSFER = 'transfer_decision';
    public const TYPE_ADVANCEMENT = 'advancement_order';
    public const TYPE_LEAVE = 'leave_decision';
    public const TYPE_DISCIPLINARY = 'disciplinary_decision';
    public const TYPE_CERTIFICATE = 'certificate';
    public const TYPE_DIPLOMA = 'diploma';
    public const TYPE_IDENTITY = 'identity_document';
    public const TYPE_OTHER = 'other';

    protected $fillable = [
        'agent_id',
        'uploaded_by',
        'document_type',
        'title',
        'reference',
        'document_date',
        'file_path',
        'original_name',
        'mime_type',
        'file_size',
        'notes',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'document_date' => 'date',
            'active' => 'boolean',
        ];
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->document_type) {
            self::TYPE_RECRUITMENT => 'Arrêté de recrutement',
            self::TYPE_APPOINTMENT => 'Décision de nomination',
            self::TYPE_ASSIGNMENT => 'Décision d’affectation',
            self::TYPE_TRANSFER => 'Décision de mutation',
            self::TYPE_ADVANCEMENT => 'Arrêté d’avancement',
            self::TYPE_LEAVE => 'Décision de congé',
            self::TYPE_DISCIPLINARY => 'Décision disciplinaire',
            self::TYPE_CERTIFICATE => 'Attestation / certificat',
            self::TYPE_DIPLOMA => 'Diplôme',
            self::TYPE_IDENTITY => 'Pièce d’identité',
            default => 'Autre document',
        };
    }
}