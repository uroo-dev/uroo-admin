<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SavingsGoal extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'target_amount',
        'current_amount',
        'icon',
        'color',
        'deadline',
        'is_completed',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'target_amount' => 'decimal:2',
            'current_amount' => 'decimal:2',
            'deadline' => 'date',
            'is_completed' => 'boolean',
        ];
    }

    public function getProgressPercentageAttribute(): float
    {
        if ($this->target_amount <= 0) {
            return 0;
        }

        return min(100, round(($this->current_amount / $this->target_amount) * 100, 2));
    }

    public function getRemainingAmountAttribute(): float
    {
        return max(0, $this->target_amount - $this->current_amount);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(SavingsTransaction::class, 'goal_id');
    }

    public function deposits(): HasMany
    {
        return $this->transactions()->where('type', 'deposit');
    }

    public function withdraws(): HasMany
    {
        return $this->transactions()->where('type', 'withdraw');
    }
}
