<?php

declare(strict_types=1);

namespace Modules\Subscription\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Subscription\Models\Subscription;
use Modules\Subscription\Services\SubscriptionService;

class SubscriptionList extends Component
{
    use WithPagination;

    public string $statusFilter = '';

    public string $categoryFilter = '';

    public SubscriptionService $service;

    protected $queryString = [
        'statusFilter' => ['except' => ''],
        'categoryFilter' => ['except' => ''],
    ];

    public function boot(SubscriptionService $service): void
    {
        $this->service = $service;
    }

    public function togglePayment(int $id): void
    {
        $subscription = Subscription::where('user_id', auth()->id())->findOrFail($id);
        $subscription->update([
            'payment_status' => $subscription->payment_status === 'paid' ? 'unpaid' : 'paid',
        ]);
    }

    public function toggleActive(int $id): void
    {
        $subscription = Subscription::where('user_id', auth()->id())->findOrFail($id);
        $subscription->update(['is_active' => ! $subscription->is_active]);
    }

    public function delete(int $id): void
    {
        Subscription::where('user_id', auth()->id())->findOrFail($id)->delete();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingCategoryFilter(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function subscriptions(): LengthAwarePaginator
    {
        return Subscription::where('user_id', auth()->id())
            ->when($this->statusFilter, fn ($query) => $query->where('payment_status', $this->statusFilter))
            ->when($this->categoryFilter, fn ($query) => $query->where('category', $this->categoryFilter))
            ->orderBy('due_date', 'asc')
            ->paginate(15);
    }

    #[Computed]
    public function stats(): array
    {
        return $this->service->getStats(auth()->id());
    }

    public function render(): View
    {
        return view('subscription::livewire.subscription-list', [
            'subscriptions' => $this->subscriptions,
            'stats' => $this->stats,
        ]);
    }
}
