<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ministries', function (Blueprint $table) {
            if (! Schema::hasColumn('ministries', 'code')) {
                $table->string('code', 30)
                    ->nullable()
                    ->unique();
            }

            if (! Schema::hasColumn('ministries', 'description')) {
                $table->text('description')->nullable();
            }

            if (! Schema::hasColumn('ministries', 'email')) {
                $table->string('email')->nullable();
            }

            if (! Schema::hasColumn('ministries', 'phone')) {
                $table->string('phone', 30)->nullable();
            }

            if (! Schema::hasColumn('ministries', 'active')) {
                $table->boolean('active')->default(true);
            }
        });

        Schema::table('procedures', function (Blueprint $table) {
            if (! Schema::hasColumn('procedures', 'slug')) {
                $table->string('slug')->nullable()->unique();
            }

            if (! Schema::hasColumn('procedures', 'description')) {
                $table->text('description')->nullable();
            }

            if (! Schema::hasColumn('procedures', 'fee')) {
                $table->decimal('fee', 12, 2)->default(0);
            }

            if (! Schema::hasColumn('procedures', 'processing_days')) {
                $table->unsignedInteger('processing_days')
                    ->nullable();
            }

            if (! Schema::hasColumn(
                'procedures',
                'payment_required'
            )) {
                $table->boolean('payment_required')
                    ->default(false);
            }

            if (! Schema::hasColumn(
                'procedures',
                'official_document_required'
            )) {
                $table->boolean('official_document_required')
                    ->default(true);
            }

            if (! Schema::hasColumn('procedures', 'active')) {
                $table->boolean('active')->default(true);
            }
        });
    }

    public function down(): void
    {
        /*
         * Migration volontairement non destructive.
         * Ne pas supprimer automatiquement les colonnes métier.
         */
    }
};