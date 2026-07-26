@php
    $billingLabels = ['monthly' => '/month', 'yearly' => '/year', 'quarterly' => '/quarter'];
    $categoryIcons = [
        'saas' => 'bxl-github', 'hosting' => 'bx-cloud', 'domain' => 'bx-globe',
        'tools' => 'bxl-figma', 'entertainment' => 'bxl-netflix',
    ];
    $iconBgColors = [
        'saas' => 'bg-secondary/10', 'hosting' => 'bg-[#22C55E]/10',
        'domain' => 'bg-primary/10', 'tools' => 'bg-purple-acc/10',
        'entertainment' => 'bg-[#F59E0B]/10',
    ];
    $iconTextColors = [
        'saas' => 'text-secondary', 'hosting' => 'text-[#22C55E]',
        'domain' => 'text-primary', 'tools' => 'text-purple-acc',
        'entertainment' => 'text-[#F59E0B]',
    ];
@endphp

<div>
    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <x-card>
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-[#22C55E] rounded-button flex items-center justify-center shadow-hard flex-shrink-0">
                    <i class="bx bx-calendar text-white text-[28px]"></i>
                </div>
                <div>
                    <p class="text-3xl font-extrabold">Rp {{ number_format($stats['total_monthly'] ?? 0, 0, ',', '.') }}</p>
                    <p class="text-sm font-medium text-txt-secondary">Monthly Total</p>
                </div>
            </div>
        </x-card>
        <x-card>
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-primary rounded-button flex items-center justify-center shadow-hard flex-shrink-0">
                    <i class="bx bx-calendar-check text-white text-[28px]"></i>
                </div>
                <div>
                    <p class="text-3xl font-extrabold">Rp {{ number_format($stats['total_annual'] ?? 0, 0, ',', '.') }}</p>
                    <p class="text-sm font-medium text-txt-secondary">Annual Total</p>
                </div>
            </div>
        </x-card>
        <x-card>
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-secondary rounded-button flex items-center justify-center shadow-hard flex-shrink-0">
                    <i class="bx bx-check-shield text-white text-[28px]"></i>
                </div>
                <div>
                    <p class="text-3xl font-extrabold">{{ $stats['active_subs'] ?? 0 }}</p>
                    <p class="text-sm font-medium text-txt-secondary">Active</p>
                </div>
            </div>
        </x-card>
        <x-card>
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-[#F59E0B] rounded-button flex items-center justify-center shadow-hard flex-shrink-0">
                    <i class="bx bx-time text-white text-[28px]"></i>
                </div>
                <div>
                    <p class="text-3xl font-extrabold">{{ $stats['unpaid'] ?? 0 }}</p>
                    <p class="text-sm font-medium text-txt-secondary">Unpaid</p>
                </div>
            </div>
        </x-card>
    </div>

    {{-- Toolbar --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8">
        <div class="flex flex-wrap items-center gap-3">
            <select wire:model.live="categoryFilter"
                class="px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-semibold text-txt-primary outline-none focus:border-primary transition-colors">
                <option value="">All Categories</option>
                <option value="saas">SaaS</option>
                <option value="hosting">Hosting</option>
                <option value="domain">Domain</option>
                <option value="tools">Tools</option>
                <option value="entertainment">Entertainment</option>
            </select>
            <select wire:model.live="statusFilter"
                class="px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-semibold text-txt-primary outline-none focus:border-primary transition-colors">
                <option value="">All Status</option>
                <option value="paid">Paid</option>
                <option value="unpaid">Unpaid</option>
            </select>
        </div>
        <x-button @click="$wire.resetForm(); $dispatch('open-modal', { id: 'subscription-modal' })">
            <i class="bx bx-plus"></i> Add Subscription
        </x-button>
    </div>

    {{-- Subscriptions List --}}
    <div class="space-y-4">
        @forelse ($subscriptions as $sub)
            <x-card>
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-4 flex-1 min-w-0">
                        <div class="w-12 h-12 {{ $iconBgColors[$sub->category] ?? 'bg-gray-100' }} rounded-button flex items-center justify-center flex-shrink-0">
                            <i class="bx {{ $categoryIcons[$sub->category] ?? 'bx-receipt' }} {{ $iconTextColors[$sub->category] ?? 'text-txt-secondary' }} text-[24px]"></i>
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-base font-extrabold truncate">{{ $sub->name }}</h3>
                            <p class="text-xs text-txt-secondary font-medium">{{ $sub->provider }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-6 flex-wrap">
                        <div class="text-right">
                            <p class="text-sm font-extrabold">Rp {{ number_format($sub->billing_cycle === 'yearly' ? ($sub->annual_cost ?? $sub->monthly_cost * 12) : $sub->monthly_cost, 0, ',', '.') }}</p>
                            <p class="text-[11px] text-txt-secondary font-semibold">{{ $billingLabels[$sub->billing_cycle] ?? '/month' }}</p>
                        </div>
                        <x-badge variant="info">{{ $sub->category }}</x-badge>
                        <x-badge variant="{{ $sub->payment_status === 'paid' ? 'success' : 'danger' }}">{{ ucfirst($sub->payment_status) }}</x-badge>
                        @if ($sub->due_date)
                            <div class="text-center">
                                <p class="text-xs font-semibold text-txt-secondary">Due: {{ $sub->due_date->format('d M Y') }}</p>
                            </div>
                        @endif
                        <div class="flex items-center gap-1">
                            <x-button variant="ghost" size="sm" wire:click="edit({{ $sub->id }})">
                                <i class="bx bx-edit text-base"></i>
                            </x-button>
                            <x-button variant="ghost" size="sm" wire:click="togglePayment({{ $sub->id }})">
                                <i class="bx {{ $sub->payment_status === 'paid' ? 'bx-x' : 'bx-check' }} text-base {{ $sub->payment_status === 'paid' ? 'text-danger' : 'text-[#22C55E]' }}"></i>
                            </x-button>
                            <x-button variant="ghost" size="sm" wire:click="confirmDelete({{ $sub->id }})">
                                <i class="bx bx-trash text-base text-danger"></i>
                            </x-button>
                        </div>
                    </div>
                </div>
            </x-card>
        @empty
            <div class="flex flex-col items-center justify-center py-16 bg-surface border-4 border-border-dark rounded-card shadow-hard">
                <i class="bx bx-receipt text-6xl text-txt-secondary"></i>
                <h3 class="text-xl font-extrabold mt-4">No subscriptions yet</h3>
                <p class="text-txt-secondary mt-2">Add your first subscription</p>
                <button onclick="Livewire.dispatch('open-modal', { id: 'subscription-modal' })"
                    class="mt-4 px-6 py-3 bg-primary text-white font-bold text-sm rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 transition-all duration-200 ease-out">
                    + Add Subscription
                </button>
            </div>
        @endforelse
    </div>

    @if ($subscriptions->hasPages())
        <div class="mt-6">
            {{ $subscriptions->links() }}
        </div>
    @endif

    {{-- Create/Edit Modal --}}
    <x-modal id="subscription-modal" title="{{ $editId ? 'Edit Subscription' : 'Add Subscription' }}">
        <form wire:submit="save" class="space-y-5">
            <div class="grid grid-cols-2 gap-4">
                <x-input name="name" label="Name" wire:model="name" placeholder="e.g. GitHub Team" />
                <x-input name="provider" label="Provider" wire:model="provider" placeholder="e.g. github.com" />
            </div>
            <div class="grid grid-cols-2 gap-4">
                <x-input name="cost" label="Cost" type="number" wire:model="cost" placeholder="e.g. 120000" />
                <div class="space-y-1.5">
                    <label class="block text-sm font-semibold text-txt-primary">Billing Cycle</label>
                    <select wire:model="billingCycle"
                        class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-semibold text-txt-primary outline-none focus:border-primary transition-colors">
                        <option value="monthly">Monthly</option>
                        <option value="yearly">Yearly</option>
                        <option value="quarterly">Quarterly</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="block text-sm font-semibold text-txt-primary">Category</label>
                    <select wire:model="category"
                        class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-semibold text-txt-primary outline-none focus:border-primary transition-colors">
                        <option value="saas">SaaS</option>
                        <option value="hosting">Hosting</option>
                        <option value="domain">Domain</option>
                        <option value="tools">Tools</option>
                        <option value="entertainment">Entertainment</option>
                    </select>
                </div>
                <div class="space-y-1.5">
                    <label class="block text-sm font-semibold text-txt-primary">Payment Status</label>
                    <select wire:model="paymentStatus"
                        class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-semibold text-txt-primary outline-none focus:border-primary transition-colors">
                        <option value="paid">Paid</option>
                        <option value="unpaid">Unpaid</option>
                    </select>
                </div>
            </div>
            <x-input name="dueDate" label="Due Date" type="date" wire:model="dueDate" />
            <div>
                <label class="block text-sm font-semibold text-txt-primary">Notes</label>
                <textarea wire:model="notes" rows="2" class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium focus:border-primary outline-none"></textarea>
            </div>
            @error('name') <p class="text-sm text-danger font-semibold">{{ $message }}</p> @enderror
            @error('cost') <p class="text-sm text-danger font-semibold">{{ $message }}</p> @enderror
            <div class="flex items-center justify-end gap-3 pt-2">
                <x-button variant="secondary" @click="$dispatch('close-modal', { id: 'subscription-modal' })">
                    Cancel
                </x-button>
                <x-button type="submit">
                    <i class="bx bx-save"></i> {{ $editId ? 'Update' : 'Save' }}
                </x-button>
            </div>
        </form>
    </x-modal>

    @push('scripts')
        <script>
            document.addEventListener('livewire:init', () => {
                Livewire.on('subscription-saved', () => {
                    Livewire.dispatch('close-modal', { id: 'subscription-modal' });
                });
            });
        </script>
    @endpush
</div>
