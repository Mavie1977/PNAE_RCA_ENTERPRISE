<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('login_audits', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('email')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();

            $table->string('event', 40);
            $table->boolean('successful')->default(false);
            $table->text('failure_reason')->nullable();

            $table->timestamp('occurred_at')->useCurrent();

            $table->timestamps();

            $table->index([
                'email',
                'occurred_at',
            ]);

            $table->index([
                'ip_address',
                'occurred_at',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('login_audits');
    }
};