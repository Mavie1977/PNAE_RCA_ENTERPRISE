<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('application_documents', function (Blueprint $table) {
            if (! Schema::hasColumn('application_documents', 'original_name')) {
                $table->string('original_name')->nullable();
            }

            if (! Schema::hasColumn('application_documents', 'mime_type')) {
                $table->string('mime_type')->nullable();
            }

            if (! Schema::hasColumn('application_documents', 'size')) {
                $table->unsignedBigInteger('size')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('application_documents', function (Blueprint $table) {
            $table->dropColumn(['original_name', 'mime_type', 'size']);
        });
    }
};