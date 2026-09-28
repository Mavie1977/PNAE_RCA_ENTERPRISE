<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agent_profiles', function (Blueprint $table) {

            $table->foreignId('category_id')
                ->nullable()
                ->after('service_id')
                ->constrained('hr_categories')
                ->nullOnDelete();

            $table->foreignId('grade_id')
                ->nullable()
                ->after('category_id')
                ->constrained('hr_grades')
                ->nullOnDelete();

            $table->foreignId('function_id')
                ->nullable()
                ->after('grade_id')
                ->constrained('hr_functions')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('agent_profiles', function (Blueprint $table) {
            $table->dropConstrainedForeignId('function_id');
            $table->dropConstrainedForeignId('grade_id');
            $table->dropConstrainedForeignId('category_id');
        });
    }
};