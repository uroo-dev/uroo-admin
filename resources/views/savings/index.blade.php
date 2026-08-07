@extends('layouts.app')
@section('title', 'Savings Vault')
@section('page-title', 'Savings Vault')

@section('content')

<script>
function savingsApp() {
    return {
        showGoalModal: false,
        showTxnModal:  false,
        editingGoal:   null,
        txnGoal:       null,
        txnType:       'deposit',
        goalForm: { name: '', target_amount: '', icon: '', color: '#4F46E5', deadline: '', notes: '' },
        txnForm:  { amount: '', description: '' },

        openCreate() {
            this.editingGoal = null;
            this.goalForm = { name: '', target_amount: '', icon: '', color: '#4F46E5', deadline: '', notes: '' };
            this.showGoalModal = true;
        },

        openEdit(goal) {
            this.editingGoal = goal;
            this.goalForm = {
                name:          goal.name          || '',
                target_amount: goal.target_amount || '',
                icon:          goal.icon          || '',
                color:         goal.color         || '#4F46E5',
                deadline:      goal.deadline ? String(goal.deadline).slice(0, 10) : '',
                notes:         goal.notes         || '',
            };
            this.showGoalModal = true;
        },

        closeGoalModal() {
            this.showGoalModal = false;
            this.editingGoal = null;
        },

        openTxn(goal, type) {
            this.txnGoal  = goal;
            this.txnType  = type;
            this.txnForm  = { amount: '', description: '' };
            this.showTxnModal = true;
        },

        closeTxnModal() {
            this.showTxnModal = false;
            this.txnGoal = null;
        },

        rp(val) {
            return 'Rp ' + Number(val || 0).toLocaleString('id-ID');
        },

        confirmDelete(formId, name) {
            SwalDanger.fire({
                title: 'Delete Goal?',
                html: `"<strong>${name}</strong>" and all its transactions will be permanently deleted.`,
                confirmButtonText: 'Yes, delete!',
                cancelButtonText: 'Cancel',
            }).then(r => { if (r.isConfirmed) document.getElementById(formId).submit(); });
        },
    };
}
</script>

