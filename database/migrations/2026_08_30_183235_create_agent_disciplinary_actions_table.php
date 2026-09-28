<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agent_disciplinary_actions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('agent_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('decided_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('sanction_type', 50);

            $table->string('status', 30)->default('pending');

            $table->date('effective_at')->nullable();

            $table->date('end_at')->nullable();

            $table->string('reference', 150)->nullable();

            $table->text('reason');

            $table->text('decision_reason')->nullable();

            $table->timestamp('decided_at')->nullable();

            $table->timestamps();

            $table->index([
                'agent_id',
                'status',
                'effective_at',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agent_disciplinary_actions');
    }
};