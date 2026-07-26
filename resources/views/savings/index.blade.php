@extends('layouts.app')
@section('title', 'Savings Vault')
@section('page-title', 'Savings Vault')
@section('content')

@php
    function formatRupiah($n) { return 'Rp ' . number_format((float)$n, 0, ',', '.'); }
@endphp

@if(session('success'))
<script>
document.addEventListener('DOMContentLoaded',()=>Swal.fire({icon:'success',title:'Berhasil!',text:'{{ session('success') }}',timer:3000,showConfirmButton:false,toast:true,position:'top-end',background:'#FFFFFF',customClass:{popup:'border-4 border-border-dark rounded-card shadow-hard'}}));
</script>
@endif

{{-- Stats --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 hover:-translate-y-1.5 hover:shadow-hard-hover transition-all duration-200 ease-out">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-primary rounded-button flex items-center justify-center shadow-hard flex-shrink-0"><i class="bx bx-wallet text-white text-[28px]"></i></div>
            <div><p class="text-2xl font-extrabold">{{ formatRupiah($stats['totalSaved'] ?? 0) }}</p><p class="text-sm font-medium text-txt-secondary">Total Saved</p></div>
        </div>
    </div>
    <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 hover:-translate-y-1.5 hover:shadow-hard-hover transition-all duration-200 ease-out">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-secondary rounded-button flex items-center justify-center shadow-hard flex-shrink-0"><i class="bx bx-flag text-white text-[28px]"></i></div>
            <div><p class="text-3xl font-extrabold">{{ $stats['totalGoals'] ?? 0 }}</p><p class="text-sm font-medium text-txt-secondary">Total Goals</p></div>
        </div>
    </div>
    <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 hover:-translate-y-1.5 hover:shadow-hard-hover transition-all duration-200 ease-out">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-[#22C55E] rounded-button flex items-center justify-center shadow-hard flex-shrink-0"><i class="bx bx-check-circle text-white text-[28px]"></i></div>
            <div><p class="text-3xl font-extrabold">{{ $stats['completedGoals'] ?? 0 }}</p><p class="text-sm font-medium text-txt-secondary">Completed</p></div>
        </div>
    </div>
    <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 hover:-translate-y-1.5 hover:shadow-hard-hover transition-all duration-200 ease-out">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-yellow-acc rounded-button flex items-center justify-center shadow-hard flex-shrink-0"><i class="bx bx-trending-up text-[#111827] text-[28px]"></i></div>
            <div><p class="text-3xl font-extrabold">{{ $stats['progressPercentage'] ?? 0 }}%</p><p class="text-sm font-medium text-txt-secondary">Progress</p></div>
        </div>
    </div>
</div>

{{-- Toolbar --}}
<div class="flex items-center justify-between mb-8">
    <div class="flex items-center gap-3">
        <i class="bx bx-piggy-bank text-primary text-2xl"></i>
        <h2 class="text-xl font-extrabold">Saving Goals</h2>
    </div>
    <button type="button" onclick="openGoalModal()"
        class="px-6 py-3 bg-primary text-white font-bold text-sm rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 active:translate-y-1 transition-all duration-200 ease-out flex items-center gap-2">
        <i class="bx bx-plus text-lg"></i> New Goal
    </button>
</div>

{{-- Cards --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
@forelse ($goals as $goal)
    <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 hover:-translate-y-1.5 hover:shadow-hard-hover transition-all duration-200 ease-out">
        <div class="flex items-start justify-between mb-5">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-button flex items-center justify-center flex-shrink-0"
                    style="background-color:{{ $goal->color ?? '#4F46E5' }}20">
                    <i class="{{ $goal->icon ?? 'bx bx-piggy-bank' }}" style="color:{{ $goal->color ?? '#4F46E5' }};font-size:28px"></i>
                </div>
                <div>
                    <h3 class="text-lg font-extrabold">{{ $goal->name }}</h3>
                    @if($goal->deadline)
                        <p class="text-xs text-txt-secondary">Deadline: {{ \Carbon\Carbon::parse($goal->deadline)->format('M Y') }}</p>
                    @endif
                </div>
            </div>
            @if($goal->is_completed)
                <span class="text-xs font-bold px-3 py-1 border-2 border-border-dark rounded-full bg-[#22C55E] text-white flex-shrink-0">Completed</span>
            @else
                <span class="text-xs font-bold px-3 py-1 border-2 border-border-dark rounded-full bg-yellow-acc text-[#111827] flex-shrink-0">In Progress</span>
            @endif
        </div>

        <div class="mb-5">
            <div class="flex justify-between mb-2">
                <span class="text-sm font-semibold text-txt-secondary">Progress</span>
                <span class="text-sm font-extrabold">{{ $goal->progress_percentage }}%</span>
            </div>
            <div class="w-full h-5 bg-gray-200 rounded-full border-2 border-border-dark overflow-hidden">
                <div class="h-full rounded-full transition-all duration-500" style="width:{{ $goal->progress_percentage }}%;background-color:{{ $goal->color ?? '#4F46E5' }}"></div>
            </div>
        </div>

        <div class="flex justify-between mb-5">
            <div><p class="text-xs text-txt-secondary">Current</p><p class="text-lg font-extrabold">{{ formatRupiah($goal->current_amount) }}</p></div>
            <div class="text-right"><p class="text-xs text-txt-secondary">Target</p><p class="text-lg font-extrabold">{{ formatRupiah($goal->target_amount) }}</p></div>
        </div>

        <div class="flex gap-3 pt-4 border-t-4 border-border-dark">
            <button type="button" onclick="openTxnModal({{ $goal->id }}, {{ json_encode($goal->name) }}, 'deposit')"
                class="flex-1 px-4 py-2.5 bg-[#22C55E] text-white font-bold text-sm rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 active:translate-y-1 transition-all duration-200 ease-out flex items-center justify-center gap-1">
                <i class="bx bx-plus-circle"></i> Deposit
            </button>
            <button type="button" onclick="openTxnModal({{ $goal->id }}, {{ json_encode($goal->name) }}, 'withdraw')"
                class="flex-1 px-4 py-2.5 bg-yellow-acc text-[#111827] font-bold text-sm rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 active:translate-y-1 transition-all duration-200 ease-out flex items-center justify-center gap-1">
                <i class="bx bx-minus-circle"></i> Withdraw
            </button>
        </div>

        <div class="flex gap-2 pt-3 border-t-2 border-gray-100 mt-3">
            <button type="button"
                onclick="openGoalModal({{ $goal->id }},{{ json_encode($goal->name) }},{{ (float)$goal->target_amount }},{{ json_encode($goal->icon ?? '') }},{{ json_encode($goal->color ?? '#4F46E5') }},{{ json_encode($goal->deadline?->format('Y-m-d') ?? '') }},{{ json_encode($goal->notes ?? '') }})"
                class="p-2 text-txt-secondary hover:text-primary transition-colors" title="Edit">
                <i class="bx bx-edit text-lg"></i>
            </button>
            <form id="del-goal-{{ $goal->id }}" action="{{ route('savings.destroy', $goal) }}" method="POST" class="inline">
                @csrf @method('DELETE')
                <button type="button" onclick="deleteGoal('del-goal-{{ $goal->id }}', {{ json_encode($goal->name) }})"
                    class="p-2 text-txt-secondary hover:text-danger transition-colors" title="Delete">
                    <i class="bx bx-trash text-lg"></i>
                </button>
            </form>
        </div>
    </div>
@empty
    <div class="col-span-full flex flex-col items-center justify-center py-16 bg-surface border-4 border-border-dark rounded-card shadow-hard">
        <i class="bx bx-piggy-bank text-6xl text-txt-secondary"></i>
        <h3 class="text-xl font-extrabold mt-4">No goals yet</h3>
        <p class="text-txt-secondary mt-2">Create your first saving goal</p>
        <button type="button" onclick="openGoalModal()"
            class="mt-4 px-6 py-3 bg-primary text-white font-bold text-sm rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 transition-all duration-200 ease-out flex items-center gap-2">
            <i class="bx bx-plus"></i> New Goal
        </button>
    </div>
@endforelse
</div>

@if($goals->hasPages())
<div class="mt-8">{{ $goals->links() }}</div>
@endif

{{-- Goal Modal --}}
<div id="goal-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" onclick="if(event.target===this)closeGoalModal()">
    <div class="bg-surface border-4 border-border-dark rounded-modal shadow-hard w-full max-w-lg animate-scale-in" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between px-6 py-4 border-b-4 border-border-dark">
            <h3 id="goal-modal-title" class="text-lg font-extrabold">New Goal</h3>
            <button onclick="closeGoalModal()" class="text-2xl text-txt-secondary hover:text-danger transition-colors"><i class="bx bx-x"></i></button>
        </div>
        <form id="goal-form" method="POST" action="{{ route('savings.store') }}" class="p-6 space-y-4">
            @csrf
            <input type="hidden" name="_method" id="goal-method" value="POST">
            <div>
                <label class="block text-sm font-semibold mb-1.5">Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="goal-name" required placeholder="e.g. Laptop Baru"
                    class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none transition-colors">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1.5">Target Amount <span class="text-red-500">*</span></label>
                <input type="number" name="target_amount" id="goal-target" required min="1" step="0.01" placeholder="10000000"
                    class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none transition-colors">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold mb-1.5">Icon (BoxIcon class)</label>
                    <input type="text" name="icon" id="goal-icon" placeholder="bx bxs-laptop"
                        class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none transition-colors">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1.5">Color</label>
                    <input type="color" name="color" id="goal-color" value="#4F46E5"
                        class="w-full h-[50px] rounded-input border-4 border-border-dark cursor-pointer">
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1.5">Deadline</label>
                <input type="date" name="deadline" id="goal-deadline"
                    class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium focus:border-primary outline-none transition-colors">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1.5">Notes</label>
                <textarea name="notes" id="goal-notes" rows="2" placeholder="Optional..."
                    class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none resize-none transition-colors"></textarea>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="closeGoalModal()"
                    class="px-6 py-3 font-bold text-sm rounded-button border-4 border-border-dark bg-surface shadow-hard hover:-translate-y-0.5 transition-all duration-200 ease-out">Cancel</button>
                <button type="submit"
                    class="px-6 py-3 bg-primary text-white font-bold text-sm rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 transition-all duration-200 ease-out">Save Goal</button>
            </div>
        </form>
    </div>
</div>

{{-- Transaction Modal --}}
<div id="txn-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" onclick="if(event.target===this)closeTxnModal()">
    <div class="bg-surface border-4 border-border-dark rounded-modal shadow-hard w-full max-w-md animate-scale-in" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between px-6 py-4 border-b-4 border-border-dark">
            <h3 id="txn-title" class="text-lg font-extrabold">Deposit</h3>
            <button onclick="closeTxnModal()" class="text-2xl text-txt-secondary hover:text-danger transition-colors"><i class="bx bx-x"></i></button>
        </div>
        <form id="txn-form" method="POST" action="" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-semibold mb-1.5">Amount <span class="text-red-500">*</span></label>
                <input type="number" name="amount" id="txn-amount" required min="0.01" step="0.01" placeholder="500000"
                    class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none transition-colors">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1.5">Description (optional)</label>
                <input type="text" name="description" id="txn-desc" placeholder="e.g. Freelance payment"
                    class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none transition-colors">
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="closeTxnModal()"
                    class="px-6 py-3 font-bold text-sm rounded-button border-4 border-border-dark bg-surface shadow-hard hover:-translate-y-0.5 transition-all duration-200 ease-out">Cancel</button>
                <button id="txn-btn" type="submit"
                    class="px-6 py-3 bg-[#22C55E] text-white font-bold text-sm rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 transition-all duration-200 ease-out">Confirm</button>
            </div>
        </form>
    </div>
</div>

<script>
const savingsUrl = '{{ url('savings') }}';

function openGoalModal(id, name, target, icon, color, deadline, notes) {
    const isEdit = !!id;
    document.getElementById('goal-modal-title').textContent = isEdit ? 'Edit Goal' : 'New Goal';
    document.getElementById('goal-method').value = isEdit ? 'PUT' : 'POST';
    document.getElementById('goal-form').action  = isEdit ? savingsUrl+'/'+id : '{{ route('savings.store') }}';
    document.getElementById('goal-name').value    = name     || '';
    document.getElementById('goal-target').value  = target   || '';
    document.getElementById('goal-icon').value    = icon     || '';
    document.getElementById('goal-color').value   = color    || '#4F46E5';
    document.getElementById('goal-deadline').value = deadline || '';
    document.getElementById('goal-notes').value   = notes    || '';
    document.getElementById('goal-modal').classList.remove('hidden');
}
function closeGoalModal() { document.getElementById('goal-modal').classList.add('hidden'); }

function openTxnModal(id, name, type) {
    const isDeposit = type === 'deposit';
    document.getElementById('txn-title').textContent = (isDeposit ? 'Deposit ke ' : 'Tarik dari ') + name;
    document.getElementById('txn-form').action = savingsUrl+'/'+id+'/'+type;
    document.getElementById('txn-amount').value = '';
    document.getElementById('txn-desc').value   = '';
    const btn = document.getElementById('txn-btn');
    btn.textContent = isDeposit ? 'Confirm Deposit' : 'Confirm Withdraw';
    btn.style.backgroundColor = isDeposit ? '#22C55E' : '#EF4444';
    document.getElementById('txn-modal').classList.remove('hidden');
    setTimeout(() => document.getElementById('txn-amount').focus(), 80);
}
function closeTxnModal() { document.getElementById('txn-modal').classList.add('hidden'); }

function deleteGoal(formId, name) {
    Swal.fire({
        title: 'Hapus Goal?',
        text: '"' + name + '" dan semua transaksinya akan dihapus permanen.',
        icon: 'warning', showCancelButton: true,
        confirmButtonColor: '#EF4444', cancelButtonColor: '#6B7280',
        confirmButtonText: 'Ya, hapus!', cancelButtonText: 'Batal',
        background: '#FFFFFF', customClass: { popup: 'border-4 border-border-dark rounded-modal shadow-hard' }
    }).then(r => { if (r.isConfirmed) document.getElementById(formId).submit(); });
}
</script>
@endsection
