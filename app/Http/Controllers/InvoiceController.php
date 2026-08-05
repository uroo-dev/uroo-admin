<?php

namespace App\Http\Controllers;

use App\Http\Requests\InvoiceRequest;
use App\Models\Invoice;
use App\Services\InvoiceService;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Invoice::with(['client:id,name,whatsapp']);

        $search          = $request->input('search', '');
        $statusFilter    = $request->input('status', '');
        $sortField       = $request->input('sort', 'created_at');
        $sortDirection   = $request->input('direction', 'desc') === 'asc' ? 'asc' : 'desc';

        if (! in_array($sortField, ['invoice_number', 'total', 'status', 'due_date', 'created_at'])) {
            $sortField = 'created_at';
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                    ->orWhereHas('client', fn ($cq) => $cq->where('name', 'like', "%{$search}%"));
            });
        }

        if ($statusFilter) {
            $query->where('status', $statusFilter);
        }

        $invoices = $query->orderBy($sortField, $sortDirection)->paginate(12)->appends($request->query());
        $stats    = app(InvoiceService::class)->getStats();
        $clients  = \App\Models\Client::orderBy('name')->get(['id', 'name']);

        return view('invoices.index', compact('invoices', 'stats', 'search', 'statusFilter', 'sortField', 'sortDirection', 'clients'));
    }

    public function store(InvoiceRequest $request, InvoiceService $invoiceService)
    {
        $data       = $request->validated();
        $paidAmount = (float) ($data['paid_amount'] ?? 0);
        unset($data['paid_amount']);

        $totals = $invoiceService->calculateTotals(
            $data['items'],
            (float) ($data['tax_percent'] ?? 0),
            (float) ($data['discount_percent'] ?? 0)
        );

        $invoice = Invoice::create(array_merge($data, [
            'invoice_number' => $invoiceService->generateInvoiceNumber(),
            'user_id'        => auth()->id(),
            'status'         => 'hutang',
        ], $totals));

        $invoiceService->setPaidAmount($invoice, $paidAmount);

        return redirect()->route('invoices.index')->with('success', 'Invoice berhasil dibuat.');
    }

    public function update(InvoiceRequest $request, Invoice $invoice, InvoiceService $invoiceService)
    {
        $this->authorize('update', $invoice);

        $data       = $request->validated();
        $paidAmount = (float) ($data['paid_amount'] ?? 0);
        unset($data['paid_amount']);

        $totals = $invoiceService->calculateTotals(
            $data['items'],
            (float) ($data['tax_percent'] ?? 0),
            (float) ($data['discount_percent'] ?? 0)
        );

        $invoice->update(array_merge($data, $totals));
        $invoiceService->setPaidAmount($invoice, $paidAmount);

        return redirect()->route('invoices.index')->with('success', 'Invoice berhasil diperbarui.');
    }

    public function updatePayment(Request $request, Invoice $invoice, InvoiceService $invoiceService)
    {
        $this->authorize('update', $invoice);

        $request->validate([
            'paid_amount' => 'required|numeric|min:0',
        ]);

        $result = $invoiceService->setPaidAmount($invoice, (float) $request->input('paid_amount'));

        return redirect()->route('invoices.index')
            ->with('success', 'Pembayaran diperbarui. Sisa: Rp ' . number_format($result['remaining'], 0, ',', '.'));
    }

    public function sendToWhatsapp(Invoice $invoice)
    {
        $this->authorize('view', $invoice);

        $clientWhatsapp = $invoice->client?->whatsapp ?? '';
        $message        = urlencode(
            "Halo {$invoice->client?->name},\n\n" .
            "Berikut invoice kami:\n" .
            "No: {$invoice->invoice_number}\n" .
            "Total: Rp " . number_format($invoice->total, 0, ',', '.') . "\n" .
            "Terbayar: Rp " . number_format($invoice->paid_amount, 0, ',', '.') . "\n" .
            "Sisa: Rp " . number_format($invoice->remainingAmount(), 0, ',', '.') . "\n\n" .
            "Terima kasih."
        );

        return redirect()->to("https://wa.me/{$clientWhatsapp}?text={$message}");
    }

    public function destroy(Invoice $invoice)
    {
        $this->authorize('delete', $invoice);
        $invoice->delete();

        return redirect()->route('invoices.index')->with('success', 'Invoice berhasil dihapus.');
    }

    public function previewPdf(Invoice $invoice)
    {
        $this->authorize('view', $invoice);

        $pdf = app(InvoiceService::class)->generatePdf($invoice);

        return response($pdf->output(), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="invoice-' . $invoice->invoice_number . '.pdf"',
        ]);
    }

    public function downloadPdf(Invoice $invoice)
    {
        $this->authorize('view', $invoice);

        $pdf = app(InvoiceService::class)->generatePdf($invoice);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, "invoice-{$invoice->invoice_number}.pdf");
    }

    public function report(Invoice $invoice)
    {
        $this->authorize('view', $invoice);
        $invoice->load('client', 'payments');

        return view('invoices.report', compact('invoice'));
    }
}
