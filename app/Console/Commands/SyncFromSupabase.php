<?php

namespace App\Console\Commands;

use App\Models\AppIdea;
use App\Models\BrainDump;
use App\Models\Client;
use App\Models\Commit;
use App\Models\Credential;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Note;
use App\Models\Project;
use App\Models\Repository;
use App\Models\SavingsGoal;
use App\Models\SavingsTransaction;
use App\Models\Subscription;
use App\Models\User;
use App\Services\SupabaseSyncService;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Pulls Supabase changes (made by the Flutter mobile app) back into MySQL.
 *
 *  php artisan sync:pull
 */
class SyncFromSupabase extends Command
{
    protected $signature = 'sync:pull {--tables=* : Restrict to specific tables}';

    protected $description = 'Pull Supabase changes into the local MySQL database';

    /** Table name => Eloquent model (parents first). */
    protected array $tables = [
        'users' => User::class,
        'clients' => Client::class,
        'projects' => Project::class,
        'invoices' => Invoice::class,
        'invoice_payments' => InvoicePayment::class,
        'app_ideas' => AppIdea::class,
        'brain_dumps' => BrainDump::class,
        'notes' => Note::class,
        'savings_goals' => SavingsGoal::class,
        'savings_transactions' => SavingsTransaction::class,
        'repositories' => Repository::class,
        'commits' => Commit::class,
        'subscriptions' => Subscription::class,
        'credentials' => Credential::class,
    ];

    /** Columns to never overwrite locally when pulling. */
    protected array $ignoreColumns = [
        'users' => ['password', 'remember_token'],
    ];

    public function handle(SupabaseSyncService $supabase): int
    {
        if (! $supabase->isConfigured()) {
            $this->error('Supabase belum dikonfigurasi (SUPABASE_URL / SUPABASE_SERVICE_ROLE_KEY).');

            return self::FAILURE;
        }

        $only = $this->option('tables') ?: array_keys($this->tables);

        foreach ($only as $table) {
            if (! isset($this->tables[$table])) {
                $this->warn("Skip: tabel '$table' tidak dikenal.");

                continue;
            }
            $count = $this->syncTable($supabase, $table);
            $this->info("{$table}: {$count} baris diproses.");
        }

        return self::SUCCESS;
    }

    protected function syncTable(SupabaseSyncService $supabase, string $table): int
    {
        $watermark = DB::table('supabase_sync_watermarks')
            ->where('table_name', $table)
            ->value('last_synced_at');

        $rows = $supabase->pull($table, $watermark);
        if ($rows === []) {
            return 0;
        }

        $modelClass = $this->tables[$table];
        $ignore = $this->ignoreColumns[$table] ?? [];

        $latest = null;
        foreach ($rows as $row) {
            $id = (int) data_get($row, 'id');
            $deletedAt = data_get($row, 'deleted_at');
            $attrs = Arr::except($row, array_merge(['id'], $ignore));

            $instance = $modelClass::withTrashed()->find($id);

            if ($instance && $deletedAt && ! $instance->trashed()) {
                $instance->delete();
            } elseif ($instance && ! $deletedAt && $instance->trashed()) {
                $instance->restore();
            }

            if (! $deletedAt) {
                if ($modelClass === User::class) {
                    $attrs['password'] = $attrs['password'] ?? bcrypt(Str::random(32));
                }
                $modelClass::updateOrCreate(['id' => $id], $attrs);
            }

            $updated = data_get($row, 'updated_at');
            if ($updated && (! $latest || $updated > $latest)) {
                $latest = $updated;
            }
        }

        if ($latest) {
            DB::table('supabase_sync_watermarks')->updateOrInsert(
                ['table_name' => $table],
                ['last_synced_at' => $latest, 'updated_at' => now()],
            );
        }

        return count($rows);
    }
}
