<div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse ($dumps as $dump)
            <div class="bg-[{{ $dump->is_pinned ? '#FFD93D' : '#FFFFFF' }}] border-4 border-border-dark rounded-card shadow-hard p-4 transition-all duration-200 ease-out hover:-translate-y-1 hover:shadow-hard-hover"
                 x-data="{ content: '{{ $dump->content }}' }">
                <p class="text-sm font-medium mb-3" x-text="content"></p>
                <div class="flex items-center gap-2 pt-3 border-t-2 border-border-dark/30">
                    <button wire:click="togglePin({{ $dump->id }})"
                        class="text-sm {{ $dump->is_pinned ? 'text-primary' : 'text-txt-secondary' }} hover:text-primary transition-colors"
                        title="{{ $dump->is_pinned ? 'Unpin' : 'Pin' }}">
                        <i class="bx {{ $dump->is_pinned ? 'bxs-pin' : 'bx-pin' }}"></i>
                    </button>
                    <button wire:click="archive({{ $dump->id }})"
                        class="text-sm text-txt-secondary hover:text-[#F59E0B] transition-colors" title="Archive">
                        <i class="bx bx-archive-in"></i>
                    </button>
                    <button wire:click="$dispatch('swal:confirm', { event: 'delete-dump-{{ $dump->id }}', title: 'Hapus catatan?', confirmText: 'Ya, hapus!' })"
                        class="text-sm text-txt-secondary hover:text-danger transition-colors ml-auto" title="Hapus">
                        <i class="bx bx-trash"></i>
                    </button>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12">
                <i class="bx bx-cloud text-5xl text-txt-secondary"></i>
                <p class="mt-3 text-txt-secondary font-medium">Belum ada catatan</p>
            </div>
        @endforelse
    </div>

    @if ($dumps->hasPages())
        <div class="mt-6">
            {{ $dumps->links() }}
        </div>
    @endif
</div>