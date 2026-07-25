<?php

namespace Modules\Invoice\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\Invoice\Models\Invoice;
use Modules\Invoice\Services\InvoiceService;

class InvoiceList extends Component
{
    use WithPagination;

    public string $search = '';
    public string $status = '';
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';

    protected $queryString = ['search', 'status', 'sortField', 'sortDirection'];

    public function render()
    {
        $query = Invoice::with(['client:id,name']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('invoice_number', 'like', "%{$this->search}%")
                  ->orWhereHas('client', function ($cq) {
                      $cq->where('name', 'like', "%{$this->search}%");
                  });
            });
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        $invoices = $query->orderBy($this->sortField, $this->sortDirection)
            ->paginate(12);

        $stats = app(InvoiceService::class)->getStats();

        return view('invoices.index', compact('invoices', 'stats'));
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function markPaid(int $id): void
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->update(['status' => 'paid', 'paid_at' => now()]);
        $this->dispatch('swal:success', title: 'Invoice marked as paid');
    }

    public function markOverdue(int $id): void
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->update(['status' => 'overdue']);
        $this->dispatch('swal:success', title: 'Invoice marked as overdue');
    }

    public function deleteInvoice(int $id): void
    {
        Invoice::findOrFail($id)->delete();
        $this->dispatch('swal:success', title: 'Invoice berhasil dihapus');
    }
}
