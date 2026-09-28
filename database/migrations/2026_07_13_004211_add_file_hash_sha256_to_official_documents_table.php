<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('official_documents', function (Blueprint $table) {
            if (! Schema::hasColumn(
                'official_documents',
                'file_hash_sha256'
            )) {
                $table->string(
                    'file_hash_sha256',
                    64
                )->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('official_documents', function (Blueprint $table) {
            if (Schema::hasColumn(
                'official_documents',
                'file_hash_sha256'
            )) {
                $table->dropColumn('file_hash_sha256');
            }
        });
    }
};