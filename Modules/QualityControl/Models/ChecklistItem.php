<?php

declare(strict_types=1);

namespace Modules\QualityControl\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChecklistItem extends Model
{
    protected $fillable = [
        'checklist_id',
        'label',
        'is_checked',
        'notes',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_checked' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function checklist(): BelongsTo
    {
        return $this->belongsTo(QualityChecklist::class, 'checklist_id');
    }
}
