@extends('layouts.app')

@section('title', 'Invoices')
@section('page-title', 'Invoices')

@section('content')
<div x-data="invoiceModal()">
    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-primary rounded-button flex items-center justify-center shadow-hard flex-shrink-0">
                    <i class="bx bx-receipt text-white text-[28px]"></i>
                </div>
                <div>
                    <p class="text-3xl font-extrabold">Rp {{ number_format($stats['totalRevenue'] ?? 0, 0, ',', '.') }}</p>
                    <p class="text-sm font-medium text-txt-secondary">Total Revenue</p>
                </div>
            </div>
        </div>

        <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-[#F59E0B] rounded-button flex items-center justify-center shadow-hard flex-shrink-0">
                    <i class="bx bx-time-five text-white text-[28px]"></i>
                </div>
                <div>
                    <p class="text-3xl font-extrabold">Rp {{ number_format($stats['pendingAmount'] ?? 0, 0, ',', '.') }}</p>
                    <p class="text-sm font-medium text-txt-secondary">Pending</p>
                </div>
            </div>
        </div>

        <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-[#22C55E] rounded-button flex items-center justify-center shadow-hard flex-shrink-0">
                    <i class="bx bx-check-circle text-white text-[28px]"></i>
                </div>
                <div>
                    <p class="text-3xl font-extrabold">Rp {{ number_format($stats['totalPaid'] ?? 0, 0, ',', '.') }}</p>
                    <p class="text-sm font-medium text-txt-secondary">Paid</p>
                </div>
            </div>
        </div>

        <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-danger rounded-button flex items-center justify-center shadow-hard flex-shrink-0">
                    <i class="bx bx-error text-white text-[28px]"></i>
                </div>
                <div>
                    <p class="text-3xl font-extrabold">Rp {{ number_format($stats['overdueAmount'] ?? 0, 0, ',', '.') }}</p>
                    <p class="text-sm font-medium text-txt-secondary">Overdue</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Toolbar --}}
    <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 mb-8">
        <form method="GET" class="flex flex-col sm:flex-row gap-4 items-center justify-between">
            <div class="flex flex-col sm:flex-row gap-4 w-full sm:w-auto">
                <div class="relative w-full sm:w-72">
                    <i class="bx bx-search absolute left-4 top-1/2 -translate-y-1/2 text-txt-secondary text-xl"></i>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Search invoices..."
                        class="w-full pl-12 pr-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none transition-colors">
                </div>
                <select name="status" class="w-full sm:w-44 px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium text-txt-primary focus:border-primary outline-none transition-colors">
                    <option value="">All Status</option>
                    <option value="pending" {{ $statusFilter === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="paid" {{ $statusFilter === 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="overdue" {{ $statusFilter === 'overdue' ? 'selected' : '' }}>Overdue</option>
                    <option value="cancelled" {{ $statusFilter === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <div class="flex gap-3 w-full sm:w-auto">
                <button type="submit" class="px-5 py-3 bg-surface text-txt-primary font-bold text-sm rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 active:translate-y-1 active:shadow-hard-pressed transition-all duration-200 ease-out w-full sm:w-auto">
                    <i class="bx bx-filter mr-1"></i> Filter
                </button>
                <button type="button" @click="openCreate()" class="px-6 py-3 bg-primary text-white font-bold text-sm rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 active:translate-y-1 active:shadow-hard-pressed transition-all duration-200 ease-out flex items-center gap-2">
                    <i class="bx bx-plus text-lg"></i>
                    New Invoice
                </button>
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b-4 border-border-dark bg-gray-50">
                        <th class="text-left px-6 py-4 font-extrabold whitespace-nowrap">
                            <a href="{{ request()->fullUrlWithQuery(array_merge(request()->query(), ['sort' => 'invoice_number', 'direction' => $sortField === 'invoice_number' && $sortDirection === 'asc' ? 'desc' : 'asc'])) }}" class="flex items-center gap-1 hover:text-primary transition-colors">
                                Invoice #
                                @if($sortField === 'invoice_number')
                                    <i class="bx bx-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }} text-xs"></i>
                                @endif
                            </a>
                        </th>
                        <th class="text-left px-6 py-4 font-extrabold">Client</th>
                        <th class="text-right px-6 py-4 font-extrabold whitespace-nowrap">
                            <a href="{{ request()->fullUrlWithQuery(array_merge(request()->query(), ['sort' => 'total', 'direction' => $sortField === 'total' && $sortDirection === 'asc' ? 'desc' : 'asc'])) }}" class="flex items-center gap-1 hover:text-primary transition-colors justify-end">
                                Amount
                                @if($sortField === 'total')
                                    <i class="bx bx-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }} text-xs"></i>
                                @endif
                            </a>
                        </th>
                        <th class="text-left px-6 py-4 font-extrabold whitespace-nowrap">
                            <a href="{{ request()->fullUrlWithQuery(array_merge(request()->query(), ['sort' => 'status', 'direction' => $sortField === 'status' && $sortDirection === 'asc' ? 'desc' : 'asc'])) }}" class="flex items-center gap-1 hover:text-primary transition-colors">
                                Status
                                @if($sortField === 'status')
                                    <i class="bx bx-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }} text-xs"></i>
                                @endif
                            </a>
                        </th>
                        <th class="text-left px-6 py-4 font-extrabold whitespace-nowrap">
                            <a href="{{ request()->fullUrlWithQuery(array_merge(request()->query(), ['sort' => 'due_date', 'direction' => $sortField === 'due_date' && $sortDirection === 'asc' ? 'desc' : 'asc'])) }}" class="flex items-center gap-1 hover:text-primary transition-colors">
                                Due Date
                                @if($sortField === 'due_date')
                                    <i class="bx bx-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }} text-xs"></i>
                                @endif
                            </a>
                        </th>
                        <th class="text-right px-6 py-4 font-extrabold">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($invoices as $invoice)
                        <tr class="border-b-2 border-gray-100 hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 font-semibold whitespace-nowrap">#{{ $invoice->invoice_number }}</td>
                            <td class="px-6 py-4 text-txt-secondary">{{ $invoice->client?->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-right font-extrabold whitespace-nowrap">Rp {{ number_format($invoice->total, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $badgeVariant = match($invoice->status) {
                                        'paid' => 'success',
                                        'pending' => 'warning',
                                        'overdue' => 'danger',
                                        default => 'default',
                                    };
                                @endphp
                                <x-badge variant="{{ $badgeVariant }}">{{ ucfirst($invoice->status) }}</x-badge>
                            </td>
                            <td class="px-6 py-4 text-txt-secondary whitespace-nowrap">{{ $invoice->due_date?->format('d M Y') ?? '-' }}</td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-1 flex-wrap">
                                    <a href="{{ route('invoices.pdf', $invoice) }}" target="_blank" class="p-2 text-txt-secondary hover:text-primary transition-colors" title="Download PDF">
                                        <i class="bx bx-download text-lg"></i>
                                    </a>
                                    <button type="button" @click='openEdit(@json($invoice))' class="p-2 text-txt-secondary hover:text-primary transition-colors" title="Edit">
                                        <i class="bx bx-edit text-lg"></i>
                                    </button>
                                    <form action="{{ route('invoices.mark-paid', $invoice) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="p-2 text-txt-secondary hover:text-[#22C55E] transition-colors" title="Mark as Paid">
                                            <i class="bx bx-check text-lg"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('invoices.mark-overdue', $invoice) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="p-2 text-txt-secondary hover:text-[#F59E0B] transition-colors" title="Mark as Overdue">
                                            <i class="bx bx-alarm text-lg"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('invoices.destroy', $invoice) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this invoice?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-txt-secondary hover:text-danger transition-colors" title="Delete">
                                            <i class="bx bx-trash text-lg"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <i class="bx bx-receipt text-5xl text-txt-secondary"></i>
                                <p class="text-txt-secondary font-medium mt-3">No invoices found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t-4 border-border-dark">
            {{ $invoices->links() }}
        </div>
    </div>

    {{-- Invoice Modal --}}
    <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" style="display: none;">
        <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="scale-95 opacity-0" x-transition:enter-end="scale-100 opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="scale-100 opacity-0" x-transition:leave-end="scale-95 opacity-0" @click.outside="close()" class="bg-surface border-4 border-border-dark rounded-modal shadow-hard w-full max-w-lg animate-scale-in" style="display: none;">
            <div class="flex items-center justify-between px-6 py-4 border-b-4 border-border-dark">
                <h3 class="text-lg font-extrabold" x-text="editingInvoice ? 'Edit Invoice' : 'New Invoice'"></h3>
                <button type="button" @click="close()" class="text-2xl text-txt-secondary hover:text-danger transition-colors">
                    <i class="bx bx-x"></i>
                </button>
            </div>
            <form id="invoice-form" method="POST" :action="formAction()" class="p-6 space-y-4">
                @csrf
                <input type="hidden" name="_method" x-bind:value="editingInvoice ? 'PUT' : ''">
                <div>
                    <label for="invoice-client_id" class="block text-sm font-semibold text-txt-primary mb-1.5">Client</label>
                    <select name="client_id" id="invoice-client_id" required class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium text-txt-primary focus:border-primary outline-none transition-colors">
                        <option value="">Select Client</option>
                        @foreach(\App\Models\Client::all() as $client)
                            <option value="{{ $client->id }}">{{ $client->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="invoice-status" class="block text-sm font-semibold text-txt-primary mb-1.5">Status</label>
                    <select name="status" id="invoice-status" required class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium text-txt-primary focus:border-primary outline-none transition-colors">
                        <option value="pending">Pending</option>
                        <option value="paid">Paid</option>
                        <option value="overdue">Overdue</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <div>
                    <label for="invoice-due_date" class="block text-sm font-semibold text-txt-primary mb-1.5">Due Date</label>
                    <input type="date" name="due_date" id="invoice-due_date" required class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium text-txt-primary focus:border-primary outline-none transition-colors">
                </div>
                <div>
                    <label for="invoice-notes" class="block text-sm font-semibold text-txt-primary mb-1.5">Notes</label>
                    <textarea name="notes" id="invoice-notes" rows="3" class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none resize-none"></textarea>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" @click="close()" class="px-6 py-3 font-bold text-sm rounded-button border-4 border-border-dark bg-surface shadow-hard hover:-translate-y-0.5 transition-all duration-200 ease-out">
                        Cancel
                    </button>
                    <button type="submit" class="px-6 py-3 bg-primary text-white font-bold text-sm rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 active:translate-y-1 active:shadow-hard-pressed transition-all duration-200 ease-out">
                        Save Invoice
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function invoiceModal() {
    return {
        open: false,
        editingInvoice: null,
        init() {
            this.resetForm();
        },
        openCreate() {
            this.editingInvoice = null;
            this.open = true;
            this.resetForm();
        },
        openEdit(invoice) {
            this.editingInvoice = invoice;
            this.open = true;
            this.populateForm(invoice);
        },
        close() {
            this.open = false;
            this.editingInvoice = null;
            this.resetForm();
        },
        formAction() {
            return this.editingInvoice ? '/invoices/' + this.editingInvoice.id : '{{ route("invoices.store") }}';
        },
        resetForm() {
            const clientField = document.getElementById('invoice-client_id');
            if (clientField) clientField.value = '';
            const statusField = document.getElementById('invoice-status');
            if (statusField) statusField.value = 'pending';
            const dueDateField = document.getElementById('invoice-due_date');
            if (dueDateField) dueDateField.value = '';
            const notesField = document.getElementById('invoice-notes');
            if (notesField) notesField.value = '';
        },
        populateForm(invoice) {
            const clientField = document.getElementById('invoice-client_id');
            if (clientField) clientField.value = invoice.client_id || '';
            const statusField = document.getElementById('invoice-status');
            if (statusField) statusField.value = invoice.status || 'pending';
            const dueDateField = document.getElementById('invoice-due_date');
            if (dueDateField) dueDateField.value = invoice.due_date || '';
            const notesField = document.getElementById('invoice-notes');
            if (notesField) notesField.value = invoice.notes || '';
        }
    }
}
</script>
@endsection