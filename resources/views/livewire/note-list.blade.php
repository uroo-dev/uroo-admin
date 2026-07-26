<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div class="flex gap-3 flex-wrap">
            <div class="relative">
                <i class="bx bx-search absolute left-3 top-1/2 -translate-y-1/2 text-txt-secondary text-lg"></i>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search notes..."
                    class="w-64 pl-10 pr-4 py-2.5 rounded-input border-4 border-border-dark bg-surface text-sm font-medium focus:border-primary outline-none">
            </div>
            <select wire:model.live="category"
                class="px-4 py-2.5 rounded-input border-4 border-border-dark bg-surface text-sm font-medium focus:border-primary outline-none">
                <option value="">All Categories</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat }}">{{ $cat }}</option>
                @endforeach
            </select>
        </div>
        <button onclick="Livewire.dispatch('open-modal', { id: 'note-form' })"
            class="px-5 py-2.5 bg-primary text-white font-bold text-sm rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 active:translate-y-1 transition-all duration-200 ease-out flex items-center gap-2">
            <i class="bx bx-plus text-lg"></i>
            New Note
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($notes as $note)
            <div wire:click="edit({{ $note->id }})"
                class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover cursor-pointer group">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex items-center gap-2 min-w-0">
                        @if ($note->is_pinned)
                            <i class="bx bxs-pin text-primary text-lg flex-shrink-0"></i>
                        @endif
                        <h3 class="font-bold text-sm truncate">{{ $note->title }}</h3>
                    </div>
                    <div class="flex items-center gap-1 flex-shrink-0">
                        <button wire:click.stop="togglePin({{ $note->id }})"
                            class="text-lg {{ $note->is_pinned ? 'text-primary' : 'text-txt-secondary' }} hover:text-primary transition-colors">
                            <i class="bx {{ $note->is_pinned ? 'bxs-pin' : 'bx-pin' }}"></i>
                        </button>
                        <button wire:click.stop="toggleFavorite({{ $note->id }})"
                            class="text-lg {{ $note->is_favorite ? 'text-[#F59E0B]' : 'text-txt-secondary' }} hover:text-[#F59E0B] transition-colors">
                            <i class="bx {{ $note->is_favorite ? 'bxs-star' : 'bx-star' }}"></i>
                        </button>
                    </div>
                </div>

                <p class="text-sm text-txt-secondary line-clamp-3 mb-4">
                    {{ Str::limit(strip_tags($note->content), 120) }}
                </p>

                <div class="flex items-center justify-between pt-3 border-t-4 border-border-dark">
                    <x-badge variant="{{ $note->category === 'Important' ? 'danger' : ($note->category === 'Idea' ? 'warning' : ($note->category === 'Tutorial' ? 'info' : 'default')) }}">
                        {{ $note->category ?? 'General' }}
                    </x-badge>
                    <span class="text-xs text-txt-secondary">{{ $note->created_at->diffForHumans() }}</span>
                </div>
            </div>
        @empty
            <div class="col-span-full flex flex-col items-center justify-center py-16 bg-surface border-4 border-border-dark rounded-card shadow-hard">
                <i class="bx bx-note text-6xl text-txt-secondary"></i>
                <h3 class="text-xl font-extrabold mt-4">No notes yet</h3>
                <p class="text-txt-secondary mt-2">Create your first note</p>
                <button onclick="Livewire.dispatch('open-modal', { id: 'note-form' })"
                    class="mt-4 px-6 py-3 bg-primary text-white font-bold text-sm rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 transition-all duration-200 ease-out">
                    + New Note
                </button>
            </div>
        @endforelse
    </div>

    @if ($notes->hasPages())
        <div class="mt-6">
            {{ $notes->links() }}
        </div>
    @endif

    <x-modal id="note-form" title="Create / Edit Note">
        @livewire('note-editor')
    </x-modal>
</div>
