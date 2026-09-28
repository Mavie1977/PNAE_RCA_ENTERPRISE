<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
    ];

    /**
     * Retourne la valeur d'un paramètre.
     */
    public static function getValue(string $key, $default = null)
    {
        $setting = self::where('key', $key)->first();

        return $setting?->value ?? $default;
    }

    /**
     * Enregistre ou met à jour un paramètre.
     */
    public static function setValue(string $key, $value, string $type = 'text')
    {
        return self::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'type'  => $type,
            ]
        );
    }
}