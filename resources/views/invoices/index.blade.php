@extends('layouts.app')

@section('title', 'Invoices')
@section('page-title', 'Invoices')

@section('content')
<div x-data="invoiceApp()" x-init="init()">

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 hover:-translate-y-1.5 hover:shadow-hard-hover transition-all duration-200">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-primary rounded-button flex items-center justify-center shadow-hard flex-shrink-0">
                    <i class="bx bx-receipt text-white text-3xl"></i>
                </div>
                <div>
                    <p class="text-2xl font-extrabold">Rp {{ number_format($stats['totalRevenue'] ?? 0, 0, ',', '.') }}</p>
                    <p class="text-sm font-medium text-txt-secondary">Total Revenue</p>
                </div>
            </div>
        </div>
        <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 hover:-translate-y-1.5 hover:shadow-hard-hover transition-all duration-200">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-[#F59E0B] rounded-button flex items-center justify-center shadow-hard flex-shrink-0">
                    <i class="bx bx-time-five text-white text-3xl"></i>
                </div>
                <div>
                    <p class="text-2xl font-extrabold">Rp {{ number_format($stats['totalHutang'] ?? 0, 0, ',', '.') }}</p>
                    <p class="text-sm font-medium text-txt-secondary">Belum Lunas</p>
                </div>
            </div>
        </div>
        <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 hover:-translate-y-1.5 hover:shadow-hard-hover transition-all duration-200">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-[#22C55E] rounded-button flex items-center justify-center shadow-hard flex-shrink-0">
                    <i class="bx bx-check-circle text-white text-3xl"></i>
                </div>
                <div>
                    <p class="text-2xl font-extrabold">Rp {{ number_format($stats['totalLunas'] ?? 0, 0, ',', '.') }}</p>
                    <p class="text-sm font-medium text-txt-secondary">Lunas</p>
                </div>
            </div>
        </div>
        <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 hover:-translate-y-1.5 hover:shadow-hard-hover transition-all duration-200">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-danger rounded-button flex items-center justify-center shadow-hard flex-shrink-0">
                    <i class="bx bx-error-circle text-white text-3xl"></i>
                </div>
                <div>
                    <p class="text-2xl font-extrabold">Rp {{ number_format($stats['totalPiutang'] ?? 0, 0, ',', '.') }}</p>
                    <p class="text-sm font-medium text-txt-secondary">Sisa Piutang</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Toolbar --}}
    <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-5 mb-6">
        <form method="GET" class="flex flex-col sm:flex-row gap-3 items-center justify-between">
            <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                <div class="relative w-full sm:w-72">
                    <i class="bx bx-search absolute left-4 top-1/2 -translate-y-1/2 text-txt-secondary text-xl"></i>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari invoice atau client..."
                        class="w-full pl-11 pr-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none transition-colors">
                </div>
                <select name="status" class="w-full sm:w-40 px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium focus:border-primary outline-none transition-colors">
                    <option value="">Semua Status</option>
                    <option value="hutang" {{ $statusFilter === 'hutang' ? 'selected' : '' }}>Hutang</option>
                    <option value="lunas" {{ $statusFilter === 'lunas' ? 'selected' : '' }}>Lunas</option>
                </select>
                <button type="submit" class="px-5 py-3 bg-surface font-bold text-sm rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 active:translate-y-1 transition-all duration-200 whitespace-nowrap">
                    <i class="bx bx-filter-alt mr-1"></i> Filter
                </button>
            </div>
            <button type="button" @click="openCreate()"
                class="w-full sm:w-auto px-6 py-3 bg-primary text-white font-bold text-sm rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 active:translate-y-1 active:shadow-hard-pressed transition-all duration-200 flex items-center justify-center gap-2">
                <i class="bx bx-plus text-xl"></i> New Invoice
            </button>
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard overflow-hidden mb-8">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b-4 border-border-dark bg-gray-50">
                        <th class="text-left px-5 py-4 font-extrabold whitespace-nowrap">
                            <a href="{{ request()->fullUrlWithQuery(['sort'=>'invoice_number','direction'=>$sortField==='invoice_number'&&$sortDirection==='asc'?'desc':'asc']) }}" class="flex items-center gap-1 hover:text-primary transition-colors">
                                Invoice # @if($sortField==='invoice_number')<i class="bx bx-chevron-{{ $sortDirection==='asc'?'up':'down' }} text-xs"></i>@endif
                            </a>
                        </th>
                        <th class="text-left px-5 py-4 font-extrabold">Client</th>
                        <th class="text-left px-5 py-4 font-extrabold whitespace-nowrap">Items</th>
                        <th class="text-right px-5 py-4 font-extrabold whitespace-nowrap">
                            <a href="{{ request()->fullUrlWithQuery(['sort'=>'total','direction'=>$sortField==='total'&&$sortDirection==='asc'?'desc':'asc']) }}" class="flex items-center gap-1 hover:text-primary transition-colors justify-end">
                                Total @if($sortField==='total')<i class="bx bx-chevron-{{ $sortDirection==='asc'?'up':'down' }} text-xs"></i>@endif
                            </a>
                        </th>
                        <th class="text-right px-5 py-4 font-extrabold whitespace-nowrap">Terbayar</th>
                        <th class="text-right px-5 py-4 font-extrabold whitespace-nowrap">Sisa</th>
                        <th class="text-left px-5 py-4 font-extrabold whitespace-nowrap">
                            <a href="{{ request()->fullUrlWithQuery(['sort'=>'status','direction'=>$sortField==='status'&&$sortDirection==='asc'?'desc':'asc']) }}" class="flex items-center gap-1 hover:text-primary transition-colors">
                                Status @if($sortField==='status')<i class="bx bx-chevron-{{ $sortDirection==='asc'?'up':'down' }} text-xs"></i>@endif
                            </a>
                        </th>
                        <th class="text-left px-5 py-4 font-extrabold whitespace-nowrap">
                            <a href="{{ request()->fullUrlWithQuery(['sort'=>'due_date','direction'=>$sortField==='due_date'&&$sortDirection==='asc'?'desc':'asc']) }}" class="flex items-center gap-1 hover:text-primary transition-colors">
                                Due Date @if($sortField==='due_date')<i class="bx bx-chevron-{{ $sortDirection==='asc'?'up':'down' }} text-xs"></i>@endif
                            </a>
                        </th>
                        <th class="text-right px-5 py-4 font-extrabold">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($invoices as $invoice)
                        <tr class="border-b-2 border-gray-100 hover:bg-gray-50 transition-colors">
                            <td class="px-5 py-4 font-bold whitespace-nowrap text-primary">{{ $invoice->invoice_number }}</td>
                            <td class="px-5 py-4">
                                <div class="font-semibold">{{ $invoice->client?->name ?? 'N/A' }}</div>
                                @if($invoice->client?->company)
                                    <div class="text-xs text-txt-secondary">{{ $invoice->client->company }}</div>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-txt-secondary text-xs">
                                {{ count($invoice->items ?? []) }} item
                            </td>
                            <td class="px-5 py-4 text-right font-extrabold whitespace-nowrap">Rp {{ number_format($invoice->total, 0, ',', '.') }}</td>
                            <td class="px-5 py-4 text-right font-bold whitespace-nowrap text-[#22C55E]">Rp {{ number_format($invoice->paid_amount, 0, ',', '.') }}</td>
                            <td class="px-5 py-4 text-right font-bold whitespace-nowrap {{ $invoice->remainingAmount() > 0 ? 'text-danger' : 'text-[#22C55E]' }}">
                                Rp {{ number_format($invoice->remainingAmount(), 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                @php $bv = match($invoice->status) { 'lunas' => 'success', 'hutang' => 'warning', default => 'default' }; @endphp
                                <x-badge variant="{{ $bv }}">{{ ucfirst($invoice->status) }}</x-badge>
                            </td>
                            <td class="px-5 py-4 text-txt-secondary whitespace-nowrap text-xs">
                                @if($invoice->due_date)
                                    <span class="{{ $invoice->due_date->isPast() && $invoice->status !== 'lunas' ? 'text-danger font-bold' : '' }}">
                                        {{ $invoice->due_date->format('d M Y') }}
                                    </span>
                                @else -
                                @endif
                            </td>
                            <td class="px-5 py-4 text-right">
                                <div class="flex items-center justify-end gap-0.5">
                                    {{-- Preview PDF --}}
                                    <a href="{{ route('invoices.preview', $invoice) }}" target="_blank"
                                        class="p-2 text-txt-secondary hover:text-primary transition-colors" title="Preview PDF">
                                        <i class="bx bx-show text-lg"></i>
                                    </a>
                                    {{-- Download PDF --}}
                                    <a href="{{ route('invoices.pdf', $invoice) }}"
                                        class="p-2 text-txt-secondary hover:text-primary transition-colors" title="Download PDF">
                                        <i class="bx bx-download text-lg"></i>
                                    </a>
                                    {{-- Laporan --}}
                                    <a href="{{ route('invoices.report', $invoice) }}"
                                        class="p-2 text-txt-secondary hover:text-[#F59E0B] transition-colors" title="Laporan Pembayaran">
                                        <i class="bx bx-bar-chart-alt-2 text-lg"></i>
                                    </a>
                                    {{-- WhatsApp --}}
                                    <a href="{{ route('invoices.send-wa', $invoice) }}" target="_blank"
                                        class="p-2 text-txt-secondary hover:text-[#25D366] transition-colors" title="Kirim WA">
                                        <i class="bx bxl-whatsapp text-lg"></i>
                                    </a>
                                    {{-- Edit Bayar --}}
                                    <button type="button" @click='openEditBayar(@json($invoice))'
                                        class="p-2 text-txt-secondary hover:text-[#22C55E] transition-colors" title="Update Pembayaran">
                                        <i class="bx bx-wallet text-lg"></i>
                                    </button>
                                    {{-- Edit Invoice --}}
                                    <button type="button" @click='openEdit(@json($invoice))'
                                        class="p-2 text-txt-secondary hover:text-primary transition-colors" title="Edit Invoice">
                                        <i class="bx bx-edit text-lg"></i>
                                    </button>
                                    {{-- Hapus --}}
                                    <form id="del-{{ $invoice->id }}" action="{{ route('invoices.destroy', $invoice) }}" method="POST" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="button" @click="confirmDelete('del-{{ $invoice->id }}', '{{ $invoice->invoice_number }}')"
                                            class="p-2 text-txt-secondary hover:text-danger transition-colors" title="Hapus">
                                            <i class="bx bx-trash text-lg"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-16 text-center">
                                <i class="bx bx-receipt text-5xl text-txt-secondary mb-3 block"></i>
                                <p class="text-txt-secondary font-semibold">Belum ada invoice</p>
                                <p class="text-txt-secondary text-sm mt-1">Klik "New Invoice" untuk membuat invoice pertama.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($invoices->hasPages())
            <div class="px-5 py-4 border-t-4 border-border-dark">
                {{ $invoices->links() }}
            </div>
        @endif
    </div>


    {{-- ============================================================ --}}
    {{-- MODAL: Create / Edit Invoice --}}
    {{-- ============================================================ --}}
    <div x-show="showModal"
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-start justify-center bg-black/50 p-4 overflow-y-auto"
        style="display:none;">
        <div x-show="showModal"
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="scale-95 opacity-0" x-transition:enter-end="scale-100 opacity-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="scale-100 opacity-0" x-transition:leave-end="scale-95 opacity-0"
            @click.outside="closeModal()"
            class="bg-surface border-4 border-border-dark rounded-modal shadow-hard w-full max-w-2xl my-6"
            style="display:none;">

            {{-- Modal Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b-4 border-border-dark">
                <h3 class="text-lg font-extrabold" x-text="editingInvoice ? 'Edit Invoice' : 'New Invoice'"></h3>
                <button @click="closeModal()" class="text-2xl text-txt-secondary hover:text-danger transition-colors">
                    <i class="bx bx-x"></i>
                </button>
            </div>

            {{-- Form --}}
            <form method="POST" :action="editingInvoice ? '/invoices/'+editingInvoice.id : '{{ route('invoices.store') }}'" class="p-6 space-y-5">
                @csrf
                <input type="hidden" name="_method" :value="editingInvoice ? 'PUT' : 'POST'">

                {{-- Row: Client + Due Date --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold mb-1.5">Client <span class="text-danger">*</span></label>
                        <select name="client_id" x-model="form.client_id" required
                            class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium focus:border-primary outline-none transition-colors">
                            <option value="">Pilih Client</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}">{{ $client->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold mb-1.5">Jatuh Tempo <span class="text-danger">*</span></label>
                        <input type="date" name="due_date" x-model="form.due_date" required
                            class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium focus:border-primary outline-none transition-colors">
                    </div>
                </div>

                {{-- Line Items --}}
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="text-sm font-bold">Rincian Biaya <span class="text-danger">*</span></label>
                        <button type="button" @click="addItem()"
                            class="flex items-center gap-1 text-xs font-bold px-3 py-1.5 bg-primary text-white rounded-button border-2 border-border-dark shadow-hard hover:-translate-y-0.5 transition-all duration-200">
                            <i class="bx bx-plus"></i> Tambah Item
                        </button>
                    </div>

                    {{-- Item rows --}}
                    <div class="border-4 border-border-dark rounded-card overflow-hidden">
                        {{-- Header --}}
                        <div class="grid grid-cols-12 gap-0 bg-gray-50 border-b-4 border-border-dark px-3 py-2">
                            <div class="col-span-6 text-xs font-extrabold uppercase">Deskripsi</div>
                            <div class="col-span-2 text-xs font-extrabold uppercase text-center">Qty</div>
                            <div class="col-span-3 text-xs font-extrabold uppercase text-right">Harga (Rp)</div>
                            <div class="col-span-1"></div>
                        </div>
                        {{-- Rows --}}
                        <template x-for="(item, index) in form.items" :key="index">
                            <div class="grid grid-cols-12 gap-0 border-b-2 border-gray-100 px-3 py-2 items-center hover:bg-gray-50">
                                <div class="col-span-6 pr-2">
                                    <input type="text" :name="'items['+index+'][description]'" x-model="item.description"
                                        placeholder="cth: Biaya Pengembangan, Hosting, Domain..."
                                        class="w-full px-3 py-2 rounded-input border-2 border-gray-300 bg-surface text-sm focus:border-primary outline-none transition-colors"
                                        required>
                                </div>
                                <div class="col-span-2 px-1">
                                    <input type="number" :name="'items['+index+'][quantity]'" x-model.number="item.quantity"
                                        min="0.01" step="0.01" placeholder="1"
                                        class="w-full px-2 py-2 rounded-input border-2 border-gray-300 bg-surface text-sm text-center focus:border-primary outline-none transition-colors"
                                        required>
                                </div>
                                <div class="col-span-3 pl-1">
                                    <input type="number" :name="'items['+index+'][rate]'" x-model.number="item.rate"
                                        min="0" step="1000" placeholder="200000"
                                        class="w-full px-3 py-2 rounded-input border-2 border-gray-300 bg-surface text-sm text-right focus:border-primary outline-none transition-colors"
                                        required>
                                </div>
                                <div class="col-span-1 flex justify-center">
                                    <button type="button" @click="removeItem(index)"
                                        x-show="form.items.length > 1"
                                        class="p-1 text-txt-secondary hover:text-danger transition-colors">
                                        <i class="bx bx-x-circle text-lg"></i>
                                    </button>
                                </div>
                            </div>
                        </template>
                        {{-- Subtotal preview --}}
                        <div class="px-4 py-3 bg-gray-50 border-t-2 border-gray-200">
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-txt-secondary font-medium">Subtotal</span>
                                <span class="font-extrabold text-txt-primary" x-text="'Rp ' + formatRp(subtotal())"></span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Row: Diskon + PPN --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold mb-1.5">Diskon (%)</label>
                        <input type="number" name="discount_percent" x-model.number="form.discount_percent"
                            min="0" max="100" step="0.01" placeholder="0"
                            class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium focus:border-primary outline-none transition-colors">
                    </div>
                    <div>
                        <label class="block text-sm font-bold mb-1.5">PPN (%)</label>
                        <input type="number" name="tax_percent" x-model.number="form.tax_percent"
                            min="0" max="100" step="0.01" placeholder="0"
                            class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium focus:border-primary outline-none transition-colors">
                    </div>
                </div>

                {{-- Total preview --}}
                <div class="bg-gray-50 border-4 border-border-dark rounded-card px-5 py-4 space-y-1">
                    <div class="flex justify-between text-sm text-txt-secondary">
                        <span>Subtotal</span>
                        <span x-text="'Rp ' + formatRp(subtotal())"></span>
                    </div>
                    <template x-if="form.discount_percent > 0">
                        <div class="flex justify-between text-sm text-[#22C55E]">
                            <span x-text="'Diskon (' + form.discount_percent + '%)'"></span>
                            <span x-text="'- Rp ' + formatRp(discountAmount())"></span>
                        </div>
                    </template>
                    <template x-if="form.tax_percent > 0">
                        <div class="flex justify-between text-sm text-[#F59E0B]">
                            <span x-text="'PPN (' + form.tax_percent + '%)'"></span>
                            <span x-text="'+ Rp ' + formatRp(taxAmount())"></span>
                        </div>
                    </template>
                    <div class="flex justify-between font-extrabold text-base border-t-2 border-border-dark pt-2 mt-2">
                        <span>Total</span>
                        <span x-text="'Rp ' + formatRp(grandTotal())"></span>
                    </div>
                </div>

                {{-- Uang Muka --}}
                <div>
                    <label class="block text-sm font-bold mb-1.5">Uang Muka / Terbayar (Rp)</label>
                    <input type="number" name="paid_amount" x-model.number="form.paid_amount"
                        min="0" step="1000" placeholder="0"
                        class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium focus:border-primary outline-none transition-colors">
                    <p class="text-xs text-txt-secondary mt-1">Isi 0 jika belum ada pembayaran.</p>
                </div>

                {{-- Notes --}}
                <div>
                    <label class="block text-sm font-bold mb-1.5">Catatan</label>
                    <textarea name="notes" x-model="form.notes" rows="3" placeholder="Catatan tambahan untuk client..."
                        class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none resize-none transition-colors"></textarea>
                </div>

                {{-- Actions --}}
                <div class="flex justify-end gap-3 pt-2 border-t-4 border-border-dark">
                    <button type="button" @click="closeModal()"
                        class="px-6 py-3 font-bold text-sm rounded-button border-4 border-border-dark bg-surface shadow-hard hover:-translate-y-0.5 transition-all duration-200">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-6 py-3 bg-primary text-white font-bold text-sm rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 active:translate-y-1 active:shadow-hard-pressed transition-all duration-200">
                        <i class="bx bx-save mr-1"></i> Simpan Invoice
                    </button>
                </div>
            </form>
        </div>
    </div>


    {{-- ============================================================ --}}
    {{-- MODAL: Edit Pembayaran --}}
    {{-- ============================================================ --}}
    <div x-show="showBayarModal"
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
        style="display:none;">
        <div x-show="showBayarModal"
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="scale-95 opacity-0" x-transition:enter-end="scale-100 opacity-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="scale-100 opacity-0" x-transition:leave-end="scale-95 opacity-0"
            @click.outside="showBayarModal = false"
            class="bg-surface border-4 border-border-dark rounded-modal shadow-hard w-full max-w-md"
            style="display:none;">

            <div class="flex items-center justify-between px-6 py-4 border-b-4 border-border-dark">
                <h3 class="text-lg font-extrabold">Update Pembayaran</h3>
                <button @click="showBayarModal = false" class="text-2xl text-txt-secondary hover:text-danger transition-colors">
                    <i class="bx bx-x"></i>
                </button>
            </div>

            <form method="POST" :action="'/invoices/'+bayarInvoice.id+'/update-payment'" class="p-6 space-y-4">
                @csrf @method('PATCH')

                <div class="bg-gray-50 border-4 border-border-dark rounded-card p-4 space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-txt-secondary">Invoice</span>
                        <span class="font-bold" x-text="bayarInvoice.invoice_number"></span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-txt-secondary">Client</span>
                        <span class="font-bold" x-text="bayarInvoice.client ? bayarInvoice.client.name : 'N/A'"></span>
                    </div>
                    <div class="flex justify-between text-sm border-t-2 border-gray-200 pt-2">
                        <span class="text-txt-secondary">Total Tagihan</span>
                        <span class="font-extrabold" x-text="'Rp ' + formatRp(bayarInvoice.total)"></span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-txt-secondary">Sudah Dibayar</span>
                        <span class="font-bold text-[#22C55E]" x-text="'Rp ' + formatRp(bayarInvoice.paid_amount)"></span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-txt-secondary">Sisa</span>
                        <span class="font-bold text-danger" x-text="'Rp ' + formatRp(parseFloat(bayarInvoice.total) - parseFloat(bayarInvoice.paid_amount))"></span>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold mb-1.5">Total Dibayar (Rp) <span class="text-danger">*</span></label>
                    <input type="number" name="paid_amount" x-model.number="bayarInvoice.paid_amount"
                        min="0" step="1000" required
                        class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium focus:border-primary outline-none transition-colors">
                    <p class="text-xs text-txt-secondary mt-1">Masukkan total kumulatif yang sudah dibayar.</p>
                </div>

                <div class="flex justify-end gap-3 pt-2 border-t-4 border-border-dark">
                    <button type="button" @click="showBayarModal = false"
                        class="px-6 py-3 font-bold text-sm rounded-button border-4 border-border-dark bg-surface shadow-hard hover:-translate-y-0.5 transition-all duration-200">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-6 py-3 bg-[#22C55E] text-white font-bold text-sm rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 active:translate-y-1 active:shadow-hard-pressed transition-all duration-200">
                        <i class="bx bx-check mr-1"></i> Simpan Pembayaran
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>{{-- end x-data --}}

<script>
function invoiceApp() {
    return {
        showModal: false,
        showBayarModal: false,
        editingInvoice: null,
        bayarInvoice: { id: '', invoice_number: '', client: null, total: 0, paid_amount: 0 },
        form: {
            client_id: '',
            due_date: '',
            items: [{ description: '', quantity: 1, rate: 0 }],
            discount_percent: 0,
            tax_percent: 0,
            paid_amount: 0,
            notes: '',
        },

        init() {
            // nothing needed on init
        },

        openCreate() {
            this.editingInvoice = null;
            this.form = {
                client_id: '',
                due_date: '',
                items: [{ description: '', quantity: 1, rate: 0 }],
                discount_percent: 0,
                tax_percent: 0,
                paid_amount: 0,
                notes: '',
            };
            this.showModal = true;
        },

        openEdit(invoice) {
            this.editingInvoice = invoice;
            const items = (invoice.items && invoice.items.length > 0)
                ? invoice.items.map(i => ({
                    description: i.description || i.name || '',
                    quantity: parseFloat(i.quantity || i.qty || 1),
                    rate: parseFloat(i.rate || i.price || 0),
                  }))
                : [{ description: '', quantity: 1, rate: 0 }];
            this.form = {
                client_id: invoice.client_id || '',
                due_date: invoice.due_date ? invoice.due_date.substring(0, 10) : '',
                items: items,
                discount_percent: parseFloat(invoice.discount_percent || 0),
                tax_percent: parseFloat(invoice.tax_percent || 0),
                paid_amount: parseFloat(invoice.paid_amount || 0),
                notes: invoice.notes || '',
            };
            this.showModal = true;
        },

        closeModal() {
            this.showModal = false;
            this.editingInvoice = null;
        },

        openEditBayar(invoice) {
            this.bayarInvoice = Object.assign({}, invoice);
            this.showBayarModal = true;
        },

        addItem() {
            this.form.items.push({ description: '', quantity: 1, rate: 0 });
        },

        removeItem(index) {
            if (this.form.items.length > 1) {
                this.form.items.splice(index, 1);
            }
        },

        subtotal() {
            return this.form.items.reduce((sum, item) => {
                return sum + (parseFloat(item.quantity || 0) * parseFloat(item.rate || 0));
            }, 0);
        },

        discountAmount() {
            return this.subtotal() * (parseFloat(this.form.discount_percent || 0) / 100);
        },

        taxAmount() {
            const afterDiscount = this.subtotal() - this.discountAmount();
            return afterDiscount * (parseFloat(this.form.tax_percent || 0) / 100);
        },

        grandTotal() {
            return this.subtotal() - this.discountAmount() + this.taxAmount();
        },

        formatRp(val) {
            const num = parseFloat(val || 0);
            return num.toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
        },

        confirmDelete(formId, invoiceNumber) {
            if (typeof SwalDanger !== 'undefined') {
                SwalDanger.fire({
                    title: 'Hapus Invoice?',
                    text: 'Invoice ' + invoiceNumber + ' akan dihapus permanen.',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal',
                    showCancelButton: true,
                }).then((result) => {
                    if (result.isConfirmed) document.getElementById(formId).submit();
                });
            } else {
                if (confirm('Hapus invoice ' + invoiceNumber + '?')) {
                    document.getElementById(formId).submit();
                }
            }
        },
    };
}
</script>
@endsection
