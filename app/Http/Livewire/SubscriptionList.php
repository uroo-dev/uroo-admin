<?php

namespace App\Http\Livewire;

use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Subscription;
use App\Services\SubscriptionService;

class SubscriptionList extends Component
{
    use WithPagination;

    public string $statusFilter = '';
    public string $categoryFilter = '';

    public SubscriptionService $service;

    public ?int $editId = null;
    public ?int $deleteId = null;
    public string $name = '';
    public string $provider = '';
    public string $category = 'saas';
    public string $billingCycle = 'monthly';
    public float $cost = 0;
    public ?string $dueDate = null;
    public string $paymentStatus = 'unpaid';
    public string $notes = '';

    protected $queryString = [
        'statusFilter' => ['except' => ''],
        'categoryFilter' => ['except' => ''],
    ];

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'provider' => 'required|string|max:255',
            'category' => 'required|string|max:50',
            'billingCycle' => 'required|in:monthly,yearly,quarterly',
            'cost' => 'required|numeric|min:0',
            'dueDate' => 'nullable|date',
            'paymentStatus' => 'required|in:paid,unpaid',
            'notes' => 'nullable|string|max:1000',
        ];
    }

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

    #[On('delete-subscription')]
    public function delete(): void
    {
        if ($this->deleteId) {
            Subscription::where('user_id', auth()->id())->findOrFail($this->deleteId)->delete();
            $this->deleteId = null;
            $this->dispatch('swal:success', title: 'Langganan dihapus');
        }
    }

    public function confirmDelete(int $id): void
    {
        $this->deleteId = $id;
        $this->dispatch('swal:confirm', ['event' => 'delete-subscription', 'title' => 'Hapus langganan?', 'confirmText' => 'Ya, hapus!']);
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'user_id' => auth()->id(),
            'name' => $this->name,
            'provider' => $this->provider,
            'category' => $this->category,
            'billing_cycle' => $this->billingCycle,
            'due_date' => $this->dueDate,
            'payment_status' => $this->paymentStatus,
            'notes' => $this->notes ?: null,
        ];

        if ($this->billingCycle === 'monthly') {
            $data['monthly_cost'] = $this->cost;
        } elseif ($this->billingCycle === 'yearly') {
            $data['annual_cost'] = $this->cost;
            $data['monthly_cost'] = round($this->cost / 12, 2);
        } else {
            $data['monthly_cost'] = $this->cost;
        }

        if ($this->editId) {
            Subscription::findOrFail($this->editId)->update($data);
            $this->dispatch('swal:success', title: 'Langganan diperbarui');
        } else {
            Subscription::create($data);
            $this->dispatch('swal:success', title: 'Langganan ditambahkan');
        }

        $this->resetForm();
        $this->dispatch('close-modal', id: 'subscription-modal');
    }

    public function edit(int $id): void
    {
        $sub = Subscription::findOrFail($id);
        $this->editId = $sub->id;
        $this->name = $sub->name;
        $this->provider = $sub->provider;
        $this->category = $sub->category ?? 'saas';
        $this->billingCycle = $sub->billing_cycle;
        $this->cost = (float) ($sub->billing_cycle === 'yearly' ? ($sub->annual_cost ?? $sub->monthly_cost * 12) : $sub->monthly_cost);
        $this->dueDate = $sub->due_date?->format('Y-m-d');
        $this->paymentStatus = $sub->payment_status;
        $this->notes = $sub->notes ?? '';
        $this->dispatch('open-modal', id: 'subscription-modal');
    }

    public function resetForm(): void
    {
        $this->reset(['editId', 'name', 'provider', 'category', 'billingCycle', 'cost', 'dueDate', 'paymentStatus', 'notes']);
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

        return view('livewire.subscription-list', [
            'subscriptions' => $this->subscriptions,
            'stats' => $this->stats,
            'categories' => $categories,
        ]);
    }
}
