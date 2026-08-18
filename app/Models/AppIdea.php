<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AppIdea extends Model
{
    use SoftDeletes;

    protected $table = 'app_ideas';

    protected $fillable = [
        'user_id',
        'name',
        'tagline',
        'description',
        'features',
        'tech_stack',
        'platform',
        'status',
        'priority',
        'tags',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'features' => 'array',
            'tech_stack' => 'array',
            'tags' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
