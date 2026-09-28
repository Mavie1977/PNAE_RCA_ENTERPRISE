<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfficialDocument extends Model
{
    protected $fillable = [
        'application_id',
        'generated_by',
        'official_number',
        'verification_token',
        'title',
        'file_path',
        'mime_type',
        'hash_sha256',
        'signature_code',
        'status',
        'issued_at',
        'revoked_at',
        'revocation_reason',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
        'revoked_at' => 'datetime',
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function generator()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    public function isActive(): bool
    {
        return $this->status === 'actif'
            && $this->revoked_at === null;
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'actif' => 'Valide',
            'revoque' => 'Révoqué',
            default => ucfirst($this->status),
        };
    }
}