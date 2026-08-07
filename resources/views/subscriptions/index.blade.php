@extends('layouts.app')
@section('title', 'Subscriptions')
@section('page-title', 'Subscriptions')

@section('content')

<script>
function subApp() {
    return {
        showModal: false,
        editingSub: null,

        openCreate() {
            this.editingSub = null;
            this.showModal = true;
            this.$nextTick(() => this.fillForm({}));
        },

        openEdit(data) {
            this.editingSub = data;
            this.showModal = true;
            this.$nextTick(() => this.fillForm(data));
        },

        fillForm(data) {
            const s = (id, val) => {
                const el = document.getElementById(id);
                if (el) el.value = (val !== null && val !== undefined) ? val : '';
            };
            const c = (id, val) => {
                const el = document.getElementById(id);
                if (el) el.checked = !!val;
            };
            s('sub-name',           data.name           || '');
            s('sub-provider',       data.provider       || '');
            s('sub-category',       data.category       || '');
            s('sub-billing_cycle',  data.billing_cycle  || 'monthly');
            s('sub-monthly_cost',   data.monthly_cost   ?? '');
            s('sub-annual_cost',    data.annual_cost    ?? '');
            s('sub-due_date',       data.due_date ? String(data.due_date).slice(0,10) : '');
            s('sub-payment_status', data.payment_status || 'unpaid');
            s('sub-reminder_days',  data.reminder_days  ?? '3');
            s('sub-notes',          data.notes          || '');
            c('sub-is_active',      data.is_active !== undefined ? data.is_active : true);
        },

        closeModal() {
            this.showModal = false;
            this.editingSub = null;
        },

        confirmDelete(formId, name) {
            SwalDanger.fire({
                title: 'Delete Subscription?',
                html: `"<strong>${name}</strong>" will be permanently deleted.`,
                confirmButtonText: 'Yes, delete!',
                cancelButtonText: 'Cancel',
            }).then(r => { if (r.isConfirmed) document.getElementById(formId).submit(); });
        },

        confirmTogglePayment(formId, current) {
            const to = current === 'paid' ? 'Unpaid' : 'Paid';
            SwalNeo.fire({
                title: `Mark as ${to}?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: `Yes, mark ${to}`,
                cancelButtonText: 'Cancel',
            }).then(r => { if (r.isConfirmed) document.getElementById(formId).submit(); });
        },

        confirmToggleActive(formId, current) {
            const to = current ? 'inactive' : 'active';
            SwalNeo.fire({
                title: `Mark as ${to}?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: `Yes`,
                cancelButtonText: 'Cancel',
            }).then(r => { if (r.isConfirmed) document.getElementById(formId).submit(); });
        },
    };
}
</script>

