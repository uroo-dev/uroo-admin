@php
    function formatRupiah($amount) {
        if ($amount >= 1000000) {
            return 'Rp ' . number_format($amount / 1000000, 1, ',', '.') . 'Jt';
        }
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }
@endphp

<div>
    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <x-card>
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-primary rounded-button flex items-center justify-center shadow-hard flex-shrink-0">
                    <i class="bx bx-wallet text-white text-[28px]"></i>
                </div>
                <div>
                    <p class="text-3xl font-extrabold">{{ formatRupiah($stats['total_saved'] ?? 0) }}</p>
                    <p class="text-sm font-medium text-txt-secondary">Total Saved</p>
                </div>
            </div>
        </x-card>
        <x-card>
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-secondary rounded-button flex items-center justify-center shadow-hard flex-shrink-0">
                    <i class="bx bx-flag text-white text-[28px]"></i>
                </div>
                <div>
                    <p class="text-3xl font-extrabold">{{ $stats['total_goals'] ?? 0 }}</p>
                    <p class="text-sm font-medium text-txt-secondary">Goals</p>
                </div>
            </div>
        </x-card>
        <x-card>
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-[#22C55E] rounded-button flex items-center justify-center shadow-hard flex-shrink-0">
                    <i class="bx bx-check-circle text-white text-[28px]"></i>
                </div>
                <div>
                    <p class="text-3xl font-extrabold">{{ $stats['completed_goals'] ?? 0 }}</p>
                    <p class="text-sm font-medium text-txt-secondary">Completed</p>
                </div>
            </div>
        </x-card>
        <x-card>
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-[#F59E0B] rounded-button flex items-center justify-center shadow-hard flex-shrink-0">
                    <i class="bx bx-trending-up text-white text-[28px]"></i>
                </div>
                <div>
                    <p class="text-3xl font-extrabold">{{ $stats['overall_progress'] ?? 0 }}%</p>
                    <p class="text-sm font-medium text-txt-secondary">Progress</p>
                </div>
            </div>
        </x-card>
    </div>

    {{-- Header --}}
    <div class="flex items-center justify-between mb-8">
        <div class="flex items-center gap-3">
            <i class="bx bx-target-lock text-primary text-2xl"></i>
            <h2 class="text-xl font-extrabold">Saving Goals</h2>
        </div>
        <x-button @click="$dispatch('open-modal', { id: 'goal-modal' })">
            <i class="bx bx-plus"></i> New Goal
        </x-button>
    </div>

    {{-- Goals Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        @forelse ($goals as $goal)
            <div>
                <x-card>
                    <div class="flex items-start justify-between mb-5">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 rounded-button flex items-center justify-center"
                                style="background-color: {{ $goal->color ?? '#4F46E5' }}15">
                                <i class="{{ $goal->icon ?? 'bx bx-target-lock' }}" style="color: {{ $goal->color ?? '#4F46E5' }}; font-size: 28px;"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-extrabold">{{ $goal->name }}</h3>
                                @if ($goal->deadline)
                                    <p class="text-xs text-txt-secondary font-medium">Deadline: {{ $goal->deadline->format('M Y') }}</p>
                                @endif
                            </div>
                        </div>
                        @if ($goal->is_completed)
                            <x-badge variant="completed">Completed</x-badge>
                        @elseif ($goal->notes === 'on_hold')
                            <x-badge variant="danger">On Hold</x-badge>
                        @else
                            <x-badge variant="warning">In Progress</x-badge>
                        @endif
                    </div>
                    <div class="mb-5">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-semibold text-txt-secondary">Progress</span>
                            <span class="text-sm font-extrabold">{{ $goal->progress_percentage }}%</span>
                        </div>
                        <div class="w-full h-5 bg-gray-200 rounded-full border-2 border-border-dark overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-500"
                                style="width: {{ $goal->progress_percentage }}%; background-color: {{ $goal->color ?? '#4F46E5' }}"></div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between mb-5">
                        <div>
                            <p class="text-xs text-txt-secondary font-medium">Current</p>
                            <p class="text-lg font-extrabold">{{ formatRupiah($goal->current_amount) }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-txt-secondary font-medium">Target</p>
                            <p class="text-lg font-extrabold">{{ formatRupiah($goal->target_amount) }}</p>
                        </div>
                    </div>
                    @if (!$goal->is_completed)
                        <div class="flex items-center gap-3 pt-4 border-t-4 border-border-dark">
                            <x-button variant="success" size="sm"
                                wire:click="openDeposit({{ $goal->id }})">
                                <i class="bx bx-plus-circle"></i> Deposit
                            </x-button>
                            <x-button variant="warning" size="sm"
                                wire:click="openWithdraw({{ $goal->id }})">
                                <i class="bx bx-minus-circle"></i> Withdraw
                            </x-button>
                        </div>
                    @else
                        <div class="flex items-center gap-3 pt-4 border-t-4 border-border-dark">
                            <x-button variant="success" size="sm" disabled>
                                <i class="bx bx-plus-circle"></i> Deposit
                            </x-button>
                            <x-button variant="warning" size="sm" disabled>
                                <i class="bx bx-minus-circle"></i> Withdraw
                            </x-button>
                        </div>
                    @endif
                </x-card>
            </div>
        @empty
            <div class="col-span-full flex flex-col items-center justify-center py-16 bg-surface border-4 border-border-dark rounded-card shadow-hard">
                <i class="bx bx-wallet text-6xl text-txt-secondary"></i>
                <h3 class="text-xl font-extrabold mt-4">No goals yet</h3>
                <p class="text-txt-secondary mt-2">Create your first saving goal</p>
                <button onclick="Livewire.dispatch('open-modal', { id: 'goal-modal' })"
                    class="mt-4 px-6 py-3 bg-primary text-white font-bold text-sm rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 transition-all duration-200 ease-out">
                    + New Goal
                </button>
            </div>
        @endforelse
    </div>

    @if ($goals->hasPages())
        <div class="mt-6">
            {{ $goals->links() }}
        </div>
    @endif

    {{-- Deposit Modal --}}
    @if ($selectedGoalId)
        @php $selectedGoal = $goals->firstWhere('id', $selectedGoalId); @endphp
        @if ($selectedGoal)
            <x-modal id="deposit-modal" title="Deposit to {{ $selectedGoal->name }}">
                <form wire:submit="deposit" class="space-y-4">
                    <x-input name="depositAmount" label="Amount" type="number" wire:model="depositAmount" placeholder="e.g. 500000" />
                    <x-input name="transactionDescription" label="Note (optional)" wire:model="transactionDescription" placeholder="From freelance project" />
                    @error('depositAmount') <p class="text-sm text-danger font-semibold">{{ $message }}</p> @enderror
                    <div class="flex items-center justify-end gap-3 pt-2">
                        <x-button variant="secondary" @click="$dispatch('close-modal', { id: 'deposit-modal' })">Cancel</x-button>
                        <x-button type="submit"><i class="bx bx-check"></i> Confirm Deposit</x-button>
                    </div>
                </form>
            </x-modal>
        @endif
    @endif

    {{-- Withdraw Modal --}}
    @if ($selectedGoalId)
        @php $selectedGoal = $goals->firstWhere('id', $selectedGoalId); @endphp
        @if ($selectedGoal)
            <x-modal id="withdraw-modal" title="Withdraw from {{ $selectedGoal->name }}">
                <form wire:submit="withdraw" class="space-y-4">
                    <x-input name="withdrawAmount" label="Amount" type="number" wire:model="withdrawAmount" placeholder="e.g. 200000" />
                    <x-input name="transactionDescription" label="Reason" wire:model="transactionDescription" placeholder="Why are you withdrawing?" />
                    @error('withdrawAmount') <p class="text-sm text-danger font-semibold">{{ $message }}</p> @enderror
                    <div class="flex items-center justify-end gap-3 pt-2">
                        <x-button variant="secondary" @click="$dispatch('close-modal', { id: 'withdraw-modal' })">Cancel</x-button>
                        <x-button variant="danger" type="submit"><i class="bx bx-check"></i> Confirm Withdraw</x-button>
                    </div>
                </form>
            </x-modal>
        @endif
    @endif

    {{-- New Goal Modal --}}
    <x-modal id="goal-modal" title="New Saving Goal">
        @livewire('goal-form')
    </x-modal>
</div>
