<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApplicationDocument extends Model
{
    protected $fillable = [
        'application_id',
        'label',
        'file_path',
        'status',
        'note',
        'original_name',
        'mime_type',
        'size',
    ];

    protected $casts = [
        'size' => 'integer',
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->original_name
            ?? $this->label
            ?? 'Document';
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'attendu' => 'Attendu',
            'recu' => 'Reçu',
            'valide' => 'Validé',
            'rejete' => 'Rejeté',
            default => ucfirst((string) $this->status),
        };
    }

    public function getFormattedSizeAttribute(): string
    {
        if (! $this->size) {
            return '-';
        }

        if ($this->size < 1024) {
            return $this->size . ' octets';
        }

        if ($this->size < 1024 * 1024) {
            return number_format(
                $this->size / 1024,
                1,
                ',',
                ' '
            ) . ' Ko';
        }

        return number_format(
            $this->size / (1024 * 1024),
            1,
            ',',
            ' '
        ) . ' Mo';
    }
}