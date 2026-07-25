<?php

declare(strict_types=1);

namespace Modules\Ideas\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppIdea extends Model
{
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
