<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoginAudit extends Model
{
    protected $fillable = [
        'user_id',
        'email',
        'ip_address',
        'user_agent',
        'event',
        'successful',
        'failure_reason',
        'occurred_at',
    ];

    protected function casts(): array
    {
        return [
            'successful' => 'boolean',
            'occurred_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}