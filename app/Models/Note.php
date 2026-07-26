<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Note extends Model
{
    protected $fillable = [
        'user_id', 'title', 'content', 'category',
        'tags', 'is_pinned', 'is_favorite',
    ];

    protected function casts(): array
    {
        return [
            'tags' => 'array',
            'is_pinned' => 'boolean',
            'is_favorite' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
