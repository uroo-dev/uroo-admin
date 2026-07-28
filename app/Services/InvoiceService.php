<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoicePayment;
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
        $totalLunas = Invoice::where('status', 'lunas')->sum('total');
        $totalHutang = Invoice::where('status', 'hutang')->sum('total');
        $countsByStatus = Invoice::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        return compact('totalLunas', 'totalHutang', 'countsByStatus');
    }

    public function processPayment(Invoice $invoice, float $amount): array
    {
        $newPaidAmount = (float) $invoice->paid_amount + $amount;
        $invoice->paid_amount = min($newPaidAmount, (float) $invoice->total);

        if ($invoice->isFullyPaid()) {
            $invoice->status = 'lunas';
            $invoice->paid_at = now();
        }

        $invoice->save();

        return [
            'paid_amount' => $invoice->paid_amount,
            'remaining' => $invoice->remainingAmount(),
            'status' => $invoice->status,
        ];
    }

    public function recordPayment(Invoice $invoice, float $amount, ?string $notes = null): array
    {
        $payment = InvoicePayment::create([
            'invoice_id' => $invoice->id,
            'amount' => $amount,
            'remaining_after' => $invoice->remainingAmount(),
            'notes' => $notes,
        ]);

        $newPaidAmount = (float) $invoice->paid_amount + $amount;
        $invoice->paid_amount = min($newPaidAmount, (float) $invoice->total);
        $this->checkPaymentStatus($invoice);

        return [
            'payment' => $payment,
            'paid_amount' => $invoice->paid_amount,
            'remaining' => $invoice->remainingAmount(),
            'status' => $invoice->status,
        ];
    }

    private function checkPaymentStatus(Invoice $invoice): void
    {
        if ($invoice->paid_amount >= $invoice->total && $invoice->total > 0) {
            $invoice->update(['status' => 'lunas', 'paid_at' => now()]);
        } elseif ($invoice->paid_amount > 0 && $invoice->status !== 'lunas') {
            $invoice->update(['status' => 'hutang']);
        }
    }

    public function generatePdf(Invoice $invoice)
    {
        $invoice->load('client', 'user');
        return Pdf::loadView('invoices.pdf', compact('invoice'));
    }
}
