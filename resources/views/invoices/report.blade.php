@extends('layouts.app')

@section('title', 'Laporan Invoice #' . $invoice->invoice_number)

@section('content')
<div class="max-w-4xl mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-extrabold text-txt-primary">Laporan Invoice #{{ $invoice->invoice_number }}</h1>
        <a href="{{ route('invoices.index') }}" class="px-4 py-2 font-bold text-sm rounded-button border-4 border-border-dark bg-surface shadow-hard hover:-translate-y-0.5 transition-all duration-200 ease-out">
            <i class="bx bx-arrow-back mr-1"></i> Kembali
        </a>
    </div>

    <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 mb-6">
        <h2 class="text-lg font-extrabold mb-4">Informasi Invoice</h2>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-txt-secondary">Klien</p>
                <p class="font-semibold">{{ $invoice->client?->name ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-txt-secondary">Status</p>
                @php
                    $badgeVariant = match($invoice->status) {
                        'lunas' => 'success',
                        'hutang' => 'warning',
                        default => 'default',
                    };
                @endphp
                <x-badge variant="{{ $badgeVariant }}">{{ ucfirst($invoice->status) }}</x-badge>
            </div>
            <div>
                <p class="text-sm text-txt-secondary">Total Tagihan</p>
                <p class="font-extrabold text-lg">Rp {{ number_format($invoice->total, 0, ',', '.') }}</p>
            </div>
            <div>
                <p class="text-sm text-txt-secondary">Total Dibayar</p>
                <p class="font-extrabold text-lg text-[#22C55E]">Rp {{ number_format($invoice->paid_amount, 0, ',', '.') }}</p>
            </div>
            <div>
                <p class="text-sm text-txt-secondary">Sisa</p>
                <p class="font-extrabold text-lg {{ $invoice->remainingAmount() > 0 ? 'text-danger' : 'text-[#22C55E]' }}">Rp {{ number_format($invoice->remainingAmount(), 0, ',', '.') }}</p>
            </div>
            <div>
                <p class="text-sm text-txt-secondary">Tanggal Jatuh Tempo</p>
                <p class="font-semibold">{{ $invoice->due_date?->format('d M Y') ?? '-' }}</p>
            </div>
        </div>
        @if($invoice->notes)
            <div class="mt-4 pt-4 border-t-4 border-border-dark">
                <p class="text-sm text-txt-secondary">Catatan</p>
                <p class="text-sm">{{ $invoice->notes }}</p>
            </div>
        @endif
    </div>

    <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-extrabold">Riwayat Pembayaran</h2>
            <a href="{{ route('invoices.report', $invoice) }}" class="text-sm text-primary font-bold hover:underline">Segarkan</a>
        </div>

        @if($invoice->payments->isEmpty())
            <div class="text-center py-8 text-txt-secondary">
                <i class="bx bx-receipt text-4xl mb-2"></i>
                <p>Belum ada catatan pembayaran</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b-4 border-border-dark">
                            <th class="text-left px-4 py-3 font-extrabold">No</th>
                            <th class="text-right px-4 py-3 font-extrabold">Tanggal</th>
                            <th class="text-right px-4 py-3 font-extrabold">Jumlah Bayar</th>
                            <th class="text-right px-4 py-3 font-extrabold">Sisa Setelah Bayar</th>
                            <th class="text-left px-4 py-3 font-extrabold">Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoice->payments as $index => $payment)
                            <tr class="border-b-2 border-gray-100">
                                <td class="px-4 py-3 font-semibold">{{ $index + 1 }}</td>
                                <td class="px-4 py-3 text-right text-txt-secondary whitespace-nowrap">{{ $payment->created_at->format('d M Y H:i') }}</td>
                                <td class="px-4 py-3 text-right font-extrabold text-[#22C55E]">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right font-bold {{ $payment->remaining_after > 0 ? 'text-danger' : 'text-[#22C55E]' }}">Rp {{ number_format($payment->remaining_after, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-txt-secondary">{{ $payment->notes ?? '-' }}</td>
                            </tr>
                        @endforeach
                        <tr class="border-t-4 border-border-dark font-extrabold">
                            <td colspan="2" class="px-4 py-3 text-right">Total Dibayar</td>
                            <td class="px-4 py-3 text-right text-[#22C55E]">Rp {{ number_format($invoice->paid_amount, 0, ',', '.') }}</td>
                            <td colspan="2" class="px-4 py-3 {{ $invoice->remainingAmount() > 0 ? 'text-danger' : 'text-[#22C55E]' }}">
                                {{ $invoice->remainingAmount() > 0 ? 'Sisa: Rp ' . number_format($invoice->remainingAmount(), 0, ',', '.') : 'LUNAS' }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endif

        <div class="flex gap-3 mt-6 pt-4 border-t-4 border-border-dark">
            <a href="{{ route('invoices.pdf', $invoice) }}" target="_blank" class="px-4 py-2 bg-primary text-white font-bold text-sm rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 transition-all duration-200 ease-out">
                <i class="bx bx-download mr-1"></i> Download PDF
            </a>
            <a href="{{ route('invoices.send-wa', $invoice) }}" target="_blank" class="px-4 py-2 bg-[#25D366] text-white font-bold text-sm rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 transition-all duration-200 ease-out">
                <i class="bx bxl-whatsapp mr-1"></i> Kirim WA
            </a>
        </div>
    </div>
</div>
@endsection