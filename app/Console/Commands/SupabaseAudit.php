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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Compares row counts & columns between the local MySQL database and the
 * Supabase mirror used by the Flutter mobile app.
 *
 *  php artisan supabase:audit
 */
class SupabaseAudit extends Command
{
    protected $signature = 'supabase:audit {--tables=* : Batasi ke tabel tertentu (contoh: --tables=users,clients)}';

    protected $description = 'Bandingkan jumlah baris & kolom antara MySQL dan Supabase';

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

    public function handle(SupabaseSyncService $supabase): int
    {
        if (! $supabase->isConfigured()) {
            $this->error('Supabase belum dikonfigurasi (SUPABASE_URL / SUPABASE_SERVICE_ROLE_KEY).');

            return self::FAILURE;
        }

        $only = $this->option('tables') ?: array_keys($this->tables);

        $mismatch = false;
        $rows = [];

        foreach ($only as $table) {
            if (! isset($this->tables[$table])) {
                $this->warn("Skip: tabel '$table' tidak dikenal.");

                continue;
            }

            $localCount = DB::table($table)->count();
            $remoteCount = $supabase->count($table);
            $columns = $this->diffColumns($table);

            if ($remoteCount === null) {
                $rows[] = [$table, $localCount, 'ERR', 'tidak bisa dibaca di Supabase'];
                $mismatch = true;

                continue;
            }

            $countOk = $localCount === $remoteCount;
            $colOk = $columns === [];
            if (! $countOk || ! $colOk) {
                $mismatch = true;
            }

            $rows[] = [
                $table,
                $localCount,
                $remoteCount,
                implode(', ', array_merge(
                    $countOk ? [] : ['jumlah baris beda'],
                    $columns === [] ? [] : ['kolom kurang: '.implode(', ', $columns)],
                )) ?: 'OK',
            ];
        }

        $this->table(['Tabel', 'MySQL', 'Supabase', 'Status'], $rows);

        if ($mismatch) {
            $this->warn('Ada perbedaan — jalankan: php artisan sync:pull (mobile→web) atau push ulang via supabase:push-all.');
        } else {
            $this->info('Semua tabel sinkron (jumlah baris & kolom).');
        }

        return $mismatch ? self::FAILURE : self::SUCCESS;
    }

    /**
     * Local columns that don't exist on the Supabase side (only those needed
     * by the mobile app are compared — password/remember_token excluded).
     *
     * @return array<int, string>
     */
    protected function diffColumns(string $table): array
    {
        $remote = $this->remoteColumns($table);
        if ($remote === null) {
            return ['?'];
        }

        $skip = ['password', 'remember_token'];

        return collect(Schema::getColumnListing($table))
            ->reject(fn ($col) => in_array($col, $skip, true))
            ->reject(fn ($col) => in_array($col, $remote, true))
            ->values()
            ->all();
    }

    /**
     * @return array<int, string>|null
     */
    protected function remoteColumns(string $table): ?array
    {
        $sample = app(SupabaseSyncService::class)->firstRow($table);

        return $sample === null ? null : array_keys($sample);
    }
}
