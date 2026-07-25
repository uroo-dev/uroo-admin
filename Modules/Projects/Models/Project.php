<?php

namespace Modules\Projects\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Client\Models\Client;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'client_id',
        'name',
        'description',
        'category',
        'status',
        'platform',
        'tech_stack',
        'storage_usage',
        'start_date',
        'deadline',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'tech_stack' => 'array',
            'storage_usage' => 'integer',
            'start_date' => 'date:Y-m-d',
            'deadline' => 'date:Y-m-d',
            'completed_at' => 'date:Y-m-d',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
