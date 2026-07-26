<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bookmark extends Model
{
    protected $fillable = [
        'user_id', 'title', 'url', 'description',
        'category', 'tags', 'logo_url', 'is_favorite',
    ];

    protected function casts(): array
    {
        return [
            'tags' => 'array',
            'is_favorite' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
