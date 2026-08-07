@extends('layouts.app')

@section('title', 'Developer Notes')
@section('page-title', 'Developer Notes')

@section('content')
<div x-data="noteApp()">

    {{-- Toolbar --}}
    <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 mb-8">
        <form method="GET" action="{{ route('notes.index') }}" class="flex flex-col sm:flex-row gap-4 items-start sm:items-center">
            <div class="relative flex-1 max-w-md">
                <i class="bx bx-search absolute left-4 top-1/2 -translate-y-1/2 text-txt-secondary text-xl"></i>
                <input type="text" name="search" value="{{ $search }}" placeholder="Search notes..."
                    class="w-full pl-12 pr-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none transition-colors">
            </div>
            <select name="category" class="px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium focus:border-primary outline-none">
                <option value="">All Categories</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat }}" {{ $category === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                @endforeach
            </select>
            <label class="flex items-center gap-2 cursor-pointer px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium">
                <input type="checkbox" name="favorites" value="1" {{ $showFavorites ? 'checked' : '' }}>
                <span>Favorites</span>
            </label>
            <button type="submit" class="px-4 py-3 bg-primary text-white font-bold text-sm rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 transition-all duration-200 ease-out">
                Filter
            </button>
            <button type="button" @click="openCreate()"
                class="px-6 py-3 bg-primary text-white font-bold text-sm rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 active:translate-y-1 transition-all duration-200 ease-out flex items-center gap-2 self-start">
                <i class="bx bx-plus text-lg"></i>
                New Note
            </button>
        </form>
    </div>

    {{-- Notes Card Grid --}}
    @if ($notes->isEmpty())
        <div class="flex flex-col items-center justify-center py-16 bg-surface border-4 border-border-dark rounded-card shadow-hard">
            <i class="bx bx-note text-6xl text-txt-secondary"></i>
            <h3 class="text-xl font-extrabold mt-4">No notes found</h3>
            <p class="text-txt-secondary mt-2">Create your first note to get started</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($notes as $note)
                <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover flex flex-col">
                    <div class="flex items-start justify-between mb-3">
                        <h3 class="font-bold text-base truncate flex-1 pr-2">{{ $note->title }}</h3>
                        <div class="flex items-center gap-1 flex-shrink-0">
                            {{-- Pin Toggle --}}
                            <form method="POST" action="{{ route('notes.toggle-pin', $note) }}" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="p-1.5 rounded transition-colors {{ $note->is_pinned ? 'text-yellow-acc bg-yellow-acc/10' : 'text-txt-secondary hover:bg-gray-100' }}" title="Toggle pin">
                                    <i class="bx bx-pin text-lg"></i>
                                </button>
                            </form>
                            {{-- Favorite Toggle --}}
                            <form method="POST" action="{{ route('notes.toggle-favorite', $note) }}" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="p-1.5 rounded transition-colors {{ $note->is_favorite ? 'text-red-500 bg-red-50' : 'text-txt-secondary hover:bg-gray-100' }}" title="Toggle favorite">
                                    <i class="bx bx-star text-lg"></i>
                                </button>
                            </form>
                        </div>
                    </div>

                    <p class="text-sm text-txt-secondary line-clamp-3 mb-4 flex-1">
                        {!! Str::limit(strip_tags($note->content), 120) !!}
                    </p>

                    <div class="flex flex-wrap items-center gap-1.5 mb-4">
                        @if ($note->category)
                            <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-bold border-2 border-border-dark rounded-full bg-primary/10 text-primary">
                                {{ $note->category }}
                            </span>
                        @endif
                        @if ($note->tags && count($note->tags) > 0)
                            @foreach ($note->tags as $tag)
                                <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-bold border-2 border-border-dark rounded-full bg-gray-100 text-txt-secondary">
                                    {{ $tag }}
                                </span>
                            @endforeach
                        @endif
                    </div>

                    <div class="flex items-center justify-between pt-4 border-t-4 border-border-dark mt-auto">
                        <div class="flex items-center gap-1">
                            <button type="button" @click='openEdit(@json($note))'
                                class="p-2 text-txt-secondary hover:text-primary transition-colors" title="Edit">
                                <i class="bx bx-edit text-lg"></i>
                            </button>
                            <form id="delNote-{{ $note->id }}" action="{{ route('notes.destroy', $note) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="button" @click="confirmDelete('delNote-{{ $note->id }}', @js($note->title))"
                                    class="p-2 text-txt-secondary hover:text-danger transition-colors" title="Delete">
                                    <i class="bx bx-trash text-lg"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Pagination --}}
    @if($notes->hasPages())
        <div class="mt-8">
            {{ $notes->links() }}
        </div>
    @endif

    {{-- ============================================================ --}}
    {{-- MODAL: Create / Edit Note --}}
    {{-- ============================================================ --}}
    <div x-show="showModal"
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 overflow-y-auto"
        style="display:none;">
        <div x-show="showModal"
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="scale-95 opacity-0" x-transition:enter-end="scale-100 opacity-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="scale-100 opacity-0" x-transition:leave-end="scale-95 opacity-0"
            @click.outside="closeModal()"
            class="bg-surface border-4 border-border-dark rounded-modal shadow-hard w-full max-w-lg my-6"
            style="display:none;">

            {{-- Modal Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b-4 border-border-dark">
                <h3 class="text-lg font-extrabold" x-text="editingNote ? 'Edit Note' : 'New Note'"></h3>
                <button @click="closeModal()" class="text-2xl text-txt-secondary hover:text-danger transition-colors">
                    <i class="bx bx-x"></i>
                </button>
            </div>

            {{-- Form --}}
            <form method="POST" :action="editingNote ? '/notes/'+editingNote.id : '{{ route('notes.store') }}'" class="p-6 space-y-5">
                @csrf
                <input type="hidden" name="_method" :value="editingNote ? 'PUT' : 'POST'">

                {{-- Title --}}
                <div>
                    <label class="block text-sm font-bold mb-1.5">Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" x-model="form.title" required maxlength="255"
                        placeholder="Note title"
                        class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none transition-colors">
                </div>

                {{-- Content --}}
                <div>
                    <label class="block text-sm font-bold mb-1.5">Content</label>
                    <textarea name="content" x-model="form.content" rows="5"
                        placeholder="Write your note here..."
                        class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none resize-none"></textarea>
                </div>

                {{-- Row: Category + Tags --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold mb-1.5">Category</label>
                        <input type="text" name="category" x-model="form.category" maxlength="100"
                            placeholder="E.g. Work"
                            class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium focus:border-primary outline-none transition-colors">
                    </div>
                    <div>
                        <label class="block text-sm font-bold mb-1.5">Tags</label>
                        <input type="text" name="tags" x-model="form.tags"
                            placeholder="comma, separated"
                            class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium focus:border-primary outline-none transition-colors">
                    </div>
                </div>

                {{-- Row: Pinned + Favorite --}}
                <div class="flex items-center gap-6 pt-1">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_pinned" value="1" x-model="form.is_pinned"
                            class="w-4 h-4 accent-primary rounded border-4 border-border-dark">
                        <span class="text-sm font-medium text-txt-primary">Pinned</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_favorite" value="1" x-model="form.is_favorite"
                            class="w-4 h-4 accent-primary rounded border-4 border-border-dark">
                        <span class="text-sm font-medium text-txt-primary">Favorite</span>
                    </label>
                </div>

                {{-- Actions --}}
                <div class="flex justify-end gap-3 pt-2 border-t-4 border-border-dark">
                    <button type="button" @click="closeModal()"
                        class="px-6 py-3 font-bold text-sm rounded-button border-4 border-border-dark bg-surface shadow-hard hover:-translate-y-0.5 transition-all duration-200">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-6 py-3 bg-primary text-white font-bold text-sm rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 active:translate-y-1 active:shadow-hard-pressed transition-all duration-200">
                        <i class="bx bx-save mr-1"></i> <span x-text="editingNote ? 'Update Note' : 'Save Note'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>{{-- end x-data --}}

<script>
function noteApp() {
    return {
        showModal: false,
        editingNote: null,
        form: {
            title: '',
            content: '',
            category: '',
            tags: '',
            is_pinned: false,
            is_favorite: false,
        },

        openCreate() {
            this.editingNote = null;
            this.form = { title: '', content: '', category: '', tags: '', is_pinned: false, is_favorite: false };
            this.showModal = true;
        },

        openEdit(note) {
            this.editingNote = note;
            this.form = {
                title: note.title || '',
                content: note.content || '',
                category: note.category || '',
                tags: Array.isArray(note.tags) ? note.tags.join(', ') : (note.tags || ''),
                is_pinned: !!note.is_pinned,
                is_favorite: !!note.is_favorite,
            };
            this.showModal = true;
        },

        closeModal() {
            this.showModal = false;
            this.editingNote = null;
        },

        confirmDelete(formId, noteTitle) {
            if (typeof SwalDanger !== 'undefined') {
                SwalDanger.fire({
                    title: 'Hapus Note?',
                    text: 'Note "' + noteTitle + '" akan dihapus permanen.',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal',
                    showCancelButton: true,
                }).then((result) => {
                    if (result.isConfirmed) document.getElementById(formId).submit();
                });
            } else {
                if (confirm('Hapus note "' + noteTitle + '"?')) {
                    document.getElementById(formId).submit();
                }
            }
        },
    };
}
</script>
@endsection