<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        body { font-family: sans-serif; color: #1a1a1a; max-width: 800px; margin: 0 auto; padding: 40px; }
        .header { border-bottom: 4px solid #1a1a1a; padding-bottom: 20px; margin-bottom: 30px; }
        .header h1 { font-size: 28px; margin: 0 0 4px; }
        .header .meta { font-size: 13px; color: #555; }
        .meta-grid { display: flex; gap: 40px; margin-bottom: 20px; flex-wrap: wrap; }
        .meta-grid div { font-size: 13px; }
        .meta-grid strong { display: block; font-size: 11px; text-transform: uppercase; color: #888; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th { text-align: left; border-bottom: 3px solid #1a1a1a; padding: 10px 8px; font-size: 12px; text-transform: uppercase; }
        td { padding: 8px; border-bottom: 1px solid #eee; font-size: 13px; }
        .text-right { text-align: right; }
        .totals { margin-top: 20px; }
        .totals table { width: 250px; margin-left: auto; }
        .totals td { border: none; padding: 4px 8px; font-size: 13px; }
        .totals .total-row { font-weight: bold; font-size: 15px; border-top: 3px solid #1a1a1a; }
        .payment-box { border: 4px solid #1a1a1a; border-radius: 8px; padding: 16px; margin-top: 30px; background: #f9f9f9; }
        .payment-box h3 { margin: 0 0 12px; font-size: 16px; }
        .payment-row { display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px dashed #ddd; font-size: 13px; }
        .payment-row:last-child { border-bottom: none; }
        .status-badge { display: inline-block; padding: 4px 12px; border-radius: 4px; font-size: 11px; font-weight: bold; text-transform: uppercase; }
        .status-paid { background: #dcfce7; color: #166534; }
        .status-hutang { background: #fef3c7; color: #92400e; }
        .status-lunas { background: #dcfce7; color: #166534; }
        .footer { margin-top: 40px; padding-top: 16px; border-top: 2px solid #ddd; font-size: 11px; color: #888; }
        @media print { body { padding: 20px; } }
    </style>
</head>
<body>
    <div class="header">
        <h1>INVOICE</h1>
        <div class="meta">{{ $invoice->invoice_number }}</div>
    </div>

    <div class="meta-grid">
        <div>
            <strong>Kepada</strong>
            {{ $invoice->client?->name ?? 'N/A' }}<br>
            {{ $invoice->client?->address ?? '' }}
        </div>
        <div>
            <strong>Tanggal</strong>
            {{ $invoice->created_at->format('d M Y') }}<br>
            <strong>Jatuh Tempo</strong><br>
            {{ $invoice->due_date?->format('d M Y') ?? '-' }}
        </div>
        <div>
            <strong>Status</strong>
            <span class="status-badge status-{{ $invoice->status }}">{{ ucfirst($invoice->status) }}</span>
            @if($invoice->paid_at)
                <br><strong>Tanggal Bayar</strong><br>{{ $invoice->paid_at->format('d M Y') }}
            @endif
        </div>
    </div>

    @php
        $items = $invoice->items ?? [];
        $hasNewFormat = isset($items[0]['description']);
    @endphp
    <table>
        <thead>
            <tr>
                <th>Deskripsi</th>
                <th class="text-right">Qty</th>
                <th class="text-right">Rate</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
                @php
                    $desc = $hasNewFormat ? ($item['description'] ?? '-') : ($item['name'] ?? '-');
                    $qty = $hasNewFormat ? ($item['quantity'] ?? 1) : ($item['qty'] ?? 1);
                    $rate = $hasNewFormat ? ($item['rate'] ?? 0) : ($item['price'] ?? 0);
                @endphp
                <tr>
                    <td>{{ $desc }}</td>
                    <td class="text-right">{{ $qty }}</td>
                    <td class="text-right">Rp {{ number_format($rate, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format((float)$qty * (float)$rate, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <table>
            <tr><td>Subtotal</td><td class="text-right">Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</td></tr>
            @if($invoice->discount_amount > 0)
                <tr><td>Diskon ({{ $invoice->discount_percent }}%)</td><td class="text-right">- Rp {{ number_format($invoice->discount_amount, 0, ',', '.') }}</td></tr>
            @endif
            @if($invoice->tax_amount > 0)
                <tr><td>PPN ({{ $invoice->tax_percent }}%)</td><td class="text-right">+ Rp {{ number_format($invoice->tax_amount, 0, ',', '.') }}</td></tr>
            @endif
            <tr class="total-row"><td>Total</td><td class="text-right">Rp {{ number_format($invoice->total, 0, ',', '.') }}</td></tr>
        </table>
    </div>

    <div class="payment-box">
        <h3>Ringkasan Pembayaran</h3>
        <div class="payment-row"><span>Total Tagihan</span><span>Rp {{ number_format($invoice->total, 0, ',', '.') }}</span></div>
        <div class="payment-row"><span>Terbayar</span><span>Rp {{ number_format($invoice->paid_amount, 0, ',', '.') }}</span></div>
        <div class="payment-row"><strong>Sisa</strong><strong>Rp {{ number_format($invoice->remainingAmount(), 0, ',', '.') }}</strong></div>
    </div>

    @if($invoice->notes)
        <div style="margin-top: 24px;">
            <strong>Catatan</strong>
            <p style="font-size: 13px; margin-top: 4px;">{{ $invoice->notes }}</p>
        </div>
    @endif

    <div class="footer">
        Terima kasih atas kerjasamanya. Invoice ini dibuat secara otomatis oleh UROO.DEV.
    </div>
</body>
</html>