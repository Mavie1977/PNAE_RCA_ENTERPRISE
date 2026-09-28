<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'role')) {
                $table->string('role')
                    ->default('citoyen');
            }

            if (! Schema::hasColumn('users', 'active')) {
                $table->boolean('active')
                    ->default(true);
            }

            if (! Schema::hasColumn('users', 'ministry_id')) {
                $table->foreignId('ministry_id')
                    ->nullable()
                    ->constrained('ministries')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'ministry_id')) {
                $table->dropConstrainedForeignId('ministry_id');
            }

            if (Schema::hasColumn('users', 'active')) {
                $table->dropColumn('active');
            }

            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }
        });
    }
};