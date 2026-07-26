<div>
    <form wire:submit="save" class="space-y-5">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div class="space-y-1.5">
                <label class="block text-sm font-semibold text-txt-primary">Client</label>
                <select wire:model="client_id"
                    class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium text-txt-primary focus:border-primary outline-none transition-colors">
                    <option value="">Select Client</option>
                    @foreach ($clients as $client)
                        <option value="{{ $client->id }}">{{ $client->name }}</option>
                    @endforeach
                </select>
                @error('client_id') <span class="text-xs text-danger mt-1 block">{{ $message }}</span> @enderror
            </div>
            <x-input label="Status" name="status" wire:model="status" disabled />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <x-input label="Invoice Number" name="invoice_number" wire:model="invoice_number" disabled />
            <x-input label="Due Date" name="due_date" type="date" wire:model="due_date" />
        </div>

        <div x-data="{
            items: $wire.entangle('items'),
            addItem() {
                $wire.addItem();
            },
            removeItem(index) {
                $wire.removeItem(index);
            },
            get total() {
                return $wire.total;
            }
        }">
            <div class="flex items-center justify-between mb-3">
                <label class="text-sm font-semibold text-txt-primary">Invoice Items</label>
                <button type="button" wire:click="addItem" class="px-4 py-2 text-xs font-bold rounded-button border-4 border-border-dark bg-surface shadow-hard-sm hover:-translate-y-0.5 transition-all duration-200 ease-out">
                    <i class="bx bx-plus"></i> Add Item
                </button>
            </div>

            <div class="space-y-3">
                @foreach ($items as $index => $item)
                    <div class="flex items-start gap-3 p-4 rounded-button border-4 border-border-dark bg-gray-50" wire:key="item-{{ $index }}">
                        <div class="flex-1 space-y-1.5">
                            <input type="text" wire:model="items.{{ $index }}.description" placeholder="Item description"
                                class="w-full px-3 py-2 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none transition-colors">
                            @error("items.{{ $index }}.description") <span class="text-xs text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="w-20 space-y-1.5">
                            <input type="number" wire:model.live="items.{{ $index }}.quantity" min="1" placeholder="Qty"
                                class="w-full px-3 py-2 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none transition-colors text-center">
                        </div>
                        <div class="w-28 space-y-1.5">
                            <input type="number" wire:model.live="items.{{ $index }}.rate" min="0" step="0.01" placeholder="Rate"
                                class="w-full px-3 py-2 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none transition-colors text-right">
                        </div>
                        <div class="w-24 pt-1.5 text-right font-extrabold text-sm">
                            Rp {{ number_format($item['amount'] ?? 0, 0, ',', '.') }}
                        </div>
                        <button type="button" wire:click="removeItem({{ $index }})" class="pt-1.5 text-danger hover:text-red-700 transition-colors">
                            <i class="bx bx-x text-xl"></i>
                        </button>
                    </div>
                @endforeach
            </div>

            <div class="flex justify-end mt-4 pt-4 border-t-4 border-border-dark">
                <div class="text-right space-y-1">
                    <div class="flex justify-between gap-8">
                        <span class="text-sm font-medium text-txt-secondary">Subtotal</span>
                        <span class="text-sm font-extrabold">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>
                    @if ($tax_amount > 0)
                        <div class="flex justify-between gap-8">
                            <span class="text-sm font-medium text-txt-secondary">Tax ({{ $tax_percent }}%)</span>
                            <span class="text-sm font-extrabold">Rp {{ number_format($tax_amount, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    @if ($discount_amount > 0)
                        <div class="flex justify-between gap-8">
                            <span class="text-sm font-medium text-txt-secondary">Discount ({{ $discount_percent }}%)</span>
                            <span class="text-sm font-extrabold text-danger">-Rp {{ number_format($discount_amount, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between gap-8 pt-1 border-t-2 border-border-dark">
                        <span class="text-base font-extrabold">Total</span>
                        <span class="text-base font-extrabold">Rp {{ number_format($total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div>
            <label class="block text-sm font-semibold mb-1.5">Notes (optional)</label>
            <textarea wire:model="notes" rows="2" class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium focus:border-primary outline-none" placeholder="Payment notes..."></textarea>
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <button type="button" x-on:click="open = false" class="px-6 py-3 font-bold text-sm rounded-button border-4 border-border-dark bg-surface shadow-hard hover:-translate-y-0.5 transition-all duration-200 ease-out">Batal</button>
            <button type="submit" class="px-6 py-3 bg-primary text-white font-bold text-sm rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 transition-all duration-200 ease-out">Simpan</button>
        </div>
    </form>
</div>
