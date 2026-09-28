<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ministry extends Model
{
    use HasFactory;

    protected $fillable = [
    'name',
    'slug',
    'code',
    'description',
    'email',
    'phone',
    'active',
];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
        ];
    }

    public function procedures(): HasMany
    {
        return $this->hasMany(Procedure::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}