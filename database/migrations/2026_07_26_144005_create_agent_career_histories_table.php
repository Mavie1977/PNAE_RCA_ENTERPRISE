<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agent_career_histories', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('agent_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('performed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('from_ministry_id')
                ->nullable()
                ->constrained('ministries')
                ->nullOnDelete();

            $table->foreignId('to_ministry_id')
                ->nullable()
                ->constrained('ministries')
                ->nullOnDelete();

            $table->string('event_type', 50);

            $table->string('title');

            $table->text('reason')->nullable();

            $table->string('reference', 100)->nullable();

            $table->boolean('previous_active')->nullable();

            $table->boolean('new_active')->nullable();

            $table->json('metadata')->nullable();

            $table->timestamp('effective_at')->nullable();

            $table->timestamps();

            $table->index([
                'agent_id',
                'event_type',
            ]);

            $table->index('effective_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agent_career_histories');
    }
};