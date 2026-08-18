<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add soft deletes to the tables synced with the mobile app so deletions
     * on one side can propagate to the other (tombstones via deleted_at).
     */
    public function up(): void
    {
        foreach ([
            'notes',
            'brain_dumps',
            'app_ideas',
            'savings_goals',
            'savings_transactions',
            'invoice_payments',
        ] as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->softDeletes();
            });
        }
    }

    public function down(): void
    {
        foreach ([
            'notes',
            'brain_dumps',
            'app_ideas',
            'savings_goals',
            'savings_transactions',
            'invoice_payments',
        ] as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropColumn('deleted_at');
            });
        }
    }
};