<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agent_trainings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('agent_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('title', 180);

            $table->string('organization', 180)
                ->nullable();

            $table->string('training_type', 60)
                ->default('training');

            $table->date('start_date')
                ->nullable();

            $table->date('end_date')
                ->nullable();

            $table->string('status', 30)
                ->default('planned');

            $table->string('certificate_reference', 150)
                ->nullable();

            $table->string('certificate_file_path')
                ->nullable();

            $table->string('certificate_original_name')
                ->nullable();

            $table->string('certificate_mime_type', 120)
                ->nullable();

            $table->unsignedBigInteger('certificate_file_size')
                ->nullable();

            $table->text('description')
                ->nullable();

            $table->text('result')
                ->nullable();

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
        Schema::dropIfExists('agent_trainings');
    }
};