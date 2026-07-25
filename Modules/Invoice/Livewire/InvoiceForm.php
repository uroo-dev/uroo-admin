<?php

namespace Modules\Invoice\Livewire;

use Livewire\Component;
use Modules\Invoice\Models\Invoice;
use Modules\Invoice\Services\InvoiceService;
use Modules\Client\Models\Client;

class InvoiceForm extends Component
{
    public ?int $invoiceId = null;
    public int $client_id = 0;
    public string $status = 'pending';
    public string $due_date = '';
    public string $notes = '';
    public array $items = [];
    public float $subtotal = 0;
    public float $tax_percent = 0;
    public float $tax_amount = 0;
    public float $discount_percent = 0;
    public float $discount_amount = 0;
    public float $total = 0;

    public bool $isEdit = false;

    protected $listeners = ['editInvoice' => 'loadInvoice'];

    public function mount(?int $id = null): void
    {
        if ($id) {
            $this->loadInvoice($id);
        }

        if (empty($this->items)) {
            $this->addItem();
        }
    }

    public function loadInvoice(int $id): void
    {
        $invoice = Invoice::findOrFail($id);
        $this->invoiceId = $invoice->id;
        $this->client_id = $invoice->client_id;
        $this->status = $invoice->status;
        $this->due_date = $invoice->due_date->format('Y-m-d');
        $this->notes = $invoice->notes;
        $this->items = $invoice->items;
        $this->subtotal = (float) $invoice->subtotal;
        $this->tax_percent = (float) $invoice->tax_percent;
        $this->tax_amount = (float) $invoice->tax_amount;
        $this->discount_percent = (float) $invoice->discount_percent;
        $this->discount_amount = (float) $invoice->discount_amount;
        $this->total = (float) $invoice->total;
        $this->isEdit = true;
    }

    public function addItem(): void
    {
        $this->items[] = ['description' => '', 'quantity' => 1, 'rate' => 0, 'amount' => 0];
    }

    public function removeItem(int $index): void
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
        $this->recalculate();
    }

    public function updatedItems(): void
    {
        $this->recalculate();
    }

    public function recalculate(): void
    {
        $service = app(InvoiceService::class);
        $totals = $service->calculateTotals(
            $this->items,
            (float) $this->tax_percent,
            (float) $this->discount_percent
        );

        $this->subtotal = $totals['subtotal'];
        $this->tax_amount = $totals['tax_amount'];
        $this->discount_amount = $totals['discount_amount'];
        $this->total = $totals['total'];
    }

    public function save(): void
    {
        $this->validate([
            'client_id' => 'required|exists:clients,id',
            'due_date' => 'required|date',
            'status' => 'required|in:pending,paid,overdue,cancelled',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.rate' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $this->recalculate();

        $service = app(InvoiceService::class);
        $data = [
            'user_id' => auth()->id(),
            'client_id' => $this->client_id,
            'items' => $this->items,
            'subtotal' => $this->subtotal,
            'tax_percent' => $this->tax_percent,
            'tax_amount' => $this->tax_amount,
            'discount_percent' => $this->discount_percent,
            'discount_amount' => $this->discount_amount,
            'total' => $this->total,
            'status' => $this->status,
            'due_date' => $this->due_date,
            'notes' => $this->notes,
        ];

        if ($this->isEdit) {
            $invoice = Invoice::findOrFail($this->invoiceId);
            $invoice->update($data);
            $this->dispatch('swal:success', title: 'Invoice berhasil diperbarui');
        } else {
            $data['invoice_number'] = $service->generateInvoiceNumber();
            Invoice::create($data);
            $this->dispatch('swal:success', title: 'Invoice berhasil dibuat');
        }

        $this->dispatch('invoice-saved');
        $this->resetForm();
    }

    public function resetForm(): void
    {
        $this->reset([
            'invoiceId', 'client_id', 'status', 'due_date', 'notes',
            'items', 'subtotal', 'tax_percent', 'tax_amount',
            'discount_percent', 'discount_amount', 'total', 'isEdit',
        ]);
        $this->addItem();
    }

    public function render()
    {
        $clients = Client::select('id', 'name')->orderBy('name')->get();
        return view('invoices.form', compact('clients'));
    }
}
