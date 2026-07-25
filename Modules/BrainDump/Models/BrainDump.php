<?php

declare(strict_types=1);

namespace Modules\BrainDump\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BrainDump extends Model
{
    protected $fillable = [
        'user_id',
        'content',
        'is_pinned',
        'is_archived',
    ];

    protected function casts(): array
    {
        return [
            'is_pinned' => 'boolean',
            'is_archived' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_archived', false);
    }

    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }
}
