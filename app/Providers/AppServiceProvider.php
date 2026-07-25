<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $modules = [
            'GitHub', 'Credential', 'Client', 'Invoice', 'Notes',
            'Bookmark', 'QualityControl', 'Ideas', 'BrainDump',
            'Savings', 'Subscription', 'Projects',
        ];

        foreach ($modules as $module) {
            $path = base_path("Modules/{$module}/Migrations");
            if (is_dir($path)) {
                $this->loadMigrationsFrom($path);
            }
        }

        Livewire::component('repository-list', \Modules\GitHub\Livewire\RepositoryList::class);
        Livewire::component('commit-timeline', \Modules\GitHub\Livewire\CommitTimeline::class);
        Livewire::component('credential-list', \Modules\Credential\Livewire\CredentialList::class);
        Livewire::component('client-list', \Modules\Client\Livewire\ClientList::class);
        Livewire::component('client-form', \Modules\Client\Livewire\ClientForm::class);
        Livewire::component('invoice-list', \Modules\Invoice\Livewire\InvoiceList::class);
        Livewire::component('invoice-form', \Modules\Invoice\Livewire\InvoiceForm::class);
        Livewire::component('project-list', \Modules\Projects\Livewire\ProjectList::class);
        Livewire::component('note-list', \Modules\Notes\Livewire\NoteList::class);
        Livewire::component('note-editor', \Modules\Notes\Livewire\NoteEditor::class);
        Livewire::component('bookmark-list', \Modules\Bookmark\Livewire\BookmarkList::class);
        Livewire::component('checklist-list', \Modules\QualityControl\Livewire\ChecklistList::class);
        Livewire::component('checklist-progress', \Modules\QualityControl\Livewire\ChecklistProgress::class);
        Livewire::component('idea-list', \Modules\Ideas\Livewire\IdeaList::class);
        Livewire::component('idea-form', \Modules\Ideas\Livewire\IdeaForm::class);
        Livewire::component('dump-list', \Modules\BrainDump\Livewire\DumpList::class);
        Livewire::component('goal-list', \Modules\Savings\Livewire\GoalList::class);
        Livewire::component('goal-form', \Modules\Savings\Livewire\GoalForm::class);
        Livewire::component('subscription-list', \Modules\Subscription\Livewire\SubscriptionList::class);
    }
}