<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('payments')) {
            Schema::create('payments', function (Blueprint $table) {
                $table->id();

                $table->foreignId('application_id')
                    ->constrained('applications')
                    ->cascadeOnDelete();

                $table->foreignId('user_id')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();

                $table->string('reference')->unique();
                $table->decimal('amount', 12, 2);
                $table->string('currency', 10)->default('XAF');
                $table->string('method', 30);
                $table->string('status', 30)->default('en_attente');
                $table->string('provider_reference')->nullable();
                $table->string('payer_phone', 30)->nullable();
                $table->string('payer_name')->nullable();
                $table->json('metadata')->nullable();
                $table->text('failure_reason')->nullable();
                $table->timestamp('paid_at')->nullable();
                $table->timestamp('cancelled_at')->nullable();
                $table->timestamps();
            });

            return;
        }

        Schema::table('payments', function (Blueprint $table) {
            if (! Schema::hasColumn('payments', 'user_id')) {
                $table->foreignId('user_id')
                    ->nullable()
                    ->after('application_id')
                    ->constrained('users')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('payments', 'reference')) {
                $table->string('reference')
                    ->nullable()
                    ->unique();
            }

            if (! Schema::hasColumn('payments', 'amount')) {
                $table->decimal('amount', 12, 2)->default(0);
            }

            if (! Schema::hasColumn('payments', 'currency')) {
                $table->string('currency', 10)->default('XAF');
            }

            if (! Schema::hasColumn('payments', 'method')) {
                $table->string('method', 30)->default('mobile_money');
            }

            if (! Schema::hasColumn('payments', 'status')) {
                $table->string('status', 30)->default('en_attente');
            }

            if (! Schema::hasColumn('payments', 'provider_reference')) {
                $table->string('provider_reference')->nullable();
            }

            if (! Schema::hasColumn('payments', 'payer_phone')) {
                $table->string('payer_phone', 30)->nullable();
            }

            if (! Schema::hasColumn('payments', 'payer_name')) {
                $table->string('payer_name')->nullable();
            }

            if (! Schema::hasColumn('payments', 'metadata')) {
                $table->json('metadata')->nullable();
            }

            if (! Schema::hasColumn('payments', 'failure_reason')) {
                $table->text('failure_reason')->nullable();
            }

            if (! Schema::hasColumn('payments', 'paid_at')) {
                $table->timestamp('paid_at')->nullable();
            }

            if (! Schema::hasColumn('payments', 'cancelled_at')) {
                $table->timestamp('cancelled_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        /*
         * Migration volontairement non destructive :
         * aucune colonne métier n’est supprimée automatiquement.
         */
    }
};