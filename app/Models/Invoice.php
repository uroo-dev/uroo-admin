<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Invoice extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'client_id', 'invoice_number', 'items',
        'subtotal', 'tax_percent', 'tax_amount',
        'discount_percent', 'discount_amount',
        'total', 'paid_amount', 'status', 'due_date', 'paid_at', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'items' => 'array',
            'subtotal' => 'decimal:2',
            'tax_percent' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'discount_percent' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'total' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'due_date' => 'date',
            'paid_at' => 'datetime',
        ];
    }

    public function remainingAmount(): float
    {
        return (float) $this->total - (float) $this->paid_amount;
    }

    public function isFullyPaid(): bool
    {
        return $this->paid_amount >= $this->total;
    }

    protected function casts(): array
    {
        return [
            'items' => 'array',
            'subtotal' => 'decimal:2',
            'tax_percent' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'discount_percent' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'total' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'due_date' => 'date',
            'paid_at' => 'datetime',
        ];
    }

    public function remainingAmount(): float
    {
        return (float) $this->total - (float) $this->paid_amount;
    }

    public function isFullyPaid(): bool
    {
        return $this->paid_amount >= $this->total;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public static function generateInvoiceNumber(): string
    {
        $last = static::withTrashed()
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $last ? (int) Str::afterLast($last->invoice_number, '-') + 1 : 1;

        return sprintf('INV-%s-%s-%04d', now()->year, now()->format('m'), $sequence);
    }
}