<div x-data="savingsApp()">

    {{-- Stats Bar --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-5 flex items-center gap-4 hover:-translate-y-1 hover:shadow-hard-hover transition-all">
            <div class="w-12 h-12 bg-primary border-2 border-border-dark rounded-button flex items-center justify-center flex-shrink-0 shadow-hard-sm">
                <i class="bx bx-wallet text-white text-xl"></i>
            </div>
            <div>
                <p class="text-base font-extrabold leading-tight">{{ 'Rp ' . number_format($stats['total_saved'] ?? 0, 0, ',', '.') }}</p>
                <p class="text-xs font-semibold text-txt-secondary mt-0.5">Total Saved</p>
            </div>
        </div>
        <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-5 flex items-center gap-4 hover:-translate-y-1 hover:shadow-hard-hover transition-all">
            <div class="w-12 h-12 bg-secondary border-2 border-border-dark rounded-button flex items-center justify-center flex-shrink-0 shadow-hard-sm">
                <i class="bx bx-flag text-white text-xl"></i>
            </div>
            <div>
                <p class="text-3xl font-extrabold leading-none">{{ $stats['total_goals'] ?? 0 }}</p>
                <p class="text-xs font-semibold text-txt-secondary mt-0.5">Total Goals</p>
            </div>
        </div>
        <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-5 flex items-center gap-4 hover:-translate-y-1 hover:shadow-hard-hover transition-all">
            <div class="w-12 h-12 bg-green-500 border-2 border-border-dark rounded-button flex items-center justify-center flex-shrink-0 shadow-hard-sm">
                <i class="bx bx-check-circle text-white text-xl"></i>
            </div>
            <div>
                <p class="text-3xl font-extrabold leading-none">{{ $stats['completed_goals'] ?? 0 }}</p>
                <p class="text-xs font-semibold text-txt-secondary mt-0.5">Completed</p>
            </div>
        </div>
        <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-5 flex items-center gap-4 hover:-translate-y-1 hover:shadow-hard-hover transition-all">
            <div class="w-12 h-12 bg-yellow-400 border-2 border-border-dark rounded-button flex items-center justify-center flex-shrink-0 shadow-hard-sm">
                <i class="bx bx-trending-up text-gray-900 text-xl"></i>
            </div>
            <div>
                <p class="text-3xl font-extrabold leading-none">{{ $stats['overall_progress'] ?? 0 }}%</p>
                <p class="text-xs font-semibold text-txt-secondary mt-0.5">Overall Progress</p>
            </div>
        </div>
    </div>

    {{-- Toolbar --}}
    <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-5 mb-6 flex items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <i class="bx bx-piggy-bank text-primary text-2xl"></i>
            <h2 class="text-base font-extrabold">Saving Goals</h2>
        </div>
        <button type="button" @click="openCreate()"
            class="px-6 py-3 bg-primary text-white font-bold text-sm rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 active:translate-y-1 transition-all flex items-center gap-2">
            <i class="bx bx-plus text-lg"></i> New Goal
        </button>
    </div>

    {{-- Flash --}}
    @if (session('success'))
        <div class="mb-5 px-5 py-4 bg-surface border-4 border-green-500 rounded-card shadow-hard flex items-center gap-3">
            <i class="bx bx-check-circle text-green-500 text-2xl"></i>
            <span class="font-semibold text-sm">{{ session('success') }}</span>
        </div>
    @endif

    {{-- Goal Cards --}}
    @if ($goals->isEmpty())
        <div class="flex flex-col items-center justify-center py-20 bg-surface border-4 border-border-dark rounded-card shadow-hard">
            <i class="bx bx-piggy-bank text-7xl text-txt-secondary"></i>
            <h3 class="text-xl font-extrabold mt-4">No saving goals yet</h3>
            <p class="text-txt-secondary mt-2 text-sm">Create your first goal to start tracking savings</p>
            <button type="button" @click="openCreate()"
                class="mt-5 px-6 py-3 bg-primary text-white font-bold text-sm rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 transition-all">
                <i class="bx bx-plus mr-1"></i> New Goal
            </button>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($goals as $goal)
                @php
                    $pct     = (float) $goal->progress_percentage;
                    $color   = $goal->color ?? '#4F46E5';
                    $isDone  = $goal->is_completed;
                    $remaining = max(0, $goal->target_amount - $goal->current_amount);

                    // Progress bar color
                    if ($isDone)        $barClass = 'bg-green-500';
                    elseif ($pct >= 75) $barClass = 'bg-yellow-400';
                    elseif ($pct >= 40) $barClass = 'bg-secondary';
                    else                $barClass  = 'bg-primary';
                @endphp

                <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-5 flex flex-col transition-all duration-200 hover:-translate-y-1.5 hover:shadow-hard-hover">

                    {{-- Header --}}
                    <div class="flex items-start justify-between gap-3 mb-4">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-12 h-12 rounded-button border-2 border-border-dark flex items-center justify-center flex-shrink-0"
                                 style="background-color: {{ $color }}22">
                                <i class="{{ $goal->icon ?? 'bx bx-piggy-bank' }} text-2xl" style="color: {{ $color }}"></i>
                            </div>
                            <div class="min-w-0">
                                <h3 class="font-extrabold text-sm leading-snug truncate">{{ $goal->name }}</h3>
                                @if ($goal->deadline)
                                    <p class="text-xs text-txt-secondary mt-0.5">
                                        <i class="bx bx-calendar text-xs"></i>
                                        {{ $goal->deadline->format('d M Y') }}
                                        @if (!$isDone && $goal->deadline->isPast())
                                            <span class="text-danger font-bold">(Overdue)</span>
                                        @endif
                                    </p>
                                @endif
                            </div>
                        </div>
                        @if ($isDone)
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-bold border-2 border-green-500 rounded-full bg-green-100 text-green-700 flex-shrink-0">
                                <i class="bx bx-check text-xs"></i> Done
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-1 text-xs font-bold border-2 border-yellow-400 rounded-full bg-yellow-50 text-yellow-700 flex-shrink-0">
                                In Progress
                            </span>
                        @endif
                    </div>

                    {{-- Progress bar --}}
                    <div class="mb-4">
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-xs font-semibold text-txt-secondary">Progress</span>
                            <span class="text-xs font-extrabold {{ $isDone ? 'text-green-600' : '' }}">{{ number_format($pct, 1) }}%</span>
                        </div>
                        <div class="w-full h-4 bg-gray-100 border-2 border-border-dark rounded-full overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-500 {{ $barClass }}"
                                 style="width: {{ $pct }}%"></div>
                        </div>
                    </div>

                    {{-- Amount breakdown --}}
                    <div class="grid grid-cols-2 gap-3 mb-4">
                        <div class="bg-green-50 border-2 border-green-300 rounded-button p-3">
                            <p class="text-xs font-semibold text-green-600 mb-0.5">Saved</p>
                            <p class="text-sm font-extrabold text-green-700">Rp {{ number_format($goal->current_amount, 0, ',', '.') }}</p>
                        </div>
                        <div class="bg-gray-50 border-2 border-border-dark rounded-button p-3">
                            <p class="text-xs font-semibold text-txt-secondary mb-0.5">{{ $isDone ? 'Target' : 'Remaining' }}</p>
                            <p class="text-sm font-extrabold text-txt-primary">
                                Rp {{ number_format($isDone ? $goal->target_amount : $remaining, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>

                    {{-- Target --}}
                    <div class="flex items-center justify-between text-xs text-txt-secondary mb-4">
                        <span>Target</span>
                        <span class="font-bold text-txt-primary">Rp {{ number_format($goal->target_amount, 0, ',', '.') }}</span>
                    </div>

                    {{-- Deposit / Withdraw buttons --}}
                    <div class="flex gap-2 pt-3 border-t-4 border-border-dark">
                        <button type="button" @click="openTxn({{ Js::from($goal) }}, 'deposit')"
                            class="flex-1 px-3 py-2.5 bg-green-500 text-white font-bold text-xs rounded-button border-4 border-border-dark shadow-hard-sm hover:-translate-y-0.5 active:translate-y-1 transition-all flex items-center justify-center gap-1.5">
                            <i class="bx bx-plus-circle text-sm"></i> Deposit
                        </button>
                        <button type="button" @click="openTxn({{ Js::from($goal) }}, 'withdraw')"
                            class="flex-1 px-3 py-2.5 bg-yellow-400 text-gray-900 font-bold text-xs rounded-button border-4 border-border-dark shadow-hard-sm hover:-translate-y-0.5 active:translate-y-1 transition-all flex items-center justify-center gap-1.5">
                            <i class="bx bx-minus-circle text-sm"></i> Withdraw
                        </button>
                        <button type="button" @click="openEdit({{ Js::from($goal) }})"
                            class="px-3 py-2.5 bg-surface text-txt-secondary font-bold text-xs rounded-button border-4 border-border-dark shadow-hard-sm hover:-translate-y-0.5 hover:text-primary transition-all">
                            <i class="bx bx-edit-alt text-base"></i>
                        </button>
                        <form id="delGoal-{{ $goal->id }}" action="{{ route('savings.destroy', $goal) }}" method="POST">
                            @csrf @method('DELETE')
                            <button type="button"
                                @click="confirmDelete('delGoal-{{ $goal->id }}', {{ Js::from($goal->name) }})"
                                class="px-3 py-2.5 bg-surface text-danger font-bold text-xs rounded-button border-4 border-border-dark shadow-hard-sm hover:-translate-y-0.5 hover:bg-danger hover:text-white transition-all">
                                <i class="bx bx-trash-alt text-base"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        @if ($goals->hasPages())
            <div class="mt-8">{{ $goals->links() }}</div>
        @endif
    @endif

    {{-- ============================================================
         MODAL: Create / Edit Goal
    ============================================================ --}}
    <div x-show="showGoalModal"
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        @keydown.escape.window="closeGoalModal()"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 overflow-y-auto"
        style="display:none;">
        <div @click.stop x-show="showGoalModal"
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="scale-95 opacity-0" x-transition:enter-end="scale-100 opacity-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="scale-100 opacity-100" x-transition:leave-end="scale-95 opacity-0"
            class="bg-surface border-4 border-border-dark rounded-modal shadow-hard w-full max-w-lg my-4"
            style="display:none;">

            <div class="flex items-center justify-between px-6 py-4 border-b-4 border-border-dark">
                <h3 class="text-lg font-extrabold" x-text="editingGoal ? 'Edit Goal' : 'New Goal'"></h3>
                <button @click="closeGoalModal()" class="text-2xl text-txt-secondary hover:text-danger transition-colors">
                    <i class="bx bx-x"></i>
                </button>
            </div>

            <form method="POST"
                  :action="editingGoal ? '/savings/' + editingGoal.id : '{{ route('savings.store') }}'"
                  class="p-6 space-y-4">
                @csrf
                <input type="hidden" name="_method" :value="editingGoal ? 'PATCH' : 'POST'">

                <div>
                    <label class="block text-sm font-bold mb-1.5">Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" x-model="goalForm.name" required maxlength="255"
                        placeholder="E.g. Laptop Baru"
                        class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none transition-colors">
                </div>

                <div>
                    <label class="block text-sm font-bold mb-1.5">Target Amount <span class="text-danger">*</span></label>
                    <input type="number" name="target_amount" x-model="goalForm.target_amount"
                        required min="0.01" step="any" placeholder="10000000"
                        class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none transition-colors">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold mb-1.5">Icon <span class="text-txt-secondary font-normal text-xs">(BoxIcon class)</span></label>
                        <input type="text" name="icon" x-model="goalForm.icon" maxlength="50"
                            placeholder="bx bxs-laptop"
                            class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none transition-colors">
                    </div>
                    <div>
                        <label class="block text-sm font-bold mb-1.5">Color</label>
                        <div class="flex items-center gap-2">
                            <input type="color" name="color" x-model="goalForm.color"
                                class="w-14 h-[50px] rounded-input border-4 border-border-dark cursor-pointer p-0.5 flex-shrink-0">
                            <input type="text" x-model="goalForm.color" maxlength="7"
                                class="flex-1 px-3 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium focus:border-primary outline-none transition-colors">
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold mb-1.5">Deadline <span class="text-txt-secondary font-normal text-xs">(optional)</span></label>
                    <input type="date" name="deadline" x-model="goalForm.deadline"
                        class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium focus:border-primary outline-none transition-colors">
                </div>

                <div>
                    <label class="block text-sm font-bold mb-1.5">Notes</label>
                    <textarea name="notes" x-model="goalForm.notes" rows="2" maxlength="5000"
                        placeholder="Optional notes..."
                        class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none resize-none transition-colors"></textarea>
                </div>

                <div class="flex justify-end gap-3 pt-2 border-t-4 border-border-dark">
                    <button type="button" @click="closeGoalModal()"
                        class="px-6 py-3 font-bold text-sm rounded-button border-4 border-border-dark bg-surface shadow-hard hover:-translate-y-0.5 transition-all">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-6 py-3 bg-primary text-white font-bold text-sm rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 active:translate-y-1 active:shadow-hard-pressed transition-all">
                        <i class="bx bx-save mr-1"></i>
                        <span x-text="editingGoal ? 'Update Goal' : 'Save Goal'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ============================================================
         MODAL: Deposit / Withdraw
    ============================================================ --}}
    <div x-show="showTxnModal"
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        @keydown.escape.window="closeTxnModal()"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
        style="display:none;">
        <div @click.stop x-show="showTxnModal"
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="scale-95 opacity-0" x-transition:enter-end="scale-100 opacity-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="scale-100 opacity-100" x-transition:leave-end="scale-95 opacity-0"
            class="bg-surface border-4 border-border-dark rounded-modal shadow-hard w-full max-w-md"
            style="display:none;">

            <div class="flex items-center justify-between px-6 py-4 border-b-4 border-border-dark"
                 :class="txnType === 'deposit' ? 'bg-green-50' : 'bg-orange-50'">
                <h3 class="text-lg font-extrabold flex items-center gap-2">
                    <i class="bx text-xl" :class="txnType === 'deposit' ? 'bx-plus-circle text-green-600' : 'bx-minus-circle text-orange-500'"></i>
                    <span x-text="txnType === 'deposit' ? 'Deposit' : 'Withdraw'"></span>
                </h3>
                <button @click="closeTxnModal()" class="text-2xl text-txt-secondary hover:text-danger transition-colors">
                    <i class="bx bx-x"></i>
                </button>
            </div>

            <form method="POST"
                  :action="txnGoal ? '/savings/' + txnGoal.id + '/' + txnType : '#'"
                  class="p-6 space-y-4">
                @csrf

                {{-- Goal info box --}}
                <div class="bg-bgmain border-2 border-border-dark rounded-button p-4">
                    <p class="text-xs text-txt-secondary mb-1">Goal</p>
                    <p class="font-extrabold text-sm" x-text="txnGoal ? txnGoal.name : ''"></p>
                    <div class="flex items-center justify-between mt-2">
                        <span class="text-xs text-txt-secondary">Current Balance</span>
                        <span class="text-sm font-extrabold text-green-600" x-text="txnGoal ? rp(txnGoal.current_amount) : ''"></span>
                    </div>
                    <div class="flex items-center justify-between mt-1">
                        <span class="text-xs text-txt-secondary">Remaining to Target</span>
                        <span class="text-sm font-bold text-txt-primary"
                              x-text="txnGoal ? rp(Math.max(0, txnGoal.target_amount - txnGoal.current_amount)) : ''"></span>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold mb-1.5">Amount <span class="text-danger">*</span></label>
                    <input type="number" name="amount" x-model.number="txnForm.amount"
                        required min="0.01" step="any" placeholder="500000"
                        class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none transition-colors">
                </div>

                <div>
                    <label class="block text-sm font-bold mb-1.5">Description <span class="text-txt-secondary font-normal text-xs">(optional)</span></label>
                    <input type="text" name="description" x-model="txnForm.description" maxlength="1000"
                        placeholder="E.g. Freelance payment"
                        class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none transition-colors">
                </div>

                <div class="flex justify-end gap-3 pt-2 border-t-4 border-border-dark">
                    <button type="button" @click="closeTxnModal()"
                        class="px-6 py-3 font-bold text-sm rounded-button border-4 border-border-dark bg-surface shadow-hard hover:-translate-y-0.5 transition-all">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-6 py-3 text-white font-bold text-sm rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 active:translate-y-1 active:shadow-hard-pressed transition-all"
                        :class="txnType === 'deposit' ? 'bg-green-500' : 'bg-orange-500'">
                        <i class="bx mr-1" :class="txnType === 'deposit' ? 'bx-download' : 'bx-upload'"></i>
                        <span x-text="txnType === 'deposit' ? 'Confirm Deposit' : 'Confirm Withdraw'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>{{-- end x-data --}}
@endsection
