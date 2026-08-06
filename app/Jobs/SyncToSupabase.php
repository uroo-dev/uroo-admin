<?php

namespace App\Jobs;

use App\Services\SupabaseSyncService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

/**
 * Pushes a single model change to the Supabase mirror (web → mobile).
 */
class SyncToSupabase implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public string $modelClass,
        public int $id,
        public string $action = 'save', // save | delete | restore
    ) {}

    public function handle(SupabaseSyncService $supabase): void
    {
        if (! $supabase->isConfigured()) {
            return;
        }

        $table = Str::snake(Str::pluralStudly(class_basename($this->modelClass)));
        $model = $this->modelClass::withTrashed()->find($this->id);

        if ($this->action === 'delete' && $model === null) {
            $supabase->delete($table, $this->id);

            return;
        }

        $payload = $model?->attributesToArray() ?? [];

        if ($this->action === 'delete') {
            // Soft-deletable models keep the row and flag deleted_at; the rest
            // are hard-deleted from Supabase.
            if ($model && in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses_recursive($model))) {
                $payload['deleted_at'] = now()->toIso8601String();
                $supabase->upsert($table, $payload, 'id');
            } else {
                $supabase->delete($table, $this->id);
            }

            return;
        }

        if ($this->action === 'restore') {
            $payload['deleted_at'] = null;
        }

        if ($payload !== []) {
            $supabase->upsert($table, $payload, 'id');
        }
    }
}
