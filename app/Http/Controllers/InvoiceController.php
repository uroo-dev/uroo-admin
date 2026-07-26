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
        $sortField = in_array($request->input('sort', 'created_at'), ['invoice_number', 'total', 'status', 'due_date', 'created_at']) ? $request->input('sort') : 'created_at';
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
        $totals = $invoiceService->calculateTotals($data['items'], (float)($data['tax_percent'] ?? 0), (float)($data['discount_percent'] ?? 0));
        Invoice::create(array_merge($data, $totals));
        return redirect()->route('invoices.index')->with('success', 'Invoice created successfully.');
    }

    public function update(InvoiceRequest $request, Invoice $invoice, InvoiceService $invoiceService)
    {
        $this->authorize('update', $invoice);
        $data = $request->validated();
        $totals = $invoiceService->calculateTotals($data['items'], (float)($data['tax_percent'] ?? 0), (float)($data['discount_percent'] ?? 0));
        $invoice->update(array_merge($data, $totals));
        return redirect()->route('invoices.index')->with('success', 'Invoice updated successfully.');
    }

    public function destroy(Invoice $invoice)
    {
        $this->authorize('delete', $invoice);
        $invoice->delete();
        return redirect()->route('invoices.index')->with('success', 'Invoice deleted successfully.');
    }

    public function markPaid(Invoice $invoice)
    {
        $this->authorize('update', $invoice);
        $invoice->update(['status' => 'paid', 'paid_at' => now()]);
        return redirect()->route('invoices.index')->with('success', 'Invoice marked as paid');
    }

    public function markOverdue(Invoice $invoice)
    {
        $this->authorize('update', $invoice);
        $invoice->update(['status' => 'overdue']);
        return redirect()->route('invoices.index')->with('success', 'Invoice marked as overdue');
    }

    public function downloadPdf(Invoice $invoice)
    {
        $pdf = app(InvoiceService::class)->generatePdf($invoice);
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, "invoice-{$invoice->invoice_number}.pdf");
    }
}
