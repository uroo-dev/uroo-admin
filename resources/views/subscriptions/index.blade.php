@extends('layouts.app')

@section('title', 'Subscriptions')
@section('page-title', 'Subscriptions')

@section('content')

    {{-- Flash success toast --}}
    @if (session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: @json(session('success')),
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
            });
        });
    </script>
    @endif

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-primary rounded-button flex items-center justify-center shadow-hard flex-shrink-0">
                    <i class="bx bx-receipt text-white text-[28px]"></i>
                </div>
                <div>
                    <p class="text-3xl font-extrabold">Rp {{ number_format($stats['monthlyTotal'] ?? 0, 0, ',', '.') }}</p>
                    <p class="text-sm font-medium text-txt-secondary">Monthly Total</p>
                </div>
            </div>
        </div>

        <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-[#F59E0B] rounded-button flex items-center justify-center shadow-hard flex-shrink-0">
                    <i class="bx bx-calendar text-white text-[28px]"></i>
                </div>
                <div>
                    <p class="text-3xl font-extrabold">Rp {{ number_format($stats['annualTotal'] ?? 0, 0, ',', '.') }}</p>
                    <p class="text-sm font-medium text-txt-secondary">Annual Total</p>
                </div>
            </div>
        </div>

        <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-[#22C55E] rounded-button flex items-center justify-center shadow-hard flex-shrink-0">
                    <i class="bx bx-check-circle text-white text-[28px]"></i>
                </div>
                <div>
                    <p class="text-3xl font-extrabold">{{ $stats['active'] ?? 0 }}</p>
                    <p class="text-sm font-medium text-txt-secondary">Active</p>
                </div>
            </div>
        </div>

        <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-danger rounded-button flex items-center justify-center shadow-hard flex-shrink-0">
                    <i class="bx bx-error text-white text-[28px]"></i>
                </div>
                <div>
                    <p class="text-3xl font-extrabold">{{ $stats['unpaid'] ?? 0 }}</p>
                    <p class="text-sm font-medium text-txt-secondary">Unpaid</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Toolbar --}}
    <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 mb-8">
        <form method="GET" action="{{ route('subscriptions.index') }}" class="flex flex-col sm:flex-row gap-4 items-center justify-between">
            <div class="flex flex-col sm:flex-row gap-4 w-full sm:w-auto">
                <select name="category" class="px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium text-txt-primary focus:border-primary outline-none transition-colors">
                    <option value="">All Categories</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat }}" {{ $categoryFilter === $cat ? 'selected' : '' }}>{{ ucfirst($cat) }}</option>
                    @endforeach
                </select>
                <select name="status" class="px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium text-txt-primary focus:border-primary outline-none transition-colors">
                    <option value="">All Status</option>
                    <option value="paid" {{ $statusFilter === 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="unpaid" {{ $statusFilter === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                </select>
                <button type="submit" class="px-5 py-3 bg-surface text-txt-primary font-bold text-sm rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 active:translate-y-1 active:shadow-hard-pressed transition-all duration-200 ease-out w-full sm:w-auto">
                    <i class="bx bx-filter mr-1"></i> Filter
                </button>
            </div>
            <button type="button" onclick="openAddModal()" class="px-6 py-3 bg-primary text-white font-bold text-sm rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 active:translate-y-1 active:shadow-hard-pressed transition-all duration-200 ease-out flex items-center gap-2 w-full sm:w-auto">
                <i class="bx bx-plus text-lg"></i>
                New Subscription
            </button>
        </form>
    </div>



    {{-- Subscription Cards Grid --}}
    @if ($subscriptions->isEmpty())
        <div class="flex flex-col items-center justify-center py-16 bg-surface border-4 border-border-dark rounded-card shadow-hard">
            <i class="bx bx-receipt text-6xl text-txt-secondary"></i>
            <h3 class="text-xl font-extrabold mt-4">No subscriptions yet</h3>
            <p class="text-txt-secondary mt-2">Add your first subscription to get started</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($subscriptions as $sub)
                @php
                    $categoryIcons = [
                        'software'      => 'bx-code',
                        'hosting'       => 'bx-server',
                        'design'        => 'bx-palette',
                        'marketing'     => 'bx-bullseye',
                        'office'        => 'bx-folder',
                        'cloud'         => 'bx-cloud',
                        'security'      => 'bx-shield',
                        'analytics'     => 'bx-bar-chart',
                        'communication' => 'bx-message',
                        'education'     => 'bx-book',
                        'payment'       => 'bx-credit-card',
                        'social'        => 'bx-share-alt',
                        'email'         => 'bx-envelope',
                        'storage'       => 'bx-hard-drive',
                        'database'      => 'bx-data',
                        'vpn'           => 'bx-lock',
                        'monitoring'    => 'bx-task',
                        'backup'        => 'bx-data',
                        'api'           => 'bx-key',
                        'devtools'      => 'bx-terminal',
                    ];
                    $iconName = $categoryIcons[strtolower($sub->category)] ?? 'bx-grid-alt';
                    $isPaid   = $sub->payment_status === 'paid';
                @endphp
                <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover flex flex-col">

                    {{-- Card Header --}}
                    <div class="flex items-start gap-4 mb-4">
                        <div class="w-12 h-12 bg-primary/10 rounded-button flex items-center justify-center shadow-hard-sm flex-shrink-0">
                            <i class="bx {{ $iconName }} text-primary text-xl"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-extrabold text-sm leading-tight truncate">{{ $sub->name }}</h3>
                            @if ($sub->provider)
                                <p class="text-xs text-txt-secondary mt-0.5">{{ $sub->provider }}</p>
                            @endif
                        </div>
                        <div class="flex flex-col items-end gap-1 flex-shrink-0">
                            <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-bold border-2 border-border-dark rounded-full bg-primary/10 text-primary">
                                {{ ucfirst($sub->billing_cycle) }}
                            </span>
                            <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-bold border-2 border-border-dark rounded-full {{ $isPaid ? 'bg-[#22C55E] text-white' : 'bg-danger text-white' }}">
                                {{ ucfirst($sub->payment_status) }}
                            </span>
                        </div>
                    </div>

                    {{-- Card Body --}}
                    <div class="space-y-2 text-sm mb-4 flex-1">
                        @if ($sub->category)
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-txt-secondary flex items-center gap-1.5"><i class="bx bx-category text-base"></i> Category</span>
                                <span class="font-semibold">{{ ucfirst($sub->category) }}</span>
                            </div>
                        @endif
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-txt-secondary flex items-center gap-1.5"><i class="bx bx-dollar-circle text-base"></i> Cost</span>
                            <span class="font-extrabold">Rp {{ number_format($sub->billing_cycle === 'monthly' ? ($sub->monthly_cost ?? 0) : ($sub->annual_cost ?? ($sub->monthly_cost ?? 0) * 12), 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-txt-secondary flex items-center gap-1.5"><i class="bx bx-calendar text-base"></i> Due</span>
                            <span class="font-semibold whitespace-nowrap">{{ $sub->due_date?->format('d M Y') ?? '-' }}</span>
                        </div>
                        @if ($sub->reminder_days)
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-txt-secondary flex items-center gap-1.5"><i class="bx bx-bell text-base"></i> Reminder</span>
                                <span class="font-semibold">{{ $sub->reminder_days }} days before</span>
                            </div>
                        @endif
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-txt-secondary flex items-center gap-1.5"><i class="bx bx-toggle text-base"></i> Active</span>
                            <span class="font-semibold {{ $sub->is_active ? 'text-[#22C55E]' : 'text-txt-secondary' }}">{{ $sub->is_active ? 'Yes' : 'No' }}</span>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex items-center gap-2 pt-4 border-t-4 border-border-dark mt-auto">
                        <form id="toggle-payment-form-{{ $sub->id }}" action="{{ route('subscriptions.toggle-payment', $sub) }}" method="POST" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="button" onclick="togglePayment({{ $sub->id }}, '{{ $sub->payment_status }}')"
                                class="flex-1 px-3 py-2 font-bold text-xs rounded-button border-4 border-border-dark bg-surface shadow-hard-sm hover:-translate-y-0.5 active:translate-y-0.5 transition-all duration-200 ease-out flex items-center justify-center gap-1.5 {{ $isPaid ? 'text-[#22C55E]' : 'text-danger' }}">
                                <i class="bx {{ $isPaid ? 'bx-check' : 'bx-x' }} text-sm"></i>
                                {{ $isPaid ? 'Paid' : 'Unpaid' }}
                            </button>
                        </form>
                        <form id="toggle-active-form-{{ $sub->id }}" action="{{ route('subscriptions.toggle-active', $sub) }}" method="POST" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="button" onclick="toggleActive({{ $sub->id }}, {{ $sub->is_active ? 'true' : 'false' }})"
                                class="px-3 py-2 font-bold text-xs rounded-button border-4 border-border-dark bg-surface shadow-hard-sm hover:-translate-y-0.5 active:translate-y-0.5 transition-all duration-200 ease-out {{ $sub->is_active ? 'text-primary' : 'text-txt-secondary' }}">
                                <i class="bx {{ $sub->is_active ? 'bx-toggle-right' : 'bx-toggle-left' }} text-base"></i>
                            </button>
                        </form>
                        <button type="button" onclick="openEditModal({{ json_encode($sub) }})"
                            class="px-3 py-2 font-bold text-xs rounded-button border-4 border-border-dark bg-surface shadow-hard-sm hover:-translate-y-0.5 active:translate-y-0.5 transition-all duration-200 ease-out text-txt-secondary hover:text-primary">
                            <i class="bx bx-edit text-base"></i>
                        </button>
                        <form id="delete-form-sub-{{ $sub->id }}" action="{{ route('subscriptions.destroy', $sub) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="button" onclick="deleteSub('delete-form-sub-{{ $sub->id }}')"
                                class="px-3 py-2 font-bold text-xs rounded-button border-4 border-border-dark bg-surface shadow-hard-sm hover:-translate-y-0.5 active:translate-y-0.5 transition-all duration-200 ease-out text-danger">
                                <i class="bx bx-trash text-base"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Pagination --}}
    <div class="mt-8">
        {{ $subscriptions->links() }}
    </div>



    {{-- Add/Edit Subscription Modal --}}
    <div id="subscription-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" style="display:none;" onclick="handleModalBackdropClick(event)">
        <div id="subscription-modal-box" class="bg-surface border-4 border-border-dark rounded-modal shadow-hard w-full max-w-lg">

            <div class="flex items-center justify-between px-6 py-4 border-b-4 border-border-dark">
                <h3 id="modal-title" class="text-lg font-extrabold">New Subscription</h3>
                <button type="button" onclick="closeModal()" class="text-2xl text-txt-secondary hover:text-danger transition-colors">
                    <i class="bx bx-x"></i>
                </button>
            </div>

            <form id="subscription-form" method="POST" action="{{ route('subscriptions.store') }}" class="p-6 space-y-4 overflow-y-auto max-h-[80vh]">
                @csrf
                <input type="hidden" name="_method" id="form-method" value="">

                <div>
                    <label for="sub-name" class="block text-sm font-semibold text-txt-primary mb-1.5">Name</label>
                    <input type="text" id="sub-name" name="name" required
                        class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none transition-colors"
                        placeholder="Subscription name">
                </div>

                <div>
                    <label for="sub-provider" class="block text-sm font-semibold text-txt-primary mb-1.5">Provider</label>
                    <input type="text" id="sub-provider" name="provider"
                        class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none transition-colors"
                        placeholder="e.g. AWS, Stripe">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="sub-category" class="block text-sm font-semibold text-txt-primary mb-1.5">Category</label>
                        <input type="text" id="sub-category" name="category"
                            class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none transition-colors"
                            placeholder="e.g. Software">
                    </div>
                    <div>
                        <label for="sub-billing_cycle" class="block text-sm font-semibold text-txt-primary mb-1.5">Billing Cycle</label>
                        <select id="sub-billing_cycle" name="billing_cycle"
                            class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium focus:border-primary outline-none transition-colors">
                            <option value="monthly">Monthly</option>
                            <option value="quarterly">Quarterly</option>
                            <option value="yearly">Yearly</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="sub-monthly_cost" class="block text-sm font-semibold text-txt-primary mb-1.5">Monthly Cost</label>
                        <input type="number" id="sub-monthly_cost" name="monthly_cost" step="0.01" min="0"
                            class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none transition-colors"
                            placeholder="0.00">
                    </div>
                    <div>
                        <label for="sub-annual_cost" class="block text-sm font-semibold text-txt-primary mb-1.5">Annual Cost</label>
                        <input type="number" id="sub-annual_cost" name="annual_cost" step="0.01" min="0"
                            class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none transition-colors"
                            placeholder="0.00">
                    </div>
                </div>

                <div>
                    <label for="sub-due_date" class="block text-sm font-semibold text-txt-primary mb-1.5">Due Date</label>
                    <input type="date" id="sub-due_date" name="due_date" required
                        class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium focus:border-primary outline-none transition-colors">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="sub-payment_status" class="block text-sm font-semibold text-txt-primary mb-1.5">Payment Status</label>
                        <select id="sub-payment_status" name="payment_status"
                            class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium focus:border-primary outline-none transition-colors">
                            <option value="unpaid">Unpaid</option>
                            <option value="paid">Paid</option>
                        </select>
                    </div>
                    <div>
                        <label for="sub-reminder_days" class="block text-sm font-semibold text-txt-primary mb-1.5">Reminder Days</label>
                        <input type="number" id="sub-reminder_days" name="reminder_days" min="0" max="90"
                            class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium focus:border-primary outline-none transition-colors"
                            placeholder="0">
                    </div>
                </div>

                <div>
                    <label for="sub-notes" class="block text-sm font-semibold text-txt-primary mb-1.5">Notes</label>
                    <textarea id="sub-notes" name="notes" rows="3"
                        class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none resize-none"
                        placeholder="Optional notes..."></textarea>
                </div>

                <div class="flex items-center gap-3">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" id="sub-is_active" name="is_active" value="1"
                            class="w-4 h-4 accent-primary rounded border-4 border-border-dark">
                        <span class="text-sm font-medium text-txt-primary">Active</span>
                    </label>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" onclick="closeModal()"
                        class="px-6 py-3 font-bold text-sm rounded-button border-4 border-border-dark bg-surface shadow-hard hover:-translate-y-0.5 transition-all duration-200 ease-out">
                        Cancel
                    </button>
                    <button type="submit" id="modal-submit-btn"
                        class="px-6 py-3 bg-primary text-white font-bold text-sm rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 active:translate-y-1 active:shadow-hard-pressed transition-all duration-200 ease-out">
                        Create Subscription
                    </button>
                </div>
            </form>
        </div>
    </div>



    <script>
        // ── Modal helpers ─────────────────────────────────────────────────────────
        var STORE_URL  = @json(route('subscriptions.store'));
        var UPDATE_URL = @json(route('subscriptions.update', ['__ID__']));

        function setVal(id, val) {
            var el = document.getElementById(id);
            if (el) el.value = val !== null && val !== undefined ? val : '';
        }

        function openAddModal() {
            document.getElementById('modal-title').textContent      = 'New Subscription';
            document.getElementById('modal-submit-btn').textContent = 'Create Subscription';
            document.getElementById('subscription-form').action     = STORE_URL;
            document.getElementById('form-method').value            = '';

            setVal('sub-name',           '');
            setVal('sub-provider',       '');
            setVal('sub-category',       '');
            setVal('sub-billing_cycle',  'monthly');
            setVal('sub-monthly_cost',   '');
            setVal('sub-annual_cost',    '');
            setVal('sub-due_date',       '');
            setVal('sub-payment_status', 'unpaid');
            setVal('sub-reminder_days',  '0');
            setVal('sub-notes',          '');
            document.getElementById('sub-is_active').checked = true;

            document.getElementById('subscription-modal').style.display = 'flex';
        }

        function openEditModal(sub) {
            document.getElementById('modal-title').textContent      = 'Edit Subscription';
            document.getElementById('modal-submit-btn').textContent = 'Update Subscription';
            document.getElementById('subscription-form').action     = UPDATE_URL.replace('__ID__', sub.id);
            document.getElementById('form-method').value            = 'PUT';

            setVal('sub-name',           sub.name        || '');
            setVal('sub-provider',       sub.provider    || '');
            setVal('sub-category',       sub.category    || '');
            setVal('sub-billing_cycle',  sub.billing_cycle || 'monthly');
            setVal('sub-monthly_cost',   sub.monthly_cost !== null ? sub.monthly_cost : '');
            setVal('sub-annual_cost',    sub.annual_cost  !== null ? sub.annual_cost  : '');

            // due_date may come as "YYYY-MM-DD HH:MM:SS" — take first 10 chars
            var dueDate = sub.due_date ? String(sub.due_date).slice(0, 10) : '';
            setVal('sub-due_date', dueDate);

            setVal('sub-payment_status', sub.payment_status || 'unpaid');
            setVal('sub-reminder_days',  sub.reminder_days  !== null ? sub.reminder_days : '0');
            setVal('sub-notes',          sub.notes          || '');

            document.getElementById('sub-is_active').checked = (sub.is_active === true || sub.is_active === 1);

            document.getElementById('subscription-modal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('subscription-modal').style.display = 'none';
        }

        function handleModalBackdropClick(event) {
            if (event.target === document.getElementById('subscription-modal')) {
                closeModal();
            }
        }

        // ── SweetAlert helpers ────────────────────────────────────────────────────
        var SWAL_COMMON = {
            background: '#FFFFFF',
            customClass: { popup: 'border-4 border-border-dark rounded-modal shadow-hard' }
        };

        function deleteSub(formId) {
            Swal.fire(Object.assign({}, SWAL_COMMON, {
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#EF4444',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Batal'
            })).then(function(result) {
                if (result.isConfirmed) {
                    document.getElementById(formId).submit();
                }
            });
        }

        function togglePayment(subId, currentStatus) {
            var from = currentStatus === 'paid' ? 'Paid' : 'Unpaid';
            var to   = currentStatus === 'paid' ? 'Unpaid' : 'Paid';
            Swal.fire(Object.assign({}, SWAL_COMMON, {
                title: 'Toggle Payment Status?',
                text: 'This will change payment status from ' + from + ' to ' + to + '.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#4F46E5',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Yes, toggle',
                cancelButtonText: 'Cancel'
            })).then(function(result) {
                if (result.isConfirmed) {
                    document.getElementById('toggle-payment-form-' + subId).submit();
                }
            });
        }

        function toggleActive(subId, currentActive) {
            var to = currentActive ? 'inactive' : 'active';
            Swal.fire(Object.assign({}, SWAL_COMMON, {
                title: 'Toggle Active Status?',
                text: 'This will change the active status to ' + to + '.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#4F46E5',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Yes, toggle',
                cancelButtonText: 'Cancel'
            })).then(function(result) {
                if (result.isConfirmed) {
                    document.getElementById('toggle-active-form-' + subId).submit();
                }
            });
        }
    </script>

@endsection
