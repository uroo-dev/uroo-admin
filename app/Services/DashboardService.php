<?php

namespace App\Services;

use App\Models\AppIdea;
use App\Models\Bookmark;
use App\Models\Client;
use App\Models\Commit;
use App\Models\Invoice;
use App\Models\Note;
use App\Models\Project;
use App\Models\SavingsGoal;

class DashboardService
{
    public function getStats(int $userId): array
    {
        $activeProjects = Project::where('user_id', $userId)
            ->whereNotIn('status', ['completed', 'archived'])
            ->count();

        $pendingInvoices = Invoice::where('user_id', $userId)
            ->where('status', 'hutang')
            ->count();

        return [
            'active_projects' => $activeProjects,
            'pending_invoices' => $pendingInvoices,
            'total_clients' => Client::where('user_id', $userId)->count(),
            'total_savings' => (float) SavingsGoal::where('user_id', $userId)->sum('current_amount'),
            'total_revenue' => (float) Invoice::where('user_id', $userId)->sum('total'),
            'total_notes' => Note::where('user_id', $userId)->count(),
            'total_ideas' => AppIdea::where('user_id', $userId)->count(),
            'total_bookmarks' => Bookmark::where('user_id', $userId)->count(),
        ];
    }

    public function getRecentActivities(int $userId, int $limit = 5): array
    {
        $commits = Commit::with('repository:id,name')
            ->latest('committed_at')
            ->limit(10)
            ->get()
            ->map(fn (Commit $commit) => [
                'type' => 'commit',
                'message' => $commit->message,
                'repo' => $commit->repository?->name,
                'date' => $commit->committed_at,
            ]);

        $invoices = Invoice::with('client:id,name')
            ->where('user_id', $userId)
            ->latest()
            ->limit(10)
            ->get()
            ->map(fn (Invoice $invoice) => [
                'type' => 'invoice',
                'number' => $invoice->invoice_number,
                'client' => $invoice->client?->name,
                'total' => (float) $invoice->total,
                'paid' => $invoice->paid_amount >= $invoice->total,
                'date' => $invoice->created_at,
            ]);

        $clients = Client::where('user_id', $userId)
            ->latest()
            ->limit(10)
            ->get()
            ->map(fn (Client $client) => [
                'type' => 'client',
                'name' => $client->name,
                'company' => $client->company,
                'date' => $client->created_at,
            ]);

        return $commits->concat($invoices)->concat($clients)
            ->sortByDesc('date')
            ->take($limit)
            ->values()
            ->toArray();
    }

    public function getOverdueInvoices(int $userId): array
    {
        return Invoice::with('client:id,name')
            ->where('user_id', $userId)
            ->where('status', 'hutang')
            ->whereNotNull('due_date')
            ->where('due_date', '<', now())
            ->latest('due_date')
            ->limit(5)
            ->get()
            ->map(fn (Invoice $invoice) => [
                'id' => $invoice->id,
                'number' => $invoice->invoice_number,
                'client' => $invoice->client?->name,
                'total' => (float) $invoice->total,
                'paid' => (float) $invoice->paid_amount,
                'due_date' => $invoice->due_date,
                'url' => route('invoices.index'),
            ])
            ->toArray();
    }
}
