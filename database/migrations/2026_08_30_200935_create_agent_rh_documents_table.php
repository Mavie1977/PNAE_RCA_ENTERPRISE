<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agent_rh_documents', function (Blueprint $table) {
            $table->id();

            $table->foreignId('agent_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('uploaded_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('document_type', 60);

            $table->string('title', 180);

            $table->string('reference', 150)
                ->nullable();

            $table->date('document_date')
                ->nullable();

            $table->string('file_path');

            $table->string('original_name');

            $table->string('mime_type', 120)
                ->nullable();

            $table->unsignedBigInteger('file_size')
                ->nullable();

            $table->text('notes')
                ->nullable();

            $table->boolean('active')
                ->default(true);

            $table->timestamps();

            $table->index([
                'agent_id',
                'document_type',
                'active',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agent_rh_documents');
    }
};