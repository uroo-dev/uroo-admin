<div>
    <div class="space-y-2 mb-4">
        @foreach ($items as $item)
            <div class="flex items-center gap-3 p-3 rounded-button border-2 border-gray-100 hover:border-border-dark transition-colors">
                <button wire:click="toggleItem({{ $item->id }})"
                    class="w-6 h-6 rounded border-4 border-border-dark flex items-center justify-center flex-shrink-0 transition-colors {{ $item->is_checked ? 'bg-[#22C55E] border-[#22C55E]' : 'bg-surface' }}">
                    @if ($item->is_checked)
                        <i class="bx bx-check text-white text-sm"></i>
                    @endif
                </button>
                <span class="text-sm font-medium flex-1 {{ $item->is_checked ? 'line-through text-txt-secondary' : '' }}">
                    {{ $item->label }}
                </span>
                <button wire:click="removeItem({{ $item->id }})" class="text-txt-secondary hover:text-danger transition-colors">
                    <i class="bx bx-x text-lg"></i>
                </button>
            </div>
        @endforeach
    </div>

    <div class="flex gap-2">
        <input wire:model="newItemLabel" wire:keydown.enter.prevent="addItem" placeholder="Tambah item..."
            class="flex-1 px-4 py-2.5 rounded-input border-4 border-border-dark bg-surface text-sm font-medium focus:border-primary outline-none">
        <button wire:click="addItem" class="px-4 py-2.5 bg-primary text-white font-bold text-sm rounded-button border-4 border-border-dark shadow-hard-sm hover:-translate-y-0.5 transition-all duration-200 ease-out">
            <i class="bx bx-plus"></i>
        </button>
    </div>
</div>