<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hr_grades', function (Blueprint $table) {
            $table->id();

            $table->foreignId('category_id')
                ->nullable()
                ->constrained('hr_categories')
                ->nullOnDelete();

            $table->string('code', 30)->unique();
            $table->string('name');
            $table->text('description')->nullable();

            $table->unsignedInteger('level')->default(0);
            $table->boolean('active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();

            $table->index([
                'category_id',
                'active',
                'sort_order',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_grades');
    }
};