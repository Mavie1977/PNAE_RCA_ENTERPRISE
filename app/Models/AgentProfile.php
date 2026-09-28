<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgentProfile extends Model
{
    use HasFactory;

    public const STATUS_ACTIVE = 'active';
    public const STATUS_SECONDMENT = 'secondment';
    public const STATUS_AVAILABLE = 'available';
    public const STATUS_SUSPENDED = 'suspended';
    public const STATUS_RETIRED = 'retired';
    public const STATUS_DISMISSED = 'dismissed';

    protected $fillable = [
        'user_id',
        'ministry_id',
        'matricule',
        'service',
        'job_title',
        'grade',
        'category',
        'administrative_status',
        'recruitment_date',
        'appointment_date',
        'hire_reference',
        'birth_date',
        'place_of_birth',
        'notes',
		'service_id',
		'category_id',
        'grade_id',
        'function_id',
    ];

    protected function casts(): array
    {
        return [
            'recruitment_date' => 'date',
            'appointment_date' => 'date',
            'birth_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function ministry(): BelongsTo
    {
        return $this->belongsTo(Ministry::class);
    }
	
	public function serviceEntity(): BelongsTo
{
    return $this->belongsTo(
        Service::class,
        'service_id'
    );
}

public function categoryEntity(): BelongsTo
{
    return $this->belongsTo(
        HrCategory::class,
        'category_id'
    );
}

public function gradeEntity(): BelongsTo
{
    return $this->belongsTo(
        HrGrade::class,
        'grade_id'
    );
}

public function functionEntity(): BelongsTo
{
    return $this->belongsTo(
        HrFunction::class,
        'function_id'
    );
}

public function hrFunction(): BelongsTo
{
    return $this->belongsTo(
        HrFunction::class,
        'function_id'
    );
}

}