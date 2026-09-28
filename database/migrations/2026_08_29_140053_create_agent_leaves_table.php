<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agent_leaves', function (Blueprint $table) {
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

            $table->string('leave_type', 50);

            $table->date('start_date');
            $table->date('end_date');

            $table->unsignedInteger('days_count')->default(1);

            $table->string('status', 30)->default('pending');

            $table->string('reference', 150)->nullable();

            $table->text('reason')->nullable();

            $table->text('decision_reason')->nullable();

            $table->timestamp('decided_at')->nullable();

            $table->timestamps();

            $table->index([
                'agent_id',
                'status',
                'start_date',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agent_leaves');
    }
};