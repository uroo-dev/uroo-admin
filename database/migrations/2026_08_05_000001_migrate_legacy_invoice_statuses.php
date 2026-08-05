<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('invoices')
            ->where('status', 'paid')
            ->whereNotNull('paid_at')
            ->update(['status' => 'lunas', 'paid_amount' => DB::raw('total')]);

        DB::table('invoices')
            ->whereIn('status', ['pending', 'overdue', 'draft', 'sent', 'partial'])
            ->update(['status' => 'hutang']);

        Schema::table('invoices', function (Blueprint $table) {
            $table->string('status')->default('hutang')->change();
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('status')->default('pending')->change();
        });
    }
};
