<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgentAdvancement extends Model
{
    public const TYPE_ADVANCEMENT = 'advancement';
    public const TYPE_PROMOTION = 'promotion';
    public const TYPE_RECLASSIFICATION = 'reclassification';

    protected $fillable = [
        'agent_id',
        'performed_by',
        'old_category_id',
        'new_category_id',
        'old_grade_id',
        'new_grade_id',
        'old_function_id',
        'new_function_id',
        'advancement_type',
        'reference',
        'reason',
        'effective_at',
    ];

           protected $casts = [
                'effective_at' => 'date',
   ];

          public function author(): BelongsTo
        {
             return $this->belongsTo(User::class, 'performed_by');
        }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function oldCategory(): BelongsTo
    {
        return $this->belongsTo(
            HrCategory::class,
            'old_category_id'
        );
    }

    public function newCategory(): BelongsTo
    {
        return $this->belongsTo(
            HrCategory::class,
            'new_category_id'
        );
    }

    public function oldGrade(): BelongsTo
    {
        return $this->belongsTo(
            HrGrade::class,
            'old_grade_id'
        );
    }

    public function newGrade(): BelongsTo
    {
        return $this->belongsTo(
            HrGrade::class,
            'new_grade_id'
        );
    }

    public function oldFunction(): BelongsTo
    {
        return $this->belongsTo(
            HrFunction::class,
            'old_function_id'
        );
    }

    public function newFunction(): BelongsTo
    {
        return $this->belongsTo(
            HrFunction::class,
            'new_function_id'
        );
    }
}