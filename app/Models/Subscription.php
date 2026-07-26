<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'provider',
        'category',
        'monthly_cost',
        'annual_cost',
        'currency',
        'billing_cycle',
        'due_date',
        'payment_status',
        'reminder_days',
        'is_active',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'monthly_cost' => 'decimal:2',
            'annual_cost' => 'decimal:2',
            'due_date' => 'date',
            'is_active' => 'boolean',
            'reminder_days' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getFormattedCostAttribute(): string
    {
        return match ($this->billing_cycle) {
            'monthly' => number_format($this->monthly_cost, 0, ',', '.'),
            'yearly' => number_format($this->annual_cost ?? $this->monthly_cost * 12, 0, ',', '.'),
            default => number_format($this->monthly_cost, 0, ',', '.'),
        };
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->payment_status === 'unpaid' && $this->due_date->isPast();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCycle($query, string $cycle)
    {
        return $query->where('billing_cycle', $cycle);
    }

    public function scopeUnpaid($query)
    {
        return $query->where('payment_status', 'unpaid');
    }
}
