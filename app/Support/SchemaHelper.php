<?php

namespace App\Support;

use Illuminate\Support\Facades\Schema;

class SchemaHelper
{
    public static function tableExists(string $table): bool
    {
        return Schema::hasTable($table);
    }
}