<div x-data="subApp()">

    {{-- Stats Bar --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        {{-- Monthly Total --}}
        <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-5 flex items-center gap-4 transition-all hover:-translate-y-1 hover:shadow-hard-hover">
            <div class="w-12 h-12 bg-primary border-2 border-border-dark rounded-button flex items-center justify-center flex-shrink-0 shadow-hard-sm">
                <i class="bx bx-receipt text-white text-xl"></i>
            </div>
            <div>
                <p class="text-lg font-extrabold leading-tight">Rp {{ number_format($stats['monthlyTotal'] ?? 0, 0, ',', '.') }}</p>
                <p class="text-xs font-semibold text-txt-secondary mt-0.5">Monthly Total</p>
            </div>
        </div>
        {{-- Annual Total --}}
        <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-5 flex items-center gap-4 transition-all hover:-translate-y-1 hover:shadow-hard-hover">
            <div class="w-12 h-12 bg-[#F59E0B] border-2 border-border-dark rounded-button flex items-center justify-center flex-shrink-0 shadow-hard-sm">
                <i class="bx bx-calendar text-white text-xl"></i>
            </div>
            <div>
                <p class="text-lg font-extrabold leading-tight">Rp {{ number_format($stats['annualTotal'] ?? 0, 0, ',', '.') }}</p>
                <p class="text-xs font-semibold text-txt-secondary mt-0.5">Annual Total</p>
            </div>
        </div>
        {{-- Active --}}
        <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-5 flex items-center gap-4 transition-all hover:-translate-y-1 hover:shadow-hard-hover">
            <div class="w-12 h-12 bg-green-500 border-2 border-border-dark rounded-button flex items-center justify-center flex-shrink-0 shadow-hard-sm">
                <i class="bx bx-check-circle text-white text-xl"></i>
            </div>
            <div>
                <p class="text-3xl font-extrabold leading-none">{{ $stats['active'] ?? 0 }}</p>
                <p class="text-xs font-semibold text-txt-secondary mt-0.5">Active</p>
            </div>
        </div>
        {{-- Unpaid --}}
        <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-5 flex items-center gap-4 transition-all hover:-translate-y-1 hover:shadow-hard-hover">
            <div class="w-12 h-12 bg-danger border-2 border-border-dark rounded-button flex items-center justify-center flex-shrink-0 shadow-hard-sm">
                <i class="bx bx-error text-white text-xl"></i>
            </div>
            <div>
                <p class="text-3xl font-extrabold leading-none">{{ $stats['unpaid'] ?? 0 }}</p>
                <p class="text-xs font-semibold text-txt-secondary mt-0.5">Unpaid</p>
            </div>
        </div>
    </div>

    {{-- Toolbar Card --}}
    <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-5 mb-6">
        <form method="GET" action="{{ route('subscriptions.index') }}"
              class="flex flex-col sm:flex-row gap-3 items-start sm:items-center flex-wrap">
            <select name="category"
                class="px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium focus:border-primary outline-none">
                <option value="">All Categories</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat }}" {{ $categoryFilter === $cat ? 'selected' : '' }}>{{ ucfirst($cat) }}</option>
                @endforeach
            </select>
            <select name="status"
                class="px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium focus:border-primary outline-none">
                <option value="">All Status</option>
                <option value="paid"   {{ $statusFilter === 'paid'   ? 'selected' : '' }}>Paid</option>
                <option value="unpaid" {{ $statusFilter === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
            </select>
            <button type="submit"
                class="px-4 py-3 bg-surface font-bold text-sm rounded-button border-4 border-border-dark shadow-hard-sm hover:-translate-y-0.5 transition-all">
                <i class="bx bx-filter-alt mr-1"></i> Filter
            </button>
            <button type="button" @click="openCreate()"
                class="px-6 py-3 bg-primary text-white font-bold text-sm rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 active:translate-y-1 transition-all sm:ml-auto flex items-center gap-2">
                <i class="bx bx-plus text-lg"></i> New Subscription
            </button>
        </form>
    </div>

    {{-- Flash --}}
    @if (session('success'))
        <div class="mb-5 px-5 py-4 bg-surface border-4 border-green-500 rounded-card shadow-hard flex items-center gap-3">
            <i class="bx bx-check-circle text-green-500 text-2xl"></i>
            <span class="font-semibold text-sm">{{ session('success') }}</span>
        </div>
    @endif

    {{-- Category color map --}}
    @php
    $catColors = [
        'software'      => ['bg' => 'bg-blue-100',   'text' => 'text-blue-700',   'border' => 'border-blue-400',   'icon' => 'bx-code-alt'],
        'hosting'       => ['bg' => 'bg-purple-100',  'text' => 'text-purple-700', 'border' => 'border-purple-400', 'icon' => 'bx-server'],
        'design'        => ['bg' => 'bg-pink-100',    'text' => 'text-pink-700',   'border' => 'border-pink-400',   'icon' => 'bx-palette'],
        'marketing'     => ['bg' => 'bg-orange-100',  'text' => 'text-orange-700', 'border' => 'border-orange-400', 'icon' => 'bx-bullseye'],
        'cloud'         => ['bg' => 'bg-cyan-100',    'text' => 'text-cyan-700',   'border' => 'border-cyan-400',   'icon' => 'bx-cloud'],
        'security'      => ['bg' => 'bg-red-100',     'text' => 'text-red-700',    'border' => 'border-red-400',    'icon' => 'bx-shield'],
        'analytics'     => ['bg' => 'bg-indigo-100',  'text' => 'text-indigo-700', 'border' => 'border-indigo-400', 'icon' => 'bx-bar-chart'],
        'communication' => ['bg' => 'bg-teal-100',    'text' => 'text-teal-700',   'border' => 'border-teal-400',   'icon' => 'bx-message'],
        'education'     => ['bg' => 'bg-yellow-100',  'text' => 'text-yellow-700', 'border' => 'border-yellow-400', 'icon' => 'bx-book'],
        'payment'       => ['bg' => 'bg-green-100',   'text' => 'text-green-700',  'border' => 'border-green-400',  'icon' => 'bx-credit-card'],
        'storage'       => ['bg' => 'bg-amber-100',   'text' => 'text-amber-700',  'border' => 'border-amber-400',  'icon' => 'bx-hdd'],
        'devtools'      => ['bg' => 'bg-slate-100',   'text' => 'text-slate-700',  'border' => 'border-slate-400',  'icon' => 'bx-terminal'],
        'vpn'           => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-700','border' => 'border-emerald-400','icon' => 'bx-lock'],
        'email'         => ['bg' => 'bg-violet-100',  'text' => 'text-violet-700', 'border' => 'border-violet-400', 'icon' => 'bx-envelope'],
    ];
    $defaultCat = ['bg' => 'bg-gray-100', 'text' => 'text-gray-600', 'border' => 'border-gray-400', 'icon' => 'bx-grid-alt'];
    @endphp

    {{-- Empty state --}}
    @if ($subscriptions->isEmpty())
        <div class="flex flex-col items-center justify-center py-20 bg-surface border-4 border-border-dark rounded-card shadow-hard">
            <i class="bx bx-receipt text-7xl text-txt-secondary"></i>
            <h3 class="text-xl font-extrabold mt-4">No subscriptions yet</h3>
            <p class="text-txt-secondary mt-2 text-sm">Add your first subscription to track expenses</p>
            <button type="button" @click="openCreate()"
                class="mt-5 px-6 py-3 bg-primary text-white font-bold text-sm rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 transition-all">
                <i class="bx bx-plus mr-1"></i> New Subscription
            </button>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach ($subscriptions as $sub)
                @php
                    $catKey  = strtolower($sub->category ?? '');
                    $cc      = $catColors[$catKey] ?? $defaultCat;
                    $isPaid  = $sub->payment_status === 'paid';
                    $isOver  = !$isPaid && $sub->due_date && $sub->due_date->isPast();
                    $cost    = $sub->billing_cycle === 'monthly'
                                ? ($sub->monthly_cost ?? 0)
                                : ($sub->annual_cost ?? ($sub->monthly_cost ?? 0) * 12);
                @endphp
                <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-5 flex flex-col transition-all duration-200 hover:-translate-y-1.5 hover:shadow-hard-hover {{ !$sub->is_active ? 'opacity-60' : '' }}">

                    {{-- Header --}}
                    <div class="flex items-start gap-3 mb-4">
                        <div class="w-11 h-11 {{ $cc['bg'] }} border-2 {{ $cc['border'] }} rounded-button flex items-center justify-center flex-shrink-0">
                            <i class="bx {{ $cc['icon'] }} {{ $cc['text'] }} text-xl"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-extrabold text-sm leading-snug truncate">{{ $sub->name }}</h3>
                            @if ($sub->provider)
                                <p class="text-xs text-txt-secondary mt-0.5">{{ $sub->provider }}</p>
                            @endif
                        </div>
                        <div class="flex flex-col items-end gap-1 flex-shrink-0">
                            {{-- Paid/Unpaid badge --}}
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 text-xs font-bold border-2 rounded-full
                                {{ $isPaid ? 'bg-green-100 text-green-700 border-green-400' : ($isOver ? 'bg-red-100 text-red-700 border-red-400' : 'bg-orange-100 text-orange-700 border-orange-400') }}">
                                <i class="bx {{ $isPaid ? 'bx-check' : 'bx-x' }} text-xs"></i>
                                {{ $isPaid ? 'Paid' : ($isOver ? 'Overdue' : 'Unpaid') }}
                            </span>
                            {{-- Billing cycle --}}
                            <span class="inline-flex items-center px-2 py-0.5 text-xs font-bold border-2 border-border-dark rounded-full bg-primary/10 text-primary">
                                {{ ucfirst($sub->billing_cycle) }}
                            </span>
                        </div>
                    </div>

                    {{-- Cost highlight --}}
                    <div class="bg-bgmain border-2 border-border-dark rounded-button px-4 py-3 mb-4 flex items-center justify-between">
                        <span class="text-xs font-semibold text-txt-secondary">
                            {{ $sub->billing_cycle === 'monthly' ? 'Monthly' : 'Annual' }} Cost
                        </span>
                        <span class="text-lg font-extrabold text-txt-primary">
                            Rp {{ number_format($cost, 0, ',', '.') }}
                        </span>
                    </div>

                    {{-- Details --}}
                    <div class="space-y-2 text-xs mb-4 flex-1">
                        @if ($sub->category)
                            <div class="flex items-center justify-between">
                                <span class="text-txt-secondary flex items-center gap-1"><i class="bx bx-category"></i> Category</span>
                                <span class="font-bold px-2 py-0.5 border-2 {{ $cc['border'] }} {{ $cc['bg'] }} {{ $cc['text'] }} rounded-full">
                                    {{ ucfirst($sub->category) }}
                                </span>
                            </div>
                        @endif
                        <div class="flex items-center justify-between">
                            <span class="text-txt-secondary flex items-center gap-1"><i class="bx bx-calendar"></i> Due Date</span>
                            <span class="font-semibold {{ $isOver ? 'text-danger' : '' }}">
                                {{ $sub->due_date?->format('d M Y') ?? '-' }}
                                @if ($isOver) <i class="bx bx-error-circle text-danger"></i> @endif
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-txt-secondary flex items-center gap-1"><i class="bx bx-bell"></i> Reminder</span>
                            <span class="font-semibold">{{ $sub->reminder_days ?? 0 }} days before</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-txt-secondary flex items-center gap-1"><i class="bx bx-toggle-right"></i> Status</span>
                            <span class="font-bold {{ $sub->is_active ? 'text-green-600' : 'text-txt-secondary' }}">
                                {{ $sub->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center gap-2 pt-3 border-t-4 border-border-dark mt-auto">
                        {{-- Toggle Payment --}}
                        <form id="tp-{{ $sub->id }}" method="POST" action="{{ route('subscriptions.toggle-payment', $sub) }}">
                            @csrf @method('PATCH')
                            <button type="button"
                                @click="confirmTogglePayment('tp-{{ $sub->id }}', '{{ $sub->payment_status }}')"
                                class="px-3 py-2 text-xs font-bold rounded-button border-4 border-border-dark shadow-hard-sm hover:-translate-y-0.5 transition-all
                                    {{ $isPaid ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700' }}">
                                <i class="bx {{ $isPaid ? 'bx-check-circle' : 'bx-time-five' }} text-sm"></i>
                            </button>
                        </form>
                        {{-- Toggle Active --}}
                        <form id="ta-{{ $sub->id }}" method="POST" action="{{ route('subscriptions.toggle-active', $sub) }}">
                            @csrf @method('PATCH')
                            <button type="button"
                                @click="confirmToggleActive('ta-{{ $sub->id }}', {{ $sub->is_active ? 'true' : 'false' }})"
                                class="px-3 py-2 text-xs font-bold rounded-button border-4 border-border-dark shadow-hard-sm hover:-translate-y-0.5 transition-all
                                    {{ $sub->is_active ? 'bg-primary/10 text-primary' : 'bg-gray-100 text-txt-secondary' }}">
                                <i class="bx {{ $sub->is_active ? 'bx-toggle-right' : 'bx-toggle-left' }} text-base"></i>
                            </button>
                        </form>
                        {{-- Edit --}}
                        <button type="button" @click="openEdit({{ Js::from($sub) }})"
                            class="px-3 py-2 text-xs font-bold rounded-button border-4 border-border-dark bg-surface shadow-hard-sm hover:-translate-y-0.5 transition-all text-txt-secondary hover:text-primary">
                            <i class="bx bx-edit-alt text-base"></i>
                        </button>
                        {{-- Delete --}}
                        <form id="del-{{ $sub->id }}" method="POST" action="{{ route('subscriptions.destroy', $sub) }}" class="ml-auto">
                            @csrf @method('DELETE')
                            <button type="button"
                                @click="confirmDelete('del-{{ $sub->id }}', {{ Js::from($sub->name) }})"
                                class="px-3 py-2 text-xs font-bold rounded-button border-4 border-border-dark bg-surface shadow-hard-sm hover:-translate-y-0.5 transition-all text-danger hover:bg-danger hover:text-white">
                                <i class="bx bx-trash-alt text-base"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if ($subscriptions->hasPages())
            <div class="mt-8">{{ $subscriptions->links() }}</div>
        @endif
    @endif

    {{-- ============================================================
         MODAL: Add / Edit Subscription
    ============================================================ --}}
    <div x-show="showModal"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @keydown.escape.window="closeModal()"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 overflow-y-auto"
        style="display:none;">

        <div @click.stop
            x-show="showModal"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="scale-95 opacity-0"
            x-transition:enter-end="scale-100 opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="scale-100 opacity-100"
            x-transition:leave-end="scale-95 opacity-0"
            class="bg-surface border-4 border-border-dark rounded-modal shadow-hard w-full max-w-lg my-4"
            style="display:none;">

            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b-4 border-border-dark sticky top-0 bg-surface rounded-t-modal z-10">
                <h3 class="text-lg font-extrabold" x-text="editingSub ? 'Edit Subscription' : 'New Subscription'"></h3>
                <button @click="closeModal()" class="text-2xl text-txt-secondary hover:text-danger transition-colors">
                    <i class="bx bx-x"></i>
                </button>
            </div>

            {{-- Form --}}
            <form method="POST"
                  :action="editingSub ? '/subscriptions/' + editingSub.id : '{{ route('subscriptions.store') }}'"
                  class="p-6 space-y-4 overflow-y-auto max-h-[75vh]">
                @csrf
                <input type="hidden" name="_method" :value="editingSub ? 'PATCH' : 'POST'">

                {{-- Name --}}
                <div>
                    <label class="block text-sm font-bold mb-1.5">Name <span class="text-danger">*</span></label>
                    <input type="text" id="sub-name" name="name" required maxlength="255"
                        placeholder="E.g. GitHub Pro"
                        class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none transition-colors">
                </div>

                {{-- Provider --}}
                <div>
                    <label class="block text-sm font-bold mb-1.5">Provider</label>
                    <input type="text" id="sub-provider" name="provider" maxlength="255"
                        placeholder="E.g. GitHub, AWS"
                        class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none transition-colors">
                </div>

                {{-- Category + Billing Cycle --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold mb-1.5">Category</label>
                        <input type="text" id="sub-category" name="category" maxlength="100"
                            placeholder="E.g. Software"
                            list="cat-options"
                            class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium focus:border-primary outline-none transition-colors">
                        <datalist id="cat-options">
                            <option value="Software"><option value="Hosting"><option value="Cloud">
                            <option value="Design"><option value="Marketing"><option value="Security">
                            <option value="Analytics"><option value="DevTools"><option value="Email">
                            <option value="Storage"><option value="VPN"><option value="Education">
                        </datalist>
                    </div>
                    <div>
                        <label class="block text-sm font-bold mb-1.5">Billing Cycle</label>
                        <select id="sub-billing_cycle" name="billing_cycle"
                            class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium focus:border-primary outline-none">
                            <option value="monthly">Monthly</option>
                            <option value="quarterly">Quarterly</option>
                            <option value="yearly">Yearly</option>
                        </select>
                    </div>
                </div>

                {{-- Monthly + Annual Cost --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold mb-1.5">Monthly Cost</label>
                        <input type="number" id="sub-monthly_cost" name="monthly_cost"
                            step="0.01" min="0" placeholder="0"
                            class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium focus:border-primary outline-none transition-colors">
                    </div>
                    <div>
                        <label class="block text-sm font-bold mb-1.5">Annual Cost</label>
                        <input type="number" id="sub-annual_cost" name="annual_cost"
                            step="0.01" min="0" placeholder="0"
                            class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium focus:border-primary outline-none transition-colors">
                    </div>
                </div>

                {{-- Due Date + Reminder --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold mb-1.5">Due Date <span class="text-danger">*</span></label>
                        <input type="date" id="sub-due_date" name="due_date" required
                            class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium focus:border-primary outline-none transition-colors">
                    </div>
                    <div>
                        <label class="block text-sm font-bold mb-1.5">Reminder (days)</label>
                        <input type="number" id="sub-reminder_days" name="reminder_days"
                            min="0" max="90" placeholder="3"
                            class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium focus:border-primary outline-none transition-colors">
                    </div>
                </div>

                {{-- Payment Status --}}
                <div>
                    <label class="block text-sm font-bold mb-1.5">Payment Status</label>
                    <select id="sub-payment_status" name="payment_status"
                        class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium focus:border-primary outline-none">
                        <option value="unpaid">Unpaid</option>
                        <option value="paid">Paid</option>
                    </select>
                </div>

                {{-- Notes --}}
                <div>
                    <label class="block text-sm font-bold mb-1.5">Notes</label>
                    <textarea id="sub-notes" name="notes" rows="2"
                        placeholder="Optional notes..."
                        class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none resize-none transition-colors"></textarea>
                </div>

                {{-- Active checkbox --}}
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" id="sub-is_active" name="is_active" value="1"
                        class="w-4 h-4 accent-primary">
                    <span class="text-sm font-medium">Active</span>
                    <i class="bx bx-check-circle text-green-500 text-base"></i>
                </label>

                {{-- Footer --}}
                <div class="flex justify-end gap-3 pt-2 border-t-4 border-border-dark">
                    <button type="button" @click="closeModal()"
                        class="px-6 py-3 font-bold text-sm rounded-button border-4 border-border-dark bg-surface shadow-hard hover:-translate-y-0.5 transition-all">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-6 py-3 bg-primary text-white font-bold text-sm rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 active:translate-y-1 transition-all">
                        <i class="bx bx-save mr-1"></i>
                        <span x-text="editingSub ? 'Update' : 'Save'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>{{-- end x-data --}}
@endsection
