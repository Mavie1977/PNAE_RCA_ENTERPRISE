<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agent_profiles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->unique()
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('ministry_id')
                ->nullable()
                ->constrained('ministries')
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Identification administrative
            |--------------------------------------------------------------------------
            */
            $table->string('matricule', 50)->unique();

            $table->string('service')->nullable();
            $table->string('job_title')->nullable();
            $table->string('grade')->nullable();
            $table->string('category', 50)->nullable();

            /*
            |--------------------------------------------------------------------------
            | Situation administrative
            |--------------------------------------------------------------------------
            */
            $table->string('administrative_status', 50)
                ->default('active');

            /*
            |--------------------------------------------------------------------------
            | Recrutement / nomination
            |--------------------------------------------------------------------------
            */
            $table->date('recruitment_date')->nullable();
            $table->date('appointment_date')->nullable();

            $table->string('hire_reference')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Informations complémentaires
            |--------------------------------------------------------------------------
            */
            $table->date('birth_date')->nullable();
            $table->string('place_of_birth')->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index('administrative_status');
            $table->index('grade');
            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agent_profiles');
    }
};