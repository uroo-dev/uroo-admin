<?php

namespace App\Observers;

use App\Jobs\SyncToSupabase;
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

/**
 * Dispatches a Supabase push job for every write to a feature model so the
 * Flutter mobile app (which reads Supabase) stays in sync with the web app.
 */
class SupabaseObserver
{
    public const MODELS = [
        Note::class,
        BrainDump::class,
        AppIdea::class,
        SavingsGoal::class,
        SavingsTransaction::class,
        Invoice::class,
        InvoicePayment::class,
        Client::class,
        Project::class,
        Subscription::class,
        Credential::class,
        Repository::class,
        Commit::class,
    ];

    public function saved($model): void
    {
        SyncToSupabase::dispatch($model::class, $model->getKey(), 'save');
    }

    public function deleted($model): void
    {
        SyncToSupabase::dispatch($model::class, $model->getKey(), 'delete');
    }

    public function restored($model): void
    {
        SyncToSupabase::dispatch($model::class, $model->getKey(), 'restore');
    }
}
