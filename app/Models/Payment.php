<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
   protected $fillable = [
    'application_id',
    'user_id',
    'reference',
    'provider',
    'amount',
    'currency',
    'method',
    'status',
    'provider_reference',
    'payer_phone',
    'payer_name',
    'metadata',
    'failure_reason',
    'paid_at',
    'cancelled_at',
];

    protected $casts = [
        'amount' => 'decimal:2',
        'metadata' => 'array',
        'paid_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'en_attente' => 'En attente',
            'initie' => 'Initié',
            'paye' => 'Payé',
            'echoue' => 'Échoué',
            'annule' => 'Annulé',
            'rembourse' => 'Remboursé',
            default => ucfirst(str_replace('_', ' ', $this->status)),
        };
    }

    public function getMethodLabelAttribute(): string
    {
        return match ($this->method) {
            'orange_money' => 'Orange Money',
            'mobile_money' => 'Mobile Money',
            'carte' => 'Carte bancaire',
            'virement' => 'Virement bancaire',
            default => ucfirst(str_replace('_', ' ', $this->method)),
        };
    }

    public function getFormattedAmountAttribute(): string
    {
        return number_format(
            (float) $this->amount,
            0,
            ',',
            ' '
        ) . ' ' . $this->currency;
    }

    public function isPaid(): bool
    {
        return $this->status === 'paye';
    }
}