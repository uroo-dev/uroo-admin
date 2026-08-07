@extends('layouts.app')

@section('title', 'Bookmarks')
@section('page-title', 'Bookmarks')

@section('content')

{{-- Alpine component script MUST be defined BEFORE x-data is evaluated --}}
<script>
function bookmarkApp() {
    return {
        showModal: false,
        editingBookmark: null,
        form: {
            title: '',
            url: '',
            description: '',
            category: '',
            tags: '',
            logo_url: '',
            is_favorite: false,
        },

        openCreate() {
            this.editingBookmark = null;
            this.form = { title: '', url: '', description: '', category: '', tags: '', logo_url: '', is_favorite: false };
            this.showModal = true;
        },

        openEdit(data) {
            this.editingBookmark = data;
            this.form = {
                title:       data.title       || '',
                url:         data.url         || '',
                description: data.description || '',
                category:    data.category    || '',
                tags:        Array.isArray(data.tags) ? data.tags.join(', ') : (data.tags || ''),
                logo_url:    data.logo_url    || '',
                is_favorite: !!data.is_favorite,
            };
            this.showModal = true;
        },

        closeModal() {
            this.showModal = false;
            this.editingBookmark = null;
        },

        copyUrl(url) {
            navigator.clipboard.writeText(url).then(() => {
                SwalToast.fire({ icon: 'success', title: 'URL copied!' });
            }).catch(() => {
                SwalToast.fire({ icon: 'error', title: 'Failed to copy' });
            });
        },

        confirmDelete(formId, title) {
            SwalDanger.fire({
                title: 'Delete Bookmark?',
                html: `"<strong>${title}</strong>" will be permanently deleted.`,
                confirmButtonText: 'Yes, delete!',
                cancelButtonText: 'Cancel',
            }).then(result => {
                if (result.isConfirmed) {
                    document.getElementById(formId).submit();
                }
            });
        },
    };
}
</script>

