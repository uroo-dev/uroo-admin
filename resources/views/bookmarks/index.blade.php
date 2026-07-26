@extends('layouts.app')

@section('title', 'Bookmarks')
@section('page-title', 'Bookmarks')

@section('content')
<div x-data="{
    addOpen: false,
    editOpen: false,
    editingId: null,
    editingTitle: '',
    editingUrl: '',
    editingDescription: '',
    editingCategory: '',
    editingLogoUrl: ''
}">

    {{-- Category Filter --}}
    <div class="flex flex-wrap gap-3 mb-6">
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

    {{-- Toolbar --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <form method="GET" action="{{ route('bookmarks.index') }}" class="flex gap-3 flex-wrap items-center">
            <div class="relative">
                <i class="bx bx-search absolute left-3 top-1/2 -translate-y-1/2 text-txt-secondary text-lg"></i>
                <input type="text" name="search" value="{{ $search }}" placeholder="Search bookmarks..."
                    class="w-64 pl-10 pr-4 py-2.5 rounded-input border-4 border-border-dark bg-surface text-sm font-medium focus:border-primary outline-none">
            </div>
            <label class="flex items-center gap-2 cursor-pointer px-4 py-2.5 rounded-input border-4 border-border-dark bg-surface text-sm font-medium">
                <input type="checkbox" name="favorites" value="1" {{ $showFavorites ? 'checked' : '' }}>
                <span>Favorites</span>
            </label>
        </form>
        <div class="flex gap-3">
            <button @click="addOpen = true"
                class="px-5 py-2.5 bg-primary text-white font-bold text-sm rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 active:translate-y-1 transition-all duration-200 ease-out flex items-center gap-2">
                <i class="bx bx-plus text-lg"></i>
                Add Bookmark
            </button>
        </div>
    </div>

    {{-- Bookmark Cards Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($bookmarks as $bookmark)
            <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover">
                <div class="flex items-start gap-4 mb-4">
                    <div class="w-12 h-12 bg-primary/10 rounded-button flex items-center justify-center flex-shrink-0">
                        @if ($bookmark->logo_url)
                            <img src="{{ $bookmark->logo_url }}" alt="{{ $bookmark->title }}" class="w-7 h-7 object-contain">
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
                    <form method="POST" action="{{ route('bookmarks.toggle-favorite', $bookmark) }}" class="flex-shrink-0">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                            class="text-xl transition-colors {{ $bookmark->is_favorite ? 'text-[#F59E0B]' : 'text-txt-secondary hover:text-[#F59E0B]' }}">
                            <i class="bx {{ $bookmark->is_favorite ? 'bxs-star' : 'bx-star' }}"></i>
                        </button>
                    </form>
                </div>

                <div class="mb-4">
                    <span class="inline-flex items-center px-3 py-1 text-xs font-bold border-2 border-border-dark rounded-full bg-gray-200 text-txt-primary">
                        {{ $bookmark->category ?? 'Uncategorized' }}
                    </span>
                </div>

                <div class="flex gap-2 pt-4 border-t-4 border-border-dark">
                    <a href="{{ $bookmark->url }}" target="_blank" rel="noopener noreferrer"
                        class="flex-1 px-3 py-2 bg-primary text-white font-bold text-xs rounded-button border-4 border-border-dark shadow-hard-sm hover:-translate-y-0.5 transition-all duration-200 ease-out inline-flex items-center justify-center gap-1.5">
                        <i class="bx bx-external-link"></i>
                        Open
                    </a>
                    <button onclick="copyBookmark('{{ addslashes($bookmark->url) }}')"
                        class="flex-1 px-3 py-2 bg-surface text-txt-primary font-bold text-xs rounded-button border-4 border-border-dark shadow-hard-sm hover:-translate-y-0.5 transition-all duration-200 ease-out inline-flex items-center justify-center gap-1.5">
                        <i class="bx bx-copy"></i>
                        Copy
                    </button>
                    <button @click="
                        const b = {!! json_encode(['id' => $bookmark->id, 'title' => $bookmark->title, 'url' => $bookmark->url, 'description' => $bookmark->description ?? '', 'category' => $bookmark->category ?? '', 'logo_url' => $bookmark->logo_url ?? '']) !!};
                        editingId = b.id;
                        editingTitle = b.title;
                        editingUrl = b.url;
                        editingDescription = b.description;
                        editingCategory = b.category;
                        editingLogoUrl = b.logo_url;
                        editOpen = true;
                    "
                        class="px-3 py-2 bg-surface text-txt-primary font-bold text-xs rounded-button border-4 border-border-dark shadow-hard-sm hover:-translate-y-0.5 transition-all duration-200 ease-out">
                        <i class="bx bx-pencil"></i>
                    </button>
                    <form method="POST" action="{{ route('bookmarks.destroy', $bookmark) }}" onsubmit="return confirm('Are you sure you want to delete this bookmark?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="px-3 py-2 bg-danger text-white font-bold text-xs rounded-button border-4 border-border-dark shadow-hard-sm hover:-translate-y-0.5 transition-all duration-200 ease-out">
                            <i class="bx bx-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-span-full flex flex-col items-center justify-center py-16 bg-surface border-4 border-border-dark rounded-card shadow-hard">
                <i class="bx bx-bookmark text-6xl text-txt-secondary"></i>
                <h3 class="text-xl font-extrabold mt-4">No bookmarks yet</h3>
                <p class="text-txt-secondary mt-2">Save your first bookmark</p>
                <button @click="addOpen = true"
                    class="mt-4 px-6 py-3 bg-primary text-white font-bold text-sm rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 transition-all duration-200 ease-out">
                    + Add Bookmark
                </button>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    {{ $bookmarks->links() }}

    {{-- Add Bookmark Modal --}}
    <div x-show="addOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
        style="display: none;"
        @click.outside="addOpen = false">
        <div x-show="addOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="scale-95 opacity-0"
            x-transition:enter-end="scale-100 opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="scale-100 opacity-100"
            x-transition:leave-end="scale-95 opacity-0"
            @click.outside="addOpen = false"
            class="bg-surface border-4 border-border-dark rounded-modal shadow-hard w-full max-w-lg animate-scale-in"
            style="display: none;">
            <div class="flex items-center justify-between px-6 py-4 border-b-4 border-border-dark">
                <h3 class="text-lg font-extrabold">Add Bookmark</h3>
                <button @click="addOpen = false" class="text-2xl text-txt-secondary hover:text-danger transition-colors">
                    <i class="bx bx-x"></i>
                </button>
            </div>
            <form method="POST" action="{{ route('bookmarks.store') }}" class="p-6 space-y-4">
                @csrf
                <div>
                    <label for="add-title" class="block text-sm font-semibold text-txt-primary mb-1.5">Title</label>
                    <input type="text" id="add-title" name="title" required
                        class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none">
                </div>
                <div>
                    <label for="add-url" class="block text-sm font-semibold text-txt-primary mb-1.5">URL</label>
                    <input type="url" id="add-url" name="url" required
                        class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none"
                        placeholder="https://example.com">
                </div>
                <div>
                    <label for="add-description" class="block text-sm font-semibold text-txt-primary mb-1.5">Description</label>
                    <textarea id="add-description" name="description" rows="3"
                        class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none resize-none"
                        placeholder="Short description..."></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="add-category" class="block text-sm font-semibold text-txt-primary mb-1.5">Category</label>
                        <select id="add-category" name="category"
                            class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium focus:border-primary outline-none">
                            <option value="">Select category</option>
                            <option value="Documentation">Documentation</option>
                            <option value="Tool">Tool</option>
                            <option value="Design">Design</option>
                            <option value="Reference">Reference</option>
                            <option value="Resource">Resource</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div>
                        <label for="add-logo_url" class="block text-sm font-semibold text-txt-primary mb-1.5">Logo URL (optional)</label>
                        <input type="url" id="add-logo_url" name="logo_url"
                            class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none"
                            placeholder="https://example.com/icon.png">
                    </div>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" @click="addOpen = false"
                        class="px-6 py-3 font-bold text-sm rounded-button border-4 border-border-dark bg-surface shadow-hard hover:-translate-y-0.5 transition-all duration-200 ease-out">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-6 py-3 bg-primary text-white font-bold text-sm rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 transition-all duration-200 ease-out">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Edit Bookmark Modal --}}
    <div x-show="editOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
        style="display: none;"
        @click.outside="editOpen = false">
        <div x-show="editOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="scale-95 opacity-0"
            x-transition:enter-end="scale-100 opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="scale-100 opacity-100"
            x-transition:leave-end="scale-95 opacity-0"
            @click.outside="editOpen = false"
            class="bg-surface border-4 border-border-dark rounded-modal shadow-hard w-full max-w-lg animate-scale-in"
            style="display: none;">
            <div class="flex items-center justify-between px-6 py-4 border-b-4 border-border-dark">
                <h3 class="text-lg font-extrabold">Edit Bookmark</h3>
                <button @click="editOpen = false" class="text-2xl text-txt-secondary hover:text-danger transition-colors">
                    <i class="bx bx-x"></i>
                </button>
            </div>
            <form method="POST" :action="`/bookmarks/` + editingId" id="editBookmarkForm" class="p-6 space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label for="edit-title" class="block text-sm font-semibold text-txt-primary mb-1.5">Title</label>
                    <input type="text" id="edit-title" name="title" x-model="editingTitle" required
                        class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none">
                </div>
                <div>
                    <label for="edit-url" class="block text-sm font-semibold text-txt-primary mb-1.5">URL</label>
                    <input type="url" id="edit-url" name="url" x-model="editingUrl" required
                        class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none"
                        placeholder="https://example.com">
                </div>
                <div>
                    <label for="edit-description" class="block text-sm font-semibold text-txt-primary mb-1.5">Description</label>
                    <textarea id="edit-description" name="description" rows="3" x-model="editingDescription"
                        class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none resize-none"
                        placeholder="Short description..."></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="edit-category" class="block text-sm font-semibold text-txt-primary mb-1.5">Category</label>
                        <select id="edit-category" name="category" x-model="editingCategory"
                            class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium focus:border-primary outline-none">
                            <option value="">Select category</option>
                            <option value="Documentation">Documentation</option>
                            <option value="Tool">Tool</option>
                            <option value="Design">Design</option>
                            <option value="Reference">Reference</option>
                            <option value="Resource">Resource</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div>
                        <label for="edit-logo_url" class="block text-sm font-semibold text-txt-primary mb-1.5">Logo URL (optional)</label>
                        <input type="url" id="edit-logo_url" name="logo_url" x-model="editingLogoUrl"
                            class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none"
                            placeholder="https://example.com/icon.png">
                    </div>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" @click="editOpen = false"
                        class="px-6 py-3 font-bold text-sm rounded-button border-4 border-border-dark bg-surface shadow-hard hover:-translate-y-0.5 transition-all duration-200 ease-out">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-6 py-3 bg-primary text-white font-bold text-sm rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 transition-all duration-200 ease-out">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function copyBookmark(url) {
    navigator.clipboard.writeText(url).then(function() {
        Swal.fire({
            icon: 'success',
            title: 'Link copied!',
            text: 'The bookmark URL has been copied to clipboard.',
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