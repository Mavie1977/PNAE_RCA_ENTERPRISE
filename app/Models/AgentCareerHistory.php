<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgentCareerHistory extends Model
{
    public const TYPE_RECRUITMENT = 'recruitment';

    public const TYPE_INITIAL_ASSIGNMENT = 'initial_assignment';

    public const TYPE_TRANSFER = 'transfer';

    public const TYPE_ACTIVATION = 'activation';

    public const TYPE_DEACTIVATION = 'deactivation';

    public const TYPE_PROFILE_UPDATE = 'profile_update';

    public const TYPE_PASSWORD_RESET = 'password_reset';
	
	public const TYPE_ADVANCEMENT = 'advancement';
	
    public const TYPE_PROMOTION = 'promotion';
	
    public const TYPE_RECLASSIFICATION = 'reclassification';
	
	public const TYPE_LEAVE_CREATED = 'leave_created';
    
	public const TYPE_LEAVE_APPROVED = 'leave_approved';
    
	public const TYPE_LEAVE_REJECTED = 'leave_rejected';
	
	public const TYPE_DISCIPLINARY_CREATED = 'disciplinary_created';
    
	public const TYPE_DISCIPLINARY_APPROVED = 'disciplinary_approved';
    
	public const TYPE_DISCIPLINARY_REJECTED = 'disciplinary_rejected';
	
	public const TYPE_RH_DOCUMENT_ADDED = 'rh_document_added';
	
    public const TYPE_RH_DOCUMENT_ARCHIVED = 'rh_document_archived';
	
	public const TYPE_TRAINING_ADDED = 'training_added';
    
	public const TYPE_TRAINING_COMPLETED = 'training_completed';
    
	public const TYPE_SKILL_ADDED = 'skill_added';
	

    protected $fillable = [
        'agent_id',
        'performed_by',
        'from_ministry_id',
        'to_ministry_id',
        'event_type',
        'title',
        'reason',
        'reference',
        'previous_active',
        'new_active',
        'metadata',
        'effective_at',
    ];

    protected function casts(): array
    {
        return [
            'previous_active' => 'boolean',
            'new_active' => 'boolean',
            'metadata' => 'array',
            'effective_at' => 'datetime',
        ];
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'agent_id'
        );
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'performed_by'
        );
    }

    public function fromMinistry(): BelongsTo
    {
        return $this->belongsTo(
            Ministry::class,
            'from_ministry_id'
        );
    }

    public function toMinistry(): BelongsTo
    {
        return $this->belongsTo(
            Ministry::class,
            'to_ministry_id'
        );
    }
}