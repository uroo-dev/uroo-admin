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

    public function calculateTotals(array $items, float $taxPercent = 0, float $discountPercent = 0): array
    {
        $subtotal = collect($items)->sum(function ($item) {
            return (float) ($item['quantity'] ?? 1) * (float) ($item['rate'] ?? 0);
        });

        $discountAmount = $subtotal * ($discountPercent / 100);
        $afterDiscount  = $subtotal - $discountAmount;
        $taxAmount      = $afterDiscount * ($taxPercent / 100);
        $total          = $afterDiscount + $taxAmount;

        return [
            'subtotal'         => round($subtotal, 2),
            'tax_percent'      => round($taxPercent, 2),
            'tax_amount'       => round($taxAmount, 2),
            'discount_percent' => round($discountPercent, 2),
            'discount_amount'  => round($discountAmount, 2),
            'total'            => round($total, 2),
        ];
    }

    public function getStats(): array
    {
        $totalRevenue   = (float) Invoice::sum('total');
        $totalLunas     = (float) Invoice::where('status', 'lunas')->sum('total');
        $totalHutang    = (float) Invoice::where('status', 'hutang')->sum('total');
        $totalPiutang   = (float) Invoice::where('status', 'hutang')->get()->sum(fn (Invoice $i) => $i->remainingAmount());
        $countsByStatus = Invoice::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        return compact('totalRevenue', 'totalLunas', 'totalHutang', 'totalPiutang', 'countsByStatus');
    }

    public function setPaidAmount(Invoice $invoice, float $amount, ?string $notes = null): array
    {
        $newPaid = min(max($amount, 0), (float) $invoice->total);
        $oldPaid = (float) $invoice->paid_amount;
        $delta   = round($newPaid - $oldPaid, 2);

        if ($delta > 0) {
            InvoicePayment::create([
                'invoice_id'      => $invoice->id,
                'amount'          => $delta,
                'remaining_after' => round((float) $invoice->total - $newPaid, 2),
                'notes'           => $notes,
            ]);
        } elseif ($delta < 0 && $invoice->status === 'lunas') {
            $invoice->paid_at = null;
        }

        $invoice->paid_amount = $newPaid;
        $this->checkPaymentStatus($invoice);

        return [
            'paid_amount' => $invoice->paid_amount,
            'remaining'   => $invoice->remainingAmount(),
            'status'      => $invoice->status,
            'new_payment' => $delta > 0,
        ];
    }

    private function checkPaymentStatus(Invoice $invoice): void
    {
        if ((float) $invoice->total > 0 && (float) $invoice->paid_amount >= (float) $invoice->total) {
            $invoice->update(['status' => 'lunas', 'paid_at' => now()]);
        } else {
            $invoice->update(['status' => 'hutang']);
        }
    }

    public function generatePdf(Invoice $invoice)
    {
        $invoice->load('client', 'user', 'payments');

        return Pdf::loadView('invoices.pdf', compact('invoice'))
            ->setPaper('a4', 'portrait');
    }
}
