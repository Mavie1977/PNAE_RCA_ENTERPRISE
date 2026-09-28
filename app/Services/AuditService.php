<?php

namespace App\Services;

use App\Models\AuditLog;

class AuditService
{
    public static function log(
        string $action,
        string $entity,
        ?int $entityId = null,
        array $payload = []
    ): void {
        AuditLog::create([
            'user_id'    => auth()->id(),
            'action'     => $action,
            'entity'     => $entity,
            'entity_id'  => $entityId,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'payload'    => $payload,
        ]);
    }
}