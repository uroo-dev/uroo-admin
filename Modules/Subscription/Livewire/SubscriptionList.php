<?php

namespace Modules\Subscription\Livewire;

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
        $sub = Subscription::where('user_id', auth()->id())->findOrFail($id);
        $sub->update(['payment_status' => $sub->payment_status === 'paid' ? 'unpaid' : 'paid']);
    }

    public function toggleActive(int $id): void
    {
        $sub = Subscription::where('user_id', auth()->id())->findOrFail($id);
        $sub->update(['is_active' => !$sub->is_active]);
    }

    public function delete(int $id): void
    {
        Subscription::where('user_id', auth()->id())->findOrFail($id)->delete();
        $this->dispatch('swal:success', title: 'Langganan dihapus');
    }

    public function updatingStatusFilter(): void { $this->resetPage(); }
    public function updatingCategoryFilter(): void { $this->resetPage(); }

    #[Computed]
    public function subscriptions()
    {
        return Subscription::where('user_id', auth()->id())
            ->when($this->statusFilter, fn ($q) => $q->where('payment_status', $this->statusFilter))
            ->when($this->categoryFilter, fn ($q) => $q->where('category', $this->categoryFilter))
            ->orderBy('due_date', 'asc')
            ->paginate(15);
    }

    #[Computed]
    public function stats(): array
    {
        return $this->service->getStats(auth()->id());
    }

    public function render()
    {
        $categories = Subscription::where('user_id', auth()->id())
            ->whereNotNull('category')
            ->distinct('category')
            ->pluck('category');

        return view('subscriptions.index', [
            'subscriptions' => $this->subscriptions,
            'stats' => $this->stats,
            'categories' => $categories,
        ]);
    }
}