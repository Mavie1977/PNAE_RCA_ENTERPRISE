<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agent_skills', function (Blueprint $table) {
            $table->id();

            $table->foreignId('agent_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('training_id')
                ->nullable()
                ->constrained('agent_trainings')
                ->nullOnDelete();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('name', 150);

            $table->string('category', 100)
                ->nullable();

            $table->string('level', 30)
                ->default('intermediate');

            $table->date('acquired_at')
                ->nullable();

            $table->text('notes')
                ->nullable();

            $table->timestamps();

            $table->index([
                'agent_id',
                'category',
                'level',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agent_skills');
    }
};