<?php

namespace Modules\GitHub\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Repository extends Model
{
    protected $fillable = [
        'user_id', 'name', 'full_name', 'description', 'url',
        'language', 'stars', 'forks', 'open_issues',
        'default_branch', 'is_private', 'is_archived', 'last_pushed_at',
    ];

    protected function casts(): array
    {
        return [
            'is_private' => 'boolean',
            'is_archived' => 'boolean',
            'last_pushed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function commits(): HasMany
    {
        return $this->hasMany(Commit::class);
    }

    public function latestCommits()
    {
        return $this->commits()->orderBy('committed_at', 'desc')->limit(10);
    }
}