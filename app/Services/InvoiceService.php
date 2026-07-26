<?php

namespace App\Services;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceService
{
    public function generateInvoiceNumber(): string
    {
        return Invoice::generateInvoiceNumber();
    }

    public function calculateTotals(array $items, float $taxPercent, float $discountPercent): array
    {
        $subtotal = collect($items)->sum(function ($item) {
            return (float) ($item['quantity'] ?? 1) * (float) ($item['rate'] ?? 0);
        });

        $discountAmount = $subtotal * ($discountPercent / 100);
        $afterDiscount = $subtotal - $discountAmount;
        $taxAmount = $afterDiscount * ($taxPercent / 100);
        $total = $afterDiscount + $taxAmount;

        return [
            'subtotal' => round($subtotal, 2),
            'tax_amount' => round($taxAmount, 2),
            'discount_amount' => round($discountAmount, 2),
            'total' => round($total, 2),
        ];
    }

    public function getStats(): array
    {
        $totalRevenue = Invoice::where('status', 'paid')->sum('total');
        $pendingAmount = Invoice::where('status', 'pending')->sum('total');
        $overdueAmount = Invoice::where('status', 'overdue')->sum('total');
        $countsByStatus = Invoice::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        return compact('totalRevenue', 'pendingAmount', 'overdueAmount', 'countsByStatus');
    }

    public function generatePdf(Invoice $invoice)
    {
        $invoice->load('client', 'user');
        return Pdf::loadView('invoices.pdf', compact('invoice'));
    }
}
