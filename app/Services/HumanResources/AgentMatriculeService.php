<?php

namespace App\Services\HumanResources;

use App\Models\AgentProfile;
use Illuminate\Support\Facades\DB;

class AgentMatriculeService
{
    public function generate(?int $year = null): string
    {
        $year ??= (int) now()->format('Y');

        return DB::transaction(function () use ($year) {

            $prefix = 'RCA-FP-' . $year . '-';

            $lastProfile = AgentProfile::query()
                ->where('matricule', 'like', $prefix . '%')
                ->lockForUpdate()
                ->orderByDesc('matricule')
                ->first();

            $number = 1;

            if ($lastProfile) {
                $number = ((int) substr($lastProfile->matricule, -6)) + 1;
            }

            return $prefix . str_pad(
                (string) $number,
                6,
                '0',
                STR_PAD_LEFT
            );
        });
    }
}