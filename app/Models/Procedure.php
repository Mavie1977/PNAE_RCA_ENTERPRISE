<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Procedure extends Model
{
    use HasFactory;

    protected $fillable = [
        'ministry_id',
        'title',
        'slug',
        'description',
        'fee',
        'processing_days',
        'payment_required',
        'official_document_required',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'fee' => 'decimal:2',
            'processing_days' => 'integer',
            'payment_required' => 'boolean',
            'official_document_required' => 'boolean',
            'active' => 'boolean',
        ];
    }

    public function ministry(): BelongsTo
    {
        return $this->belongsTo(Ministry::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }
}