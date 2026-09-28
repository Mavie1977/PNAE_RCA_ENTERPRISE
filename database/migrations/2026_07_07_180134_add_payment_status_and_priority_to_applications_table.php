<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            if (! Schema::hasColumn('applications', 'payment_status')) {
                $table->string('payment_status')->default('en_attente')->after('status');
            }

            if (! Schema::hasColumn('applications', 'priority')) {
                $table->string('priority')->default('normale')->after('payment_status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            if (Schema::hasColumn('applications', 'priority')) {
                $table->dropColumn('priority');
            }

            if (Schema::hasColumn('applications', 'payment_status')) {
                $table->dropColumn('payment_status');
            }
        });
    }
};