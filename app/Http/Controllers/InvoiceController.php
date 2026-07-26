<?php

namespace App\Http\Controllers;

use App\Http\Requests\InvoiceRequest;
use App\Models\Invoice;
use App\Services\InvoiceService;

class InvoiceController extends Controller
{
    public function index()
    {
        return view('invoices.index');
    }

    public function store(InvoiceRequest $request, InvoiceService $invoiceService)
    {
        $data = $request->validated();
        $data['invoice_number'] = $invoiceService->generateInvoiceNumber();
        $data['user_id'] = auth()->id();

        $totals = $invoiceService->calculateTotals(
            $data['items'],
            (float) ($data['tax_percent'] ?? 0),
            (float) ($data['discount_percent'] ?? 0)
        );

        Invoice::create(array_merge($data, $totals));

        return redirect()->route('invoices.index')->with('success', 'Invoice created successfully.');
    }

    public function update(InvoiceRequest $request, Invoice $invoice, InvoiceService $invoiceService)
    {
        $this->authorize('update', $invoice);

        $data = $request->validated();

        $totals = $invoiceService->calculateTotals(
            $data['items'],
            (float) ($data['tax_percent'] ?? 0),
            (float) ($data['discount_percent'] ?? 0)
        );

        $invoice->update(array_merge($data, $totals));

        return redirect()->route('invoices.index')->with('success', 'Invoice updated successfully.');
    }

    public function destroy(Invoice $invoice)
    {
        $this->authorize('delete', $invoice);
        $invoice->delete();

        return redirect()->route('invoices.index')->with('success', 'Invoice deleted successfully.');
    }
}
