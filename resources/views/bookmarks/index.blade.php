@extends('layouts.app')

@section('title', 'Bookmarks')
@section('page-title', 'Bookmarks')

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div class="flex gap-3 flex-wrap items-center">
            <a href="{{ route('bookmarks.index') }}"
                class="px-4 py-2.5 rounded-button border-4 font-bold text-sm transition-all duration-200 ease-out {{ !request('category') ? 'bg-primary text-white border-border-dark shadow-hard' : 'bg-surface text-txt-primary border-border-dark hover:-translate-y-0.5 hover:shadow-hard' }}">
                All
            </a>
            @foreach ($categories as $cat)
                <a href="{{ route('bookmarks.index', ['category' => $cat]) }}"
                    class="px-4 py-2.5 rounded-button border-4 font-bold text-sm transition-all duration-200 ease-out {{ request('category') === $cat ? 'bg-primary text-white border-border-dark shadow-hard' : 'bg-surface text-txt-primary border-border-dark hover:-translate-y-0.5 hover:shadow-hard' }}">
                    {{ $cat }}
                </a>
            @endforeach
        </div>
        <div class="flex gap-3">
            <div class="relative">
                <i class="bx bx-search absolute left-3 top-1/2 -translate-y-1/2 text-txt-secondary text-lg"></i>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search bookmarks..."
                    class="w-64 pl-10 pr-4 py-2.5 rounded-input border-4 border-border-dark bg-surface text-sm font-medium focus:border-primary outline-none">
            </div>
            <button onclick="Livewire.dispatch('open-modal', { id: 'bookmark-form' })"
                class="px-5 py-2.5 bg-primary text-white font-bold text-sm rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 active:translate-y-1 transition-all duration-200 ease-out flex items-center gap-2">
                <i class="bx bx-plus text-lg"></i>
                Add Bookmark
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($bookmarks as $bookmark)
            <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover">
                <div class="flex items-start gap-4 mb-4">
                    <div class="w-12 h-12 bg-primary/10 rounded-button flex items-center justify-center flex-shrink-0">
                        @if ($bookmark->logo)
                            <img src="{{ $bookmark->logo }}" alt="{{ $bookmark->title }}" class="w-7 h-7 object-contain">
                        @else
                            <i class="bx bx-link-alt text-primary text-[24px]"></i>
                        @endif
                    </div>
                    <div class="min-w-0 flex-1">
                        <h3 class="font-bold text-sm truncate">{{ $bookmark->title }}</h3>
                        @if ($bookmark->description)
                            <p class="text-xs text-txt-secondary mt-0.5 line-clamp-2">{{ $bookmark->description }}</p>
                        @endif
                    </div>
                    <button wire:click="toggleFavorite({{ $bookmark->id }})"
                        class="text-xl flex-shrink-0 {{ $bookmark->is_favorite ? 'text-[#F59E0B]' : 'text-txt-secondary' }} hover:text-[#F59E0B] transition-colors">
                        <i class="bx {{ $bookmark->is_favorite ? 'bxs-star' : 'bx-star' }}"></i>
                    </button>
                </div>

                <div class="mb-4">
                    <x-badge variant="{{ $bookmark->category === 'Documentation' ? 'info' : ($bookmark->category === 'Tool' ? 'warning' : ($bookmark->category === 'Design' ? 'danger' : ($bookmark->category === 'Reference' ? 'success' : 'default'))) }}">
                        {{ $bookmark->category ?? 'Uncategorized' }}
                    </x-badge>
                </div>

                <div class="flex gap-2 pt-4 border-t-4 border-border-dark">
                    <a href="{{ $bookmark->url }}" target="_blank" rel="noopener noreferrer"
                        class="flex-1 px-3 py-2 bg-primary text-white font-bold text-xs rounded-button border-4 border-border-dark shadow-hard-sm hover:-translate-y-0.5 transition-all duration-200 ease-out inline-flex items-center justify-center gap-1.5">
                        <i class="bx bx-external-link"></i>
                        Open Link
                    </a>
                    <button onclick="copyBookmark('{{ $bookmark->url }}', '{{ addslashes($bookmark->title) }}')"
                        class="flex-1 px-3 py-2 bg-surface text-txt-primary font-bold text-xs rounded-button border-4 border-border-dark shadow-hard-sm hover:-translate-y-0.5 transition-all duration-200 ease-out inline-flex items-center justify-center gap-1.5">
                        <i class="bx bx-copy"></i>
                        Copy Link
                    </button>
                    <button wire:click="edit({{ $bookmark->id }})"
                        class="px-3 py-2 bg-surface text-txt-primary font-bold text-xs rounded-button border-4 border-border-dark shadow-hard-sm hover:-translate-y-0.5 transition-all duration-200 ease-out">
                        <i class="bx bx-pencil"></i>
                    </button>
                    <button wire:click="$dispatch('swal:confirm', { event: 'delete-bookmark-{{ $bookmark->id }}', title: 'Delete this bookmark?', text: '{{ addslashes($bookmark->title) }} will be removed.', confirmText: 'Yes, delete!' })"
                        class="px-3 py-2 bg-danger text-white font-bold text-xs rounded-button border-4 border-border-dark shadow-hard-sm hover:-translate-y-0.5 transition-all duration-200 ease-out">
                        <i class="bx bx-trash"></i>
                    </button>
                </div>
            </div>
        @empty
            <div class="col-span-full flex flex-col items-center justify-center py-16 bg-surface border-4 border-border-dark rounded-card shadow-hard">
                <i class="bx bx-bookmark text-6xl text-txt-secondary"></i>
                <h3 class="text-xl font-extrabold mt-4">No bookmarks yet</h3>
                <p class="text-txt-secondary mt-2">Save your first bookmark</p>
                <button onclick="Livewire.dispatch('open-modal', { id: 'bookmark-form' })"
                    class="mt-4 px-6 py-3 bg-primary text-white font-bold text-sm rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 transition-all duration-200 ease-out">
                    + Add Bookmark
                </button>
            </div>
        @endforelse
    </div>

    @if ($bookmarks->hasPages())
        <div class="mt-6">
            {{ $bookmarks->links() }}
        </div>
    @endif

    <x-modal id="bookmark-form" title="Add Bookmark">
        <form wire:submit="save" class="space-y-4">
            <x-input label="Title" name="title" placeholder="Bookmark title..." wire:model="title" />
            <x-input label="URL" name="url" placeholder="https://example.com" wire:model="url" />
            <div>
                <label for="description" class="block text-sm font-semibold text-txt-primary mb-1.5">Description</label>
                <textarea wire:model="description" id="description" rows="3"
                    class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none resize-none"
                    placeholder="Short description..."></textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <select wire:model="category"
                    class="px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium focus:border-primary outline-none">
                    <option value="">Select category</option>
                    <option value="Documentation">Documentation</option>
                    <option value="Tool">Tool</option>
                    <option value="Design">Design</option>
                    <option value="Reference">Reference</option>
                    <option value="Resource">Resource</option>
                    <option value="Other">Other</option>
                </select>
                <x-input label="Logo URL (optional)" name="logo" placeholder="https://example.com/icon.png" wire:model="logo" />
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="Livewire.dispatch('close-modal', { id: 'bookmark-form' })"
                    class="px-6 py-3 font-bold text-sm rounded-button border-4 border-border-dark bg-surface shadow-hard hover:-translate-y-0.5 transition-all duration-200 ease-out">
                    Cancel
                </button>
                <button type="submit"
                    class="px-6 py-3 bg-primary text-white font-bold text-sm rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 transition-all duration-200 ease-out">
                    Save
                </button>
            </div>
        </form>
    </x-modal>

    <x-modal id="bookmark-edit" title="Edit Bookmark">
        <form wire:submit="update" class="space-y-4">
            <x-input label="Title" name="edit_title" placeholder="Bookmark title..." wire:model="edit_title" />
            <x-input label="URL" name="edit_url" placeholder="https://example.com" wire:model="edit_url" />
            <div>
                <label for="edit_description" class="block text-sm font-semibold text-txt-primary mb-1.5">Description</label>
                <textarea wire:model="edit_description" id="edit_description" rows="3"
                    class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none resize-none"
                    placeholder="Short description..."></textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <select wire:model="edit_category"
                    class="px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium focus:border-primary outline-none">
                    <option value="">Select category</option>
                    <option value="Documentation">Documentation</option>
                    <option value="Tool">Tool</option>
                    <option value="Design">Design</option>
                    <option value="Reference">Reference</option>
                    <option value="Resource">Resource</option>
                    <option value="Other">Other</option>
                </select>
                <x-input label="Logo URL (optional)" name="edit_logo" placeholder="https://example.com/icon.png" wire:model="edit_logo" />
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="Livewire.dispatch('close-modal', { id: 'bookmark-edit' })"
                    class="px-6 py-3 font-bold text-sm rounded-button border-4 border-border-dark bg-surface shadow-hard hover:-translate-y-0.5 transition-all duration-200 ease-out">
                    Cancel
                </button>
                <button type="submit"
                    class="px-6 py-3 bg-primary text-white font-bold text-sm rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 transition-all duration-200 ease-out">
                    Update
                </button>
            </div>
        </form>
    </x-modal>

    @push('scripts')
    <script>
        function copyBookmark(url, title) {
            navigator.clipboard.writeText(url).then(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'Link copied!',
                    text: title + ' URL has been copied to clipboard.',
                    timer: 2000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end',
                    background: '#FFFFFF',
                    customClass: {
                        popup: 'border-4 border-border-dark rounded-card shadow-hard'
                    }
                });
            });
        }
    </script>
    @endpush
@endsection
