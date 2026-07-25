@extends('layouts.app')

@section('title', 'Invoices')
@section('page-title', 'Invoices')

@section('content')
    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-primary rounded-button flex items-center justify-center shadow-hard flex-shrink-0">
                    <i class="bx bx-receipt text-white text-[28px]"></i>
                </div>
                <div>
                    <p class="text-3xl font-extrabold" x-text="$wire.totalInvoices ?? 0">0</p>
                    <p class="text-sm font-medium text-txt-secondary">Total</p>
                </div>
            </div>
        </div>

        <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-[#F59E0B] rounded-button flex items-center justify-center shadow-hard flex-shrink-0">
                    <i class="bx bx-time-five text-white text-[28px]"></i>
                </div>
                <div>
                    <p class="text-3xl font-extrabold" x-text="$wire.pendingInvoices ?? 0">0</p>
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
                    <p class="text-3xl font-extrabold" x-text="$wire.paidInvoices ?? 0">0</p>
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
                    <p class="text-3xl font-extrabold" x-text="$wire.overdueInvoices ?? 0">0</p>
                    <p class="text-sm font-medium text-txt-secondary">Overdue</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Toolbar --}}
    <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 mb-8">
        <div class="flex flex-col sm:flex-row gap-4 items-center justify-between">
            <div class="flex flex-col sm:flex-row gap-4 w-full sm:w-auto">
                <div class="relative w-full sm:w-72">
                    <i class="bx bx-search absolute left-4 top-1/2 -translate-y-1/2 text-txt-secondary text-xl"></i>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search invoices..."
                        class="w-full pl-12 pr-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none transition-colors">
                </div>
                <select wire:model.live="statusFilter"
                    class="w-full sm:w-44 px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium text-txt-primary focus:border-primary outline-none transition-colors">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="paid">Paid</option>
                    <option value="overdue">Overdue</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
            <x-button @click="$dispatch('open-modal', { id: 'invoice-form' })" variant="primary" size="md" class="w-full sm:w-auto">
                <i class="bx bx-plus"></i>
                Create Invoice
            </x-button>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b-4 border-border-dark bg-gray-50">
                        <th class="text-left px-6 py-4 font-extrabold">Invoice #</th>
                        <th class="text-left px-6 py-4 font-extrabold">Client</th>
                        <th class="text-right px-6 py-4 font-extrabold">Amount</th>
                        <th class="text-left px-6 py-4 font-extrabold">Status</th>
                        <th class="text-left px-6 py-4 font-extrabold">Due Date</th>
                        <th class="text-right px-6 py-4 font-extrabold">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($invoices ?? [] as $invoice)
                        <tr class="border-b-2 border-gray-100 hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 font-semibold">#{{ $invoice->invoice_number }}</td>
                            <td class="px-6 py-4 text-txt-secondary">{{ $invoice->client?->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-right font-extrabold">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</td>
                            <td class="px-6 py-4">
                                @php
                                    $variant = match($invoice->status) {
                                        'paid' => 'success',
                                        'pending' => 'warning',
                                        'overdue' => 'danger',
                                        default => 'default',
                                    };
                                @endphp
                                <x-badge variant="{{ $variant }}">{{ ucfirst($invoice->status) }}</x-badge>
                            </td>
                            <td class="px-6 py-4 text-txt-secondary">{{ $invoice->due_date?->format('d M Y') ?? '-' }}</td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <x-button variant="ghost" size="sm" wire:click="downloadPdf({{ $invoice->id }})">
                                        <i class="bx bx-download text-base"></i>
                                    </x-button>
                                    <x-button variant="ghost" size="sm" wire:click="edit({{ $invoice->id }})">
                                        <i class="bx bx-edit text-base"></i>
                                    </x-button>
                                    <x-button variant="ghost" size="sm" wire:click="confirmDelete({{ $invoice->id }})">
                                        <i class="bx bx-trash text-base text-danger"></i>
                                    </x-button>
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
        @if (method_exists($invoices ?? [], 'links'))
            <div class="px-6 py-4 border-t-4 border-border-dark">
                {{ $invoices->links() }}
            </div>
        @endif
    </div>

    {{-- Modal Form --}}
    <x-modal id="invoice-form" title="Invoice Form" maxWidth="max-w-3xl">
        <form wire:submit="save" class="space-y-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <x-input label="Invoice Number" name="invoice_number" placeholder="INV-001" wire:model="form.invoice_number" />
                <div class="space-y-1.5">
                    <label class="block text-sm font-semibold text-txt-primary">Client</label>
                    <select wire:model="form.client_id"
                        class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium text-txt-primary focus:border-primary outline-none transition-colors">
                        <option value="">Select Client</option>
                        @foreach ($clients ?? [] as $client)
                            <option value="{{ $client->id }}">{{ $client->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <x-input label="Issue Date" name="issue_date" type="date" wire:model="form.issue_date" />
                <x-input label="Due Date" name="due_date" type="date" wire:model="form.due_date" />
            </div>

            {{-- Invoice Items --}}
            <div x-data="{
                items: @entangle('form.items'),
                addItem() {
                    this.items.push({ name: '', qty: 1, price: 0 });
                    $wire.set('form.items', this.items);
                },
                removeItem(index) {
                    this.items.splice(index, 1);
                    $wire.set('form.items', this.items);
                },
                get subtotal() {
                    return this.items.reduce((sum, item) => sum + (item.qty * item.price), 0);
                }
            }">
                <div class="flex items-center justify-between mb-3">
                    <label class="text-sm font-semibold text-txt-primary">Invoice Items</label>
                    <x-button variant="secondary" size="sm" type="button" @click="addItem()">
                        <i class="bx bx-plus"></i>
                        Add Item
                    </x-button>
                </div>

                <div class="space-y-3">
                    <template x-for="(item, index) in items" :key="index">
                        <div class="flex items-start gap-3 p-4 rounded-button border-4 border-border-dark bg-gray-50">
                            <div class="flex-1 space-y-1.5">
                                <input type="text" x-model="item.name" placeholder="Item name"
                                    class="w-full px-3 py-2 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none transition-colors">
                            </div>
                            <div class="w-20 space-y-1.5">
                                <input type="number" x-model="item.qty" min="1" placeholder="Qty"
                                    class="w-full px-3 py-2 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none transition-colors text-center">
                            </div>
                            <div class="w-28 space-y-1.5">
                                <input type="number" x-model="item.price" min="0" placeholder="Price"
                                    class="w-full px-3 py-2 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none transition-colors text-right">
                            </div>
                            <div class="w-24 pt-1.5 text-right font-extrabold text-sm" x-text="'Rp ' + (item.qty * item.price).toLocaleString('id-ID')"></div>
                            <button type="button" @click="removeItem(index)" class="pt-1.5 text-danger hover:text-red-700 transition-colors">
                                <i class="bx bx-x text-xl"></i>
                            </button>
                        </div>
                    </template>
                </div>

                @if (!empty($form->items))
                    <div class="flex justify-end mt-4 pt-4 border-t-4 border-border-dark">
                        <div class="text-right">
                            <p class="text-sm font-medium text-txt-secondary">Subtotal</p>
                            <p class="text-2xl font-extrabold" x-text="'Rp ' + subtotal.toLocaleString('id-ID')">Rp 0</p>
                        </div>
                    </div>
                @endif
            </div>

            <x-input label="Notes (optional)" name="notes" placeholder="Payment notes..." wire:model="form.notes" />

            <div class="flex justify-end gap-3 pt-2">
                <x-button variant="secondary" type="button" @click="$dispatch('close-modal', { id: 'invoice-form' })">
                    Cancel
                </x-button>
                <x-button variant="primary" type="submit">
                    <i class="bx bx-check"></i>
                    Save Invoice
                </x-button>
            </div>
        </form>
    </x-modal>
@endsection
