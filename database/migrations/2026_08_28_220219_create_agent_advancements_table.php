<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agent_advancements', function (Blueprint $table) {
            $table->id();

            $table->foreignId('agent_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('performed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('old_category_id')
                ->nullable()
                ->constrained('hr_categories')
                ->nullOnDelete();

            $table->foreignId('new_category_id')
                ->nullable()
                ->constrained('hr_categories')
                ->nullOnDelete();

            $table->foreignId('old_grade_id')
                ->nullable()
                ->constrained('hr_grades')
                ->nullOnDelete();

            $table->foreignId('new_grade_id')
                ->nullable()
                ->constrained('hr_grades')
                ->nullOnDelete();

            $table->foreignId('old_function_id')
                ->nullable()
                ->constrained('hr_functions')
                ->nullOnDelete();

            $table->foreignId('new_function_id')
                ->nullable()
                ->constrained('hr_functions')
                ->nullOnDelete();

            $table->string('advancement_type', 50)
                ->default('advancement');

            $table->string('reference', 150)->nullable();

            $table->text('reason')->nullable();

            $table->date('effective_at');

            $table->timestamps();

            $table->index([
                'agent_id',
                'effective_at',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agent_advancements');
    }
};