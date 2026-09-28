<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('official_documents', function (Blueprint $table) {
            $table->id();

            $table->foreignId('application_id')
                ->unique()
                ->constrained('applications')
                ->cascadeOnDelete();

            $table->foreignId('generated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('official_number')->unique();
            $table->uuid('verification_token')->unique();

            $table->string('title');
            $table->string('file_path');
            $table->string('mime_type')->default('application/pdf');

            $table->string('hash_sha256', 64);
            $table->string('signature_code', 128)->unique();

            $table->string('status', 30)->default('actif');

            $table->timestamp('issued_at');
            $table->timestamp('revoked_at')->nullable();
            $table->text('revocation_reason')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('official_documents');
    }
};