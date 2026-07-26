<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('provider')->nullable();
            $table->string('category')->nullable();
            $table->decimal('monthly_cost', 10, 2);
            $table->decimal('annual_cost', 10, 2)->nullable();
            $table->string('currency', 10)->default('IDR');
            $table->string('billing_cycle')->default('monthly');
            $table->date('due_date');
            $table->string('payment_status')->default('unpaid');
            $table->integer('reminder_days')->default(3);
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
