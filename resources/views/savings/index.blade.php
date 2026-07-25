@extends('layouts.app')

@section('title', 'Savings Vault')
@section('page-title', 'Savings Vault')

@section('content')
    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <x-card>
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-primary rounded-button flex items-center justify-center shadow-hard flex-shrink-0">
                    <i class="bx bx-wallet text-white text-[28px]"></i>
                </div>
                <div>
                    <p class="text-3xl font-extrabold">Rp 12.5Jt</p>
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
                    <p class="text-3xl font-extrabold">5</p>
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
                    <p class="text-3xl font-extrabold">2</p>
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
                    <p class="text-3xl font-extrabold">65%</p>
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
        {{-- Goal 1 --}}
        <div x-data="{ depositOpen: false, withdrawOpen: false }">
            <x-card>
                <div class="flex items-start justify-between mb-5">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 bg-primary/10 rounded-button flex items-center justify-center">
                            <i class="bx bx-laptop text-primary text-[28px]"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-extrabold">MacBook Pro M4</h3>
                            <p class="text-xs text-txt-secondary font-medium">Deadline: Dec 2026</p>
                        </div>
                    </div>
                    <x-badge variant="warning">In Progress</x-badge>
                </div>
                <div class="mb-5">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-semibold text-txt-secondary">Progress</span>
                        <span class="text-sm font-extrabold">45%</span>
                    </div>
                    <div class="w-full h-5 bg-gray-200 rounded-full border-2 border-border-dark overflow-hidden">
                        <div class="h-full bg-primary rounded-full transition-all duration-500"
                            x-bind:style="'width: 45%'"></div>
                    </div>
                </div>
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <p class="text-xs text-txt-secondary font-medium">Current</p>
                        <p class="text-lg font-extrabold">Rp 18Jt</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-txt-secondary font-medium">Target</p>
                        <p class="text-lg font-extrabold">Rp 40Jt</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 pt-4 border-t-4 border-border-dark">
                    <x-button variant="success" size="sm" @click="depositOpen = !depositOpen">
                        <i class="bx bx-plus-circle"></i> Deposit
                    </x-button>
                    <x-button variant="warning" size="sm" @click="withdrawOpen = !withdrawOpen">
                        <i class="bx bx-minus-circle"></i> Withdraw
                    </x-button>
                </div>
            </x-card>

            {{-- Deposit Modal --}}
            <div x-show="depositOpen"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
                style="display: none;">
                <div x-show="depositOpen"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="scale-95 opacity-0"
                    x-transition:enter-end="scale-100 opacity-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="scale-100 opacity-100"
                    x-transition:leave-end="scale-95 opacity-0"
                    @click.outside="depositOpen = false"
                    class="bg-surface border-4 border-border-dark rounded-modal shadow-hard w-full max-w-sm animate-scale-in p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-extrabold">Deposit to MacBook Pro M4</h3>
                        <button @click="depositOpen = false" class="text-2xl text-txt-secondary hover:text-danger transition-colors">
                            <i class="bx bx-x"></i>
                        </button>
                    </div>
                    <form class="space-y-4">
                        <x-input name="deposit_amount" label="Amount" type="number" placeholder="e.g. 500000" />
                        <x-input name="deposit_note" label="Note (optional)" placeholder="From freelance project" />
                        <div class="flex items-center justify-end gap-3 pt-2">
                            <x-button variant="secondary" @click="depositOpen = false">Cancel</x-button>
                            <x-button type="submit"><i class="bx bx-check"></i> Confirm Deposit</x-button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Withdraw Modal --}}
            <div x-show="withdrawOpen"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
                style="display: none;">
                <div x-show="withdrawOpen"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="scale-95 opacity-0"
                    x-transition:enter-end="scale-100 opacity-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="scale-100 opacity-100"
                    x-transition:leave-end="scale-95 opacity-0"
                    @click.outside="withdrawOpen = false"
                    class="bg-surface border-4 border-border-dark rounded-modal shadow-hard w-full max-w-sm animate-scale-in p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-extrabold">Withdraw from MacBook Pro M4</h3>
                        <button @click="withdrawOpen = false" class="text-2xl text-txt-secondary hover:text-danger transition-colors">
                            <i class="bx bx-x"></i>
                        </button>
                    </div>
                    <form class="space-y-4">
                        <x-input name="withdraw_amount" label="Amount" type="number" placeholder="e.g. 200000" />
                        <x-input name="withdraw_note" label="Reason" placeholder="Why are you withdrawing?" />
                        <div class="flex items-center justify-end gap-3 pt-2">
                            <x-button variant="secondary" @click="withdrawOpen = false">Cancel</x-button>
                            <x-button variant="danger" type="submit"><i class="bx bx-check"></i> Confirm Withdraw</x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Goal 2 --}}
        <div x-data="{ depositOpen: false, withdrawOpen: false }">
            <x-card>
                <div class="flex items-start justify-between mb-5">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 bg-[#22C55E]/10 rounded-button flex items-center justify-center">
                            <i class="bx bx-plane text-[#22C55E] text-[28px]"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-extrabold">Trip to Japan</h3>
                            <p class="text-xs text-txt-secondary font-medium">Deadline: Mar 2027</p>
                        </div>
                    </div>
                    <x-badge variant="completed">Completed</x-badge>
                </div>
                <div class="mb-5">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-semibold text-txt-secondary">Progress</span>
                        <span class="text-sm font-extrabold">100%</span>
                    </div>
                    <div class="w-full h-5 bg-gray-200 rounded-full border-2 border-border-dark overflow-hidden">
                        <div class="h-full bg-[#22C55E] rounded-full transition-all duration-500"
                            x-bind:style="'width: 100%'"></div>
                    </div>
                </div>
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <p class="text-xs text-txt-secondary font-medium">Current</p>
                        <p class="text-lg font-extrabold">Rp 25Jt</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-txt-secondary font-medium">Target</p>
                        <p class="text-lg font-extrabold">Rp 25Jt</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 pt-4 border-t-4 border-border-dark">
                    <x-button variant="success" size="sm" disabled>
                        <i class="bx bx-plus-circle"></i> Deposit
                    </x-button>
                    <x-button variant="warning" size="sm" disabled>
                        <i class="bx bx-minus-circle"></i> Withdraw
                    </x-button>
                </div>
            </x-card>
        </div>

        {{-- Goal 3 --}}
        <div x-data="{ depositOpen: false, withdrawOpen: false }">
            <x-card>
                <div class="flex items-start justify-between mb-5">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 bg-[#F59E0B]/10 rounded-button flex items-center justify-center">
                            <i class="bx bx-home text-[#F59E0B] text-[28px]"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-extrabold">House Down Payment</h3>
                            <p class="text-xs text-txt-secondary font-medium">Deadline: Jun 2028</p>
                        </div>
                    </div>
                    <x-badge variant="danger">On Hold</x-badge>
                </div>
                <div class="mb-5">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-semibold text-txt-secondary">Progress</span>
                        <span class="text-sm font-extrabold">20%</span>
                    </div>
                    <div class="w-full h-5 bg-gray-200 rounded-full border-2 border-border-dark overflow-hidden">
                        <div class="h-full bg-[#F59E0B] rounded-full transition-all duration-500"
                            x-bind:style="'width: 20%'"></div>
                    </div>
                </div>
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <p class="text-xs text-txt-secondary font-medium">Current</p>
                        <p class="text-lg font-extrabold">Rp 50Jt</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-txt-secondary font-medium">Target</p>
                        <p class="text-lg font-extrabold">Rp 250Jt</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 pt-4 border-t-4 border-border-dark">
                    <x-button variant="success" size="sm" @click="depositOpen = !depositOpen">
                        <i class="bx bx-plus-circle"></i> Deposit
                    </x-button>
                    <x-button variant="warning" size="sm" @click="withdrawOpen = !withdrawOpen">
                        <i class="bx bx-minus-circle"></i> Withdraw
                    </x-button>
                </div>
            </x-card>
        </div>

        {{-- Goal 4 --}}
        <div x-data="{ depositOpen: false, withdrawOpen: false }">
            <x-card>
                <div class="flex items-start justify-between mb-5">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 bg-purple-acc/10 rounded-button flex items-center justify-center">
                            <i class="bx bx-car text-purple-acc text-[28px]"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-extrabold">New Car</h3>
                            <p class="text-xs text-txt-secondary font-medium">Deadline: Jan 2027</p>
                        </div>
                    </div>
                    <x-badge variant="warning">In Progress</x-badge>
                </div>
                <div class="mb-5">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-semibold text-txt-secondary">Progress</span>
                        <span class="text-sm font-extrabold">72%</span>
                    </div>
                    <div class="w-full h-5 bg-gray-200 rounded-full border-2 border-border-dark overflow-hidden">
                        <div class="h-full bg-purple-acc rounded-full transition-all duration-500"
                            x-bind:style="'width: 72%'"></div>
                    </div>
                </div>
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <p class="text-xs text-txt-secondary font-medium">Current</p>
                        <p class="text-lg font-extrabold">Rp 180Jt</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-txt-secondary font-medium">Target</p>
                        <p class="text-lg font-extrabold">Rp 250Jt</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 pt-4 border-t-4 border-border-dark">
                    <x-button variant="success" size="sm" @click="depositOpen = !depositOpen">
                        <i class="bx bx-plus-circle"></i> Deposit
                    </x-button>
                    <x-button variant="warning" size="sm" @click="withdrawOpen = !withdrawOpen">
                        <i class="bx bx-minus-circle"></i> Withdraw
                    </x-button>
                </div>
            </x-card>
        </div>
    </div>

    {{-- New Goal Modal --}}
    <x-modal id="goal-modal" title="New Saving Goal">
        <form class="space-y-5">
            <x-input name="goal_name" label="Goal Name" placeholder="e.g. MacBook Pro" />
            <x-input name="goal_icon" label="Icon (BoxIcons class)" placeholder="e.g. bx bx-laptop" />
            <div class="grid grid-cols-2 gap-4">
                <x-input name="target_amount" label="Target Amount" type="number" placeholder="e.g. 40000000" />
                <x-input name="deadline" label="Deadline" type="month" />
            </div>
            <div class="space-y-1.5">
                <label for="goal_status" class="block text-sm font-semibold text-txt-primary">Status</label>
                <select name="goal_status" id="goal_status"
                    class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-semibold text-txt-primary outline-none focus:border-primary transition-colors">
                    <option value="in_progress">In Progress</option>
                    <option value="completed">Completed</option>
                    <option value="on_hold">On Hold</option>
                </select>
            </div>
            <div class="flex items-center justify-end gap-3 pt-2">
                <x-button variant="secondary" @click="$dispatch('close-modal', { id: 'goal-modal' })">
                    Cancel
                </x-button>
                <x-button type="submit">
                    <i class="bx bx-save"></i> Save Goal
                </x-button>
            </div>
        </form>
    </x-modal>
@endsection
