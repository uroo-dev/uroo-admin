<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Commit extends Model
{
    protected $fillable = [
        'repository_id', 'sha', 'message', 'author_name', 'author_email',
        'branch', 'modified_files', 'added_files', 'deleted_files',
        'additions', 'deletions', 'committed_at',
    ];

    protected function casts(): array
    {
        return [
            'modified_files' => 'array',
            'added_files' => 'array',
            'deleted_files' => 'array',
            'committed_at' => 'datetime',
        ];
    }

    public function repository(): BelongsTo
    {
        return $this->belongsTo(Repository::class);
    }
}
