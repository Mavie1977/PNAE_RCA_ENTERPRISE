<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();

            $table->foreignId('ministry_id')
                ->constrained('ministries')
                ->cascadeOnDelete();

            $table->string('name');
            $table->string('code', 50)->nullable();

            $table->text('description')->nullable();

            $table->boolean('active')->default(true);

            $table->timestamps();

            $table->unique([
                'ministry_id',
                'name',
            ]);

            $table->index([
                'ministry_id',
                'active',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};