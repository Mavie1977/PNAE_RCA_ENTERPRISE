<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agent_profiles', function (Blueprint $table) {
            $table->foreignId('service_id')
                ->nullable()
                ->after('ministry_id')
                ->constrained('services')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('agent_profiles', function (Blueprint $table) {
            $table->dropConstrainedForeignId('service_id');
        });
    }
};