<div x-data="bookmarkApp()">

    {{-- Stats Bar --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-4 flex items-center gap-3">
            <div class="w-10 h-10 bg-primary/10 border-2 border-border-dark rounded-button flex items-center justify-center flex-shrink-0">
                <i class="bx bx-bookmark text-primary text-xl"></i>
            </div>
            <div>
                <p class="text-2xl font-extrabold leading-none">{{ $totalCount }}</p>
                <p class="text-xs font-semibold text-txt-secondary mt-0.5">Total</p>
            </div>
        </div>
        <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-4 flex items-center gap-3">
            <div class="w-10 h-10 bg-yellow-100 border-2 border-border-dark rounded-button flex items-center justify-center flex-shrink-0">
                <i class="bx bxs-star text-yellow-400 text-xl"></i>
            </div>
            <div>
                <p class="text-2xl font-extrabold leading-none">{{ $favoritesCount }}</p>
                <p class="text-xs font-semibold text-txt-secondary mt-0.5">Favorites</p>
            </div>
        </div>
        <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-4 flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-50 border-2 border-border-dark rounded-button flex items-center justify-center flex-shrink-0">
                <i class="bx bx-grid-alt text-blue-500 text-xl"></i>
            </div>
            <div>
                <p class="text-2xl font-extrabold leading-none">{{ $categories->count() }}</p>
                <p class="text-xs font-semibold text-txt-secondary mt-0.5">Categories</p>
            </div>
        </div>
        <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-4 flex items-center gap-3">
            <div class="w-10 h-10 bg-green-50 border-2 border-border-dark rounded-button flex items-center justify-center flex-shrink-0">
                <i class="bx bx-list-ul text-green-500 text-xl"></i>
            </div>
            <div>
                <p class="text-2xl font-extrabold leading-none">{{ $bookmarks->total() }}</p>
                <p class="text-xs font-semibold text-txt-secondary mt-0.5">Showing</p>
            </div>
        </div>
    </div>

    {{-- Toolbar Card --}}
    <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-5 mb-6">
        <form method="GET" action="{{ route('bookmarks.index') }}" id="filterForm"
              class="flex flex-col sm:flex-row gap-3 items-start sm:items-center flex-wrap">

            {{-- Search --}}
            <div class="relative flex-1 min-w-[180px]">
                <i class="bx bx-search absolute left-4 top-1/2 -translate-y-1/2 text-txt-secondary text-xl"></i>
                <input type="text" name="search" value="{{ $search }}" placeholder="Search bookmarks..."
                    class="w-full pl-12 pr-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none transition-colors">
            </div>

            {{-- Category --}}
            <select name="category"
                class="px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium focus:border-primary outline-none">
                <option value="">All Categories</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat }}" {{ $category === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                @endforeach
            </select>

            {{-- Favorites toggle --}}
            <label class="flex items-center gap-2 cursor-pointer px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium select-none">
                <input type="checkbox" name="favorites" value="1" {{ $showFavorites ? 'checked' : '' }}
                    onchange="document.getElementById('filterForm').submit()">
                <i class="bx bxs-star text-yellow-400"></i>
                <span>Favorites</span>
            </label>

            <button type="submit"
                class="px-4 py-3 bg-surface font-bold text-sm rounded-button border-4 border-border-dark shadow-hard-sm hover:-translate-y-0.5 transition-all duration-200 ease-out">
                <i class="bx bx-search mr-1"></i> Search
            </button>

            <button type="button" @click="openCreate()"
                class="px-6 py-3 bg-primary text-white font-bold text-sm rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 active:translate-y-1 transition-all duration-200 ease-out flex items-center gap-2 sm:ml-auto">
                <i class="bx bx-plus text-lg"></i>
                Add Bookmark
            </button>
        </form>

        {{-- Category Pills --}}
        @if ($categories->count() > 0)
        <div class="flex flex-wrap gap-2 mt-4 pt-4 border-t-2 border-gray-100">
            <a href="{{ route('bookmarks.index', array_filter(['search' => $search, 'favorites' => $showFavorites ? '1' : null])) }}"
                class="px-3 py-1 rounded-button border-2 border-border-dark font-bold text-xs transition-all {{ !$category ? 'bg-primary text-white shadow-hard-sm' : 'bg-bgmain text-txt-primary hover:-translate-y-0.5 hover:shadow-hard-sm' }}">
                All
            </a>
            @foreach ($categories as $cat)
                <a href="{{ route('bookmarks.index', array_filter(['category' => $cat, 'search' => $search, 'favorites' => $showFavorites ? '1' : null])) }}"
                    class="px-3 py-1 rounded-button border-2 border-border-dark font-bold text-xs transition-all {{ $category === $cat ? 'bg-primary text-white shadow-hard-sm' : 'bg-bgmain text-txt-primary hover:-translate-y-0.5 hover:shadow-hard-sm' }}">
                    {{ $cat }}
                </a>
            @endforeach
        </div>
        @endif
    </div>

    {{-- Flash --}}
    @if (session('success'))
        <div class="mb-5 px-5 py-4 bg-surface border-4 border-green-500 rounded-card shadow-hard flex items-center gap-3">
            <i class="bx bx-check-circle text-green-500 text-2xl"></i>
            <span class="font-semibold text-sm">{{ session('success') }}</span>
        </div>
    @endif

    {{-- Grid --}}
    @if ($bookmarks->isEmpty())
        <div class="flex flex-col items-center justify-center py-20 bg-surface border-4 border-border-dark rounded-card shadow-hard">
            <i class="bx bx-bookmark text-7xl text-txt-secondary"></i>
            <h3 class="text-xl font-extrabold mt-4">No bookmarks found</h3>
            <p class="text-txt-secondary mt-2 text-sm">
                @if ($search || $category || $showFavorites)
                    Try clearing your filters
                @else
                    Save your first bookmark to get started
                @endif
            </p>
            <button @click="openCreate()"
                class="mt-5 px-6 py-3 bg-primary text-white font-bold text-sm rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 transition-all duration-200 ease-out">
                <i class="bx bx-plus mr-1"></i> Add Bookmark
            </button>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($bookmarks as $bookmark)
            <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-5 flex flex-col transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover">

                {{-- Header --}}
                <div class="flex items-start gap-3 mb-3">
                    <div class="w-11 h-11 bg-primary/10 border-2 border-border-dark rounded-button flex items-center justify-center flex-shrink-0 overflow-hidden">
                        @if ($bookmark->logo_url)
                            <img src="{{ $bookmark->logo_url }}" alt=""
                                 class="w-7 h-7 object-contain"
                                 onerror="this.parentElement.innerHTML='<i class=\'bx bx-link-alt text-primary text-xl\'></i>'">
                        @else
                            <i class="bx bx-link-alt text-primary text-xl"></i>
                        @endif
                    </div>
                    <div class="min-w-0 flex-1">
                        <h3 class="font-extrabold text-sm leading-snug line-clamp-2">{{ $bookmark->title }}</h3>
                        <p class="text-xs text-txt-secondary mt-0.5 truncate">{{ parse_url($bookmark->url, PHP_URL_HOST) }}</p>
                    </div>
                    {{-- Favorite --}}
                    <form method="POST" action="{{ route('bookmarks.toggle-favorite', $bookmark) }}" class="flex-shrink-0">
                        @csrf @method('PATCH')
                        <button type="submit" title="Toggle favorite"
                            class="p-1.5 rounded transition-colors {{ $bookmark->is_favorite ? 'text-yellow-400' : 'text-txt-secondary hover:text-yellow-400' }}">
                            <i class="bx {{ $bookmark->is_favorite ? 'bxs-star' : 'bx-star' }} text-xl"></i>
                        </button>
                    </form>
                </div>

                {{-- Description --}}
                @if ($bookmark->description)
                    <p class="text-xs text-txt-secondary line-clamp-2 mb-3 leading-relaxed">{{ $bookmark->description }}</p>
                @endif

                {{-- Badges --}}
                <div class="flex flex-wrap gap-1.5 mb-4 flex-1">
                    @if ($bookmark->category)
                        <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-bold border-2 border-border-dark rounded-full bg-primary/10 text-primary">
                            {{ $bookmark->category }}
                        </span>
                    @endif
                    @if ($bookmark->tags && count($bookmark->tags) > 0)
                        @foreach ($bookmark->tags as $tag)
                            <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-bold border-2 border-border-dark rounded-full bg-gray-100 text-txt-secondary">
                                #{{ $tag }}
                            </span>
                        @endforeach
                    @endif
                    @if (!$bookmark->category && (!$bookmark->tags || count($bookmark->tags) === 0))
                        <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-bold border-2 border-border-dark rounded-full bg-gray-100 text-txt-secondary">
                            Uncategorized
                        </span>
                    @endif
                </div>

                {{-- Actions --}}
                <div class="flex gap-2 pt-3 border-t-4 border-border-dark mt-auto">
                    {{-- Open --}}
                    <a href="{{ $bookmark->url }}" target="_blank" rel="noopener noreferrer"
                        class="flex-1 px-3 py-2 bg-primary text-white font-bold text-xs rounded-button border-4 border-border-dark shadow-hard-sm hover:-translate-y-0.5 transition-all duration-200 ease-out inline-flex items-center justify-center gap-1.5">
                        <i class="bx bx-link-external text-sm"></i> Open
                    </a>
                    {{-- Copy --}}
                    <button type="button" @click="copyUrl('{{ addslashes($bookmark->url) }}')"
                        class="flex-1 px-3 py-2 bg-surface text-txt-primary font-bold text-xs rounded-button border-4 border-border-dark shadow-hard-sm hover:-translate-y-0.5 transition-all duration-200 ease-out inline-flex items-center justify-center gap-1.5">
                        <i class="bx bx-copy text-sm"></i> Copy
                    </button>
                    {{-- Edit --}}
                    <button type="button"
                        @click="openEdit({
                            id:          {{ $bookmark->id }},
                            title:       {{ Js::from($bookmark->title) }},
                            url:         {{ Js::from($bookmark->url) }},
                            description: {{ Js::from($bookmark->description) }},
                            category:    {{ Js::from($bookmark->category) }},
                            tags:        {{ Js::from($bookmark->tags) }},
                            logo_url:    {{ Js::from($bookmark->logo_url) }},
                            is_favorite: {{ $bookmark->is_favorite ? 'true' : 'false' }}
                        })"
                        class="px-3 py-2 bg-surface text-txt-primary font-bold text-xs rounded-button border-4 border-border-dark shadow-hard-sm hover:-translate-y-0.5 transition-all duration-200 ease-out"
                        title="Edit">
                        <i class="bx bx-edit-alt text-sm"></i>
                    </button>
                    {{-- Delete --}}
                    <form id="del-{{ $bookmark->id }}" method="POST" action="{{ route('bookmarks.destroy', $bookmark) }}">
                        @csrf @method('DELETE')
                        <button type="button"
                            @click="confirmDelete('del-{{ $bookmark->id }}', {{ Js::from($bookmark->title) }})"
                            class="px-3 py-2 bg-danger text-white font-bold text-xs rounded-button border-4 border-border-dark shadow-hard-sm hover:-translate-y-0.5 transition-all duration-200 ease-out"
                            title="Delete">
                            <i class="bx bx-trash-alt text-[#EF4444] text-sm"></i>
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    @endif

    {{-- Pagination --}}
    @if ($bookmarks->hasPages())
        <div class="mt-8">{{ $bookmarks->links() }}</div>
    @endif

    {{-- ============================================================
         MODAL: Add / Edit Bookmark
    ============================================================ --}}
    <div x-show="showModal"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @keydown.escape.window="closeModal()"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 overflow-y-auto"
        style="display:none;">

        <div @click.stop
            x-show="showModal"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="scale-95 opacity-0"
            x-transition:enter-end="scale-100 opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="scale-100 opacity-100"
            x-transition:leave-end="scale-95 opacity-0"
            class="bg-surface border-4 border-border-dark rounded-modal shadow-hard w-full max-w-lg my-4"
            style="display:none;">

            {{-- Modal Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b-4 border-border-dark">
                <h3 class="text-lg font-extrabold" x-text="editingBookmark ? 'Edit Bookmark' : 'Add Bookmark'"></h3>
                <button @click="closeModal()" class="text-2xl text-txt-secondary hover:text-danger transition-colors">
                    <i class="bx bx-x"></i>
                </button>
            </div>

            {{-- Form --}}
            <form method="POST"
                  :action="editingBookmark ? '/bookmarks/' + editingBookmark.id : '{{ route('bookmarks.store') }}'"
                  class="p-6 space-y-4">
                @csrf
                <input type="hidden" name="_method" :value="editingBookmark ? 'PUT' : 'POST'">

                {{-- Title --}}
                <div>
                    <label class="block text-sm font-bold mb-1.5">Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" x-model="form.title" required maxlength="255"
                        placeholder="E.g. Laravel Docs"
                        class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none transition-colors">
                </div>

                {{-- URL --}}
                <div>
                    <label class="block text-sm font-bold mb-1.5">URL <span class="text-danger">*</span></label>
                    <input type="url" name="url" x-model="form.url" required maxlength="2048"
                        placeholder="https://example.com"
                        class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none transition-colors">
                </div>

                {{-- Description --}}
                <div>
                    <label class="block text-sm font-bold mb-1.5">Description</label>
                    <textarea name="description" x-model="form.description" rows="2" maxlength="1000"
                        placeholder="Short description..."
                        class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none resize-none transition-colors"></textarea>
                </div>

                {{-- Category + Tags --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold mb-1.5">Category</label>
                        <input type="text" name="category" x-model="form.category" maxlength="100"
                            placeholder="E.g. Documentation"
                            list="cat-list"
                            class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium focus:border-primary outline-none transition-colors">
                        <datalist id="cat-list">
                            @foreach ($categories as $cat)
                                <option value="{{ $cat }}">
                            @endforeach
                            <option value="Documentation">
                            <option value="Tool">
                            <option value="Design">
                            <option value="Reference">
                            <option value="Resource">
                            <option value="Other">
                        </datalist>
                    </div>
                    <div>
                        <label class="block text-sm font-bold mb-1.5">Tags</label>
                        <input type="text" name="tags_input" x-model="form.tags"
                            placeholder="tag1, tag2, tag3"
                            class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium focus:border-primary outline-none transition-colors">
                    </div>
                </div>

                {{-- Logo URL --}}
                <div>
                    <label class="block text-sm font-bold mb-1.5">Logo URL <span class="text-txt-secondary font-normal text-xs">(optional)</span></label>
                    <input type="url" name="logo_url" x-model="form.logo_url" maxlength="2048"
                        placeholder="https://example.com/favicon.ico"
                        class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none transition-colors">
                </div>

                {{-- Favorite --}}
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_favorite" value="1" x-model="form.is_favorite"
                        class="w-4 h-4 accent-primary">
                    <span class="text-sm font-medium">Mark as favorite</span>
                    <i class="bx bxs-star text-yellow-400 text-base"></i>
                </label>

                {{-- Footer --}}
                <div class="flex justify-end gap-3 pt-2 border-t-4 border-border-dark">
                    <button type="button" @click="closeModal()"
                        class="px-6 py-3 font-bold text-sm rounded-button border-4 border-border-dark bg-surface shadow-hard hover:-translate-y-0.5 transition-all duration-200">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-6 py-3 bg-primary text-white font-bold text-sm rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 active:translate-y-1 active:shadow-hard-pressed transition-all duration-200">
                        <i class="bx bx-save mr-1"></i>
                        <span x-text="editingBookmark ? 'Update' : 'Save'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>{{-- end x-data --}}
@endsection
