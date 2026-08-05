<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; color: #111827; background: #fff; font-size: 13px; }

        .page { padding: 48px; }

        /* Header */
        .header { display: table; width: 100%; margin-bottom: 36px; border-bottom: 4px solid #111827; padding-bottom: 24px; }
        .header-left { display: table-cell; vertical-align: top; width: 60%; }
        .header-right { display: table-cell; vertical-align: top; text-align: right; }
        .brand { font-size: 28px; font-weight: 900; letter-spacing: -1px; color: #111827; }
        .brand span { color: #4F46E5; }
        .brand-sub { font-size: 11px; color: #6B7280; margin-top: 2px; }
        .invoice-title { font-size: 36px; font-weight: 900; color: #4F46E5; letter-spacing: -1px; }
        .invoice-number { font-size: 14px; font-weight: 700; color: #111827; margin-top: 4px; }
        .invoice-date { font-size: 11px; color: #6B7280; margin-top: 2px; }

        /* Status badge */
        .badge { display: inline-block; padding: 4px 12px; border-radius: 6px; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; border: 2px solid #111827; }
        .badge-lunas { background: #dcfce7; color: #166534; }
        .badge-hutang { background: #fef3c7; color: #92400e; }

        /* Meta grid */
        .meta-section { display: table; width: 100%; margin-bottom: 32px; }
        .meta-cell { display: table-cell; vertical-align: top; width: 50%; }
        .meta-label { font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.8px; color: #6B7280; margin-bottom: 6px; }
        .meta-value { font-size: 14px; font-weight: 700; color: #111827; }
        .meta-value-sm { font-size: 12px; color: #374151; margin-top: 2px; }

        /* Items table */
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
        .items-table thead tr { background: #111827; }
        .items-table thead th { padding: 12px 14px; text-align: left; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.6px; color: #fff; }
        .items-table thead th.text-right { text-align: right; }
        .items-table tbody tr { border-bottom: 1px solid #E5E7EB; }
        .items-table tbody tr:nth-child(even) { background: #F9FAFB; }
        .items-table tbody td { padding: 12px 14px; font-size: 13px; color: #111827; }
        .items-table tbody td.text-right { text-align: right; font-weight: 600; }
        .items-table tfoot td { padding: 8px 14px; }

        /* Totals */
        .totals-wrapper { display: table; width: 100%; margin-bottom: 32px; }
        .totals-left { display: table-cell; vertical-align: top; width: 55%; }
        .totals-right { display: table-cell; vertical-align: top; width: 45%; }
        .totals-table { width: 100%; border-collapse: collapse; }
        .totals-table td { padding: 6px 12px; font-size: 13px; }
        .totals-table .label { color: #6B7280; text-align: right; }
        .totals-table .value { text-align: right; font-weight: 600; width: 130px; }
        .totals-table .total-row td { border-top: 3px solid #111827; padding-top: 10px; font-size: 16px; font-weight: 900; color: #111827; }
        .totals-table .discount-row td { color: #166534; }
        .totals-table .tax-row td { color: #92400e; }

        /* Payment summary */
        .payment-box { border: 3px solid #111827; border-radius: 8px; padding: 18px 20px; background: #F9FAFB; margin-bottom: 24px; }
        .payment-box-title { font-size: 13px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px; color: #111827; }
        .payment-row { display: table; width: 100%; padding: 6px 0; border-bottom: 1px dashed #D1D5DB; }
        .payment-row:last-child { border-bottom: none; }
        .payment-row-label { display: table-cell; font-size: 12px; color: #6B7280; }
        .payment-row-value { display: table-cell; text-align: right; font-size: 12px; font-weight: 700; color: #111827; }
        .payment-row.remaining .payment-row-label,
        .payment-row.remaining .payment-row-value { font-size: 14px; font-weight: 900; color: #EF4444; }
        .payment-row.paid .payment-row-value { color: #22C55E; }
        .payment-row.lunas .payment-row-label,
        .payment-row.lunas .payment-row-value { color: #166534; font-size: 14px; font-weight: 900; }

        /* Notes */
        .notes-box { border-left: 4px solid #4F46E5; padding: 10px 14px; background: #EEF2FF; margin-bottom: 24px; border-radius: 0 6px 6px 0; }
        .notes-label { font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.6px; color: #4F46E5; margin-bottom: 4px; }
        .notes-text { font-size: 12px; color: #374151; }

        /* Footer */
        .footer { border-top: 2px solid #E5E7EB; padding-top: 16px; display: table; width: 100%; }
        .footer-left { display: table-cell; font-size: 11px; color: #9CA3AF; }
        .footer-right { display: table-cell; text-align: right; font-size: 11px; color: #9CA3AF; }
        .footer-brand { font-weight: 800; color: #4F46E5; }
    </style>
</head>
<body>
<div class="page">

    {{-- HEADER --}}
    <div class="header">
        <div class="header-left">
            <div class="brand">UROO<span>.</span>DEV</div>
            <div class="brand-sub">Software Developer &amp; Freelance Services</div>
        </div>
        <div class="header-right">
            <div class="invoice-title">INVOICE</div>
            <div class="invoice-number">{{ $invoice->invoice_number }}</div>
            <div class="invoice-date">Tanggal: {{ $invoice->created_at->format('d F Y') }}</div>
            <div style="margin-top: 8px;">
                <span class="badge badge-{{ $invoice->status }}">{{ strtoupper($invoice->status) }}</span>
            </div>
        </div>
    </div>

    {{-- META: Kepada & Detail --}}
    <div class="meta-section">
        <div class="meta-cell">
            <div class="meta-label">Kepada</div>
            <div class="meta-value">{{ $invoice->client?->name ?? 'N/A' }}</div>
            @if($invoice->client?->company)
                <div class="meta-value-sm">{{ $invoice->client->company }}</div>
            @endif
            @if($invoice->client?->address)
                <div class="meta-value-sm">{{ $invoice->client->address }}</div>
            @endif
            @if($invoice->client?->email)
                <div class="meta-value-sm">{{ $invoice->client->email }}</div>
            @endif
            @if($invoice->client?->phone)
                <div class="meta-value-sm">{{ $invoice->client->phone }}</div>
            @endif
        </div>
        <div class="meta-cell" style="text-align: right;">
            <div style="display: inline-block; text-align: left;">
                <div class="meta-label">Tanggal Invoice</div>
                <div class="meta-value">{{ $invoice->created_at->format('d M Y') }}</div>
                <div style="margin-top: 12px;">
                    <div class="meta-label">Jatuh Tempo</div>
                    <div class="meta-value" style="{{ $invoice->due_date && $invoice->due_date->isPast() && $invoice->status !== 'lunas' ? 'color:#EF4444;' : '' }}">
                        {{ $invoice->due_date?->format('d M Y') ?? '-' }}
                    </div>
                </div>
                @if($invoice->paid_at)
                    <div style="margin-top: 12px;">
                        <div class="meta-label">Tanggal Lunas</div>
                        <div class="meta-value" style="color:#22C55E;">{{ $invoice->paid_at->format('d M Y') }}</div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ITEMS TABLE --}}
    @php $items = $invoice->items ?? []; @endphp
    <table class="items-table">
        <thead>
            <tr>
                <th style="width:5%;">#</th>
                <th style="width:45%;">Deskripsi</th>
                <th class="text-right" style="width:15%;">Qty</th>
                <th class="text-right" style="width:20%;">Harga Satuan</th>
                <th class="text-right" style="width:15%;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $i => $item)
                @php
                    $desc  = $item['description'] ?? ($item['name'] ?? '-');
                    $qty   = (float) ($item['quantity'] ?? $item['qty'] ?? 1);
                    $rate  = (float) ($item['rate'] ?? $item['price'] ?? 0);
                    $sub   = $qty * $rate;
                @endphp
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $desc }}</td>
                    <td class="text-right">{{ rtrim(rtrim(number_format($qty, 2, ',', '.'), '0'), ',') }}</td>
                    <td class="text-right">Rp {{ number_format($rate, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($sub, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align:center;color:#6B7280;padding:20px;">Tidak ada item</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- TOTALS --}}
    <div class="totals-wrapper">
        <div class="totals-left">
            @if($invoice->notes)
                <div class="notes-box">
                    <div class="notes-label">Catatan</div>
                    <div class="notes-text">{{ $invoice->notes }}</div>
                </div>
            @endif
        </div>
        <div class="totals-right">
            <table class="totals-table">
                <tr>
                    <td class="label">Subtotal</td>
                    <td class="value">Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</td>
                </tr>
                @if((float)$invoice->discount_amount > 0)
                    <tr class="discount-row">
                        <td class="label">Diskon ({{ rtrim(rtrim(number_format($invoice->discount_percent,2,',','.'), '0'), ',') }}%)</td>
                        <td class="value">- Rp {{ number_format($invoice->discount_amount, 0, ',', '.') }}</td>
                    </tr>
                @endif
                @if((float)$invoice->tax_amount > 0)
                    <tr class="tax-row">
                        <td class="label">PPN ({{ rtrim(rtrim(number_format($invoice->tax_percent,2,',','.'), '0'), ',') }}%)</td>
                        <td class="value">+ Rp {{ number_format($invoice->tax_amount, 0, ',', '.') }}</td>
                    </tr>
                @endif
                <tr class="total-row">
                    <td class="label">TOTAL</td>
                    <td class="value">Rp {{ number_format($invoice->total, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>
    </div>

    {{-- PAYMENT SUMMARY --}}
    <div class="payment-box">
        <div class="payment-box-title">Ringkasan Pembayaran</div>
        <div class="payment-row">
            <div class="payment-row-label">Total Tagihan</div>
            <div class="payment-row-value">Rp {{ number_format($invoice->total, 0, ',', '.') }}</div>
        </div>
        <div class="payment-row paid">
            <div class="payment-row-label">Sudah Dibayar</div>
            <div class="payment-row-value">Rp {{ number_format($invoice->paid_amount, 0, ',', '.') }}</div>
        </div>
        @if($invoice->remainingAmount() > 0)
            <div class="payment-row remaining">
                <div class="payment-row-label">Sisa Tagihan</div>
                <div class="payment-row-value">Rp {{ number_format($invoice->remainingAmount(), 0, ',', '.') }}</div>
            </div>
        @else
            <div class="payment-row lunas">
                <div class="payment-row-label">Status</div>
                <div class="payment-row-value">&#10003; LUNAS</div>
            </div>
        @endif
    </div>

    {{-- FOOTER --}}
    <div class="footer">
        <div class="footer-left">
            Terima kasih atas kepercayaan Anda.<br>
            Invoice ini dibuat secara otomatis oleh <span class="footer-brand">UROO.DEV</span>
        </div>
        <div class="footer-right">
            Dicetak: {{ now()->format('d M Y H:i') }}
        </div>
    </div>

</div>
</body>
</html>
