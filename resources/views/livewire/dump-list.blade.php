<div>
    @php
        $bgColors = ['#FFD93D', '#67E8F9', '#FF66C4', '#A855F7', '#22C55E', '#60A5FA', '#F97316', '#34D399'];
    @endphp

    {{-- Quick Add --}}
    <div class="mb-8">
        <x-card>
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 bg-primary rounded-button flex items-center justify-center shadow-hard flex-shrink-0">
                    <i class="bx bx-cloud-lightning text-white text-[26px]"></i>
                </div>
                <div class="flex-1">
                    <label class="block text-sm font-semibold text-txt-primary mb-1.5">Quick Note</label>
                    <textarea wire:model="newContent" rows="4"
                        placeholder="Apa yang ada di pikiranmu?"
                        class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none transition-colors resize-none"></textarea>
                    <div class="flex items-center justify-between mt-3">
                        <p class="text-xs text-txt-secondary">{{ strlen($newContent) }} characters</p>
                        <x-button wire:click="quickCreate" :disabled="!trim($newContent)">
                            <i class="bx bx-pin"></i> Add Note
                        </x-button>
                    </div>
                </div>
            </div>
        </x-card>
    </div>

    {{-- Search --}}
    <div class="flex items-center justify-between mb-6">
        <div class="relative">
            <i class="bx bx-search absolute left-4 top-1/2 -translate-y-1/2 text-txt-secondary text-lg"></i>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search notes..."
                class="w-80 pl-11 pr-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none transition-colors">
        </div>
        <p class="text-sm font-semibold text-txt-secondary">{{ $dumps->total() + count($pinnedDumps) }} notes</p>
    </div>

    {{-- Pinned Section --}}
    @if (count($pinnedDumps))
        <div class="mb-8">
            <div class="flex items-center gap-3 mb-4">
                <i class="bx bx-pin text-[#EF4444] text-xl"></i>
                <h3 class="text-lg font-extrabold">Pinned</h3>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                @foreach ($pinnedDumps as $dump)
                    <div
                        style="background-color: {{ $bgColors[$loop->index % count($bgColors)] }}"
                        class="border-4 border-border-dark rounded-card shadow-hard p-5 transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover">
                        <div class="flex items-start justify-between mb-3">
                            <button wire:click="togglePin({{ $dump->id }})" class="text-lg text-[#EF4444]">
                                <i class="bx bxs-pin"></i>
                            </button>
                            <div class="flex items-center gap-1">
                                <button wire:click="archive({{ $dump->id }})" class="p-1.5 rounded-lg hover:bg-black/10 transition-colors">
                                    <i class="bx bx-archive-in text-sm"></i>
                                </button>
                                <button wire:click="confirmDelete({{ $dump->id }})"
                                    class="p-1.5 rounded-lg hover:bg-black/10 transition-colors">
                                    <i class="bx bx-trash text-sm text-danger"></i>
                                </button>
                            </div>
                        </div>
                        <p class="text-sm font-medium leading-relaxed">{{ $dump->content }}</p>
                        <p class="text-[11px] font-semibold text-txt-secondary mt-3">{{ $dump->updated_at->diffForHumans() }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- All Notes --}}
    <div>
        <div class="flex items-center gap-3 mb-4">
            <i class="bx bx-notepad text-primary text-xl"></i>
            <h3 class="text-lg font-extrabold">All Notes</h3>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
            @forelse ($dumps as $dump)
                <div
                    style="background-color: {{ $bgColors[($loop->index + count($pinnedDumps)) % count($bgColors)] }}"
                    class="border-4 border-border-dark rounded-card shadow-hard p-5 transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover">
                    <div class="flex items-start justify-between mb-3">
                        <button wire:click="togglePin({{ $dump->id }})"
                            class="text-lg text-txt-secondary hover:text-[#EF4444] transition-colors">
                            <i class="bx bx-pin"></i>
                        </button>
                        <div class="flex items-center gap-1">
                            <button wire:click="archive({{ $dump->id }})" class="p-1.5 rounded-lg hover:bg-black/10 transition-colors">
                                <i class="bx bx-archive-in text-sm"></i>
                            </button>
                            <button wire:click="confirmDelete({{ $dump->id }})"
                                class="p-1.5 rounded-lg hover:bg-black/10 transition-colors">
                                <i class="bx bx-trash text-sm text-danger"></i>
                            </button>
                        </div>
                    </div>
                    <p class="text-sm font-medium leading-relaxed">{{ $dump->content }}</p>
                    <p class="text-[11px] font-semibold text-txt-secondary mt-3">{{ $dump->updated_at->diffForHumans() }}</p>
                </div>
            @empty
                <div class="col-span-full flex flex-col items-center justify-center py-16">
                    <i class="bx bx-cloud text-6xl text-txt-secondary"></i>
                    <p class="mt-3 text-txt-secondary font-medium">Belum ada catatan</p>
                </div>
            @endforelse
        </div>
    </div>

    @if ($dumps->hasPages())
        <div class="mt-6">
            {{ $dumps->links() }}
        </div>
    @endif
</div>