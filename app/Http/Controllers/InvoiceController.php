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
        $query = Invoice::with(['client:id,name']);
        $search = $request->input('search', '');
        $statusFilter = $request->input('status', '');
        $sortField = $request->input('sort', 'created_at');
        if (!in_array($sortField, ['invoice_number', 'total', 'status', 'due_date', 'created_at'])) {
            $sortField = 'created_at';
        }
        $sortDirection = $request->input('direction', 'desc') === 'asc' ? 'asc' : 'desc';

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('client', fn($cq) => $cq->where('name', 'like', "%{$search}%"));
            });
        }
        if ($statusFilter) {
            $query->where('status', $statusFilter);
        }

        $invoices = $query->orderBy($sortField, $sortDirection)->paginate(12)->appends($request->query());
        $stats = app(InvoiceService::class)->getStats();

        return view('invoices.index', compact('invoices', 'stats', 'search', 'statusFilter', 'sortField', 'sortDirection'));
    }

    public function store(InvoiceRequest $request, InvoiceService $invoiceService)
    {
        $data = $request->validated();
        $data['invoice_number'] = $invoiceService->generateInvoiceNumber();
        $data['user_id'] = auth()->id();
        $data['paid_amount'] = (float) ($data['paid_amount'] ?? 0);

        if (!empty($data['total_billing'])) {
            $totals = [
                'subtotal' => (float) $data['total_billing'],
                'tax_percent' => 0,
                'tax_amount' => 0,
                'discount_percent' => 0,
                'discount_amount' => 0,
                'total' => (float) $data['total_billing'],
            ];
        } else {
            $totals = $invoiceService->calculateTotals($data['items'] ?? [], (float)($data['tax_percent'] ?? 0), (float)($data['discount_percent'] ?? 0));
        }

        $invoice = Invoice::create(array_merge($data, $totals));
        if ($invoice->paid_amount > 0) {
            $invoiceService->recordPayment($invoice, $invoice->paid_amount);
        }
        return redirect()->route('invoices.index')->with('success', 'Invoice created successfully.');
    }

    public function update(InvoiceRequest $request, Invoice $invoice, InvoiceService $invoiceService)
    {
        $this->authorize('update', $invoice);
        $data = $request->validated();
        $data['paid_amount'] = (float) ($data['paid_amount'] ?? 0);

        if (!empty($data['total_billing'])) {
            $totals = [
                'subtotal' => (float) $data['total_billing'],
                'tax_percent' => 0,
                'tax_amount' => 0,
                'discount_percent' => 0,
                'discount_amount' => 0,
                'total' => (float) $data['total_billing'],
            ];
        } else {
            $totals = $invoiceService->calculateTotals($data['items'] ?? [], (float)($data['tax_percent'] ?? 0), (float)($data['discount_percent'] ?? 0));
        }

        $invoice->update(array_merge($data, $totals));
        if ($data['paid_amount'] > 0) {
            $invoiceService->recordPayment($invoice, $data['paid_amount']);
        }
        return redirect()->route('invoices.index')->with('success', 'Invoice updated successfully.');
    }

    public function updatePayment(Request $request, Invoice $invoice, InvoiceService $invoiceService)
    {
        $this->authorize('update', $invoice);
        $request->validate([
            'paid_amount' => 'required|numeric|min:0',
        ]);
        $result = $invoiceService->recordPayment($invoice, (float) $request->input('paid_amount'));
        return redirect()->route('invoices.index')->with('success', 'Pembayaran diperbarui. Sisa: Rp ' . number_format($result['remaining'], 0, ',', '.'));
    }

    public function sendToWhatsapp(Invoice $invoice)
    {
        $this->authorize('view', $invoice);
        $pdf = app(InvoiceService::class)->generatePdf($invoice);
        $pdfPath = storage_path('app/public/invoice-' . $invoice->invoice_number . '.pdf');
        file_put_contents($pdfPath, $pdf->output());
        $clientWhatsapp = $invoice->client?->whatsapp ?? '';
        $message = urlencode("Halo {$invoice->client?->name},\n\nBerikut invoice kami:\nNo: {$invoice->invoice_number}\nTotal: Rp " . number_format($invoice->total, 0, ',', '.') . "\nTerbayar: Rp " . number_format($invoice->paid_amount, 0, ',', '.') . "\nSisa: Rp " . number_format($invoice->remainingAmount(), 0, ',', '.') . "\n\nTerima kasih.");
        $waUrl = "https://wa.me/{$clientWhatsapp}?text={$message}";
        return redirect()->to($waUrl);
    }

    public function destroy(Invoice $invoice)
    {
        $this->authorize('delete', $invoice);
        $invoice->delete();
        return redirect()->route('invoices.index')->with('success', 'Invoice deleted successfully.');
    }

    public function downloadPdf(Invoice $invoice)
    {
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