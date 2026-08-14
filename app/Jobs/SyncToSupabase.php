<?php

namespace App\Jobs;

use App\Services\SupabaseSyncService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
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

        try {
            $this->sync($supabase);
        } catch (\Throwable $e) {
            // Best effort — never let a Supabase hiccup break the web app.
            Log::warning('Supabase sync failed', [
                'model' => $this->modelClass,
                'id' => $this->id,
                'action' => $this->action,
                'error' => $e->getMessage(),
            ]);
        }
    }

    protected function sync(SupabaseSyncService $supabase): void
    {
        $table = Str::snake(Str::pluralStudly(class_basename($this->modelClass)));

        // Only soft-deletable models support withTrashed(); others are
        // hard-deleted from Supabase immediately.
        $softDeletes = in_array(
            SoftDeletes::class,
            class_uses_recursive($this->modelClass),
            true,
        );
        $model = $softDeletes
            ? $this->modelClass::withTrashed()->find($this->id)
            : $this->modelClass::find($this->id);

        if ($this->action === 'delete' && $model === null) {
            $supabase->delete($table, $this->id);

            return;
        }

        $payload = $model?->attributesToArray() ?? [];

        if ($this->action === 'delete') {
            // Soft-deletable models keep the row and flag deleted_at; the rest
            // are hard-deleted from Supabase.
            if ($model && $softDeletes) {
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
