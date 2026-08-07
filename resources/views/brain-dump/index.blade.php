@extends('layouts.app')
@section('title', 'Brain Dump')
@section('page-title', 'Brain Dump')

@section('content')

{{-- Alpine component — defined BEFORE x-data --}}
<script>
function brainDumpApp() {
    return {
        showModal: false,
        editingDump: null,
        form: { content: '', is_pinned: false },

        openCreate() {
            this.editingDump = null;
            this.form = { content: '', is_pinned: false };
            this.showModal = true;
            this.$nextTick(() => this.$refs.contentArea && this.$refs.contentArea.focus());
        },

        openEdit(data) {
            this.editingDump = data;
            this.form = {
                content:   data.content   || '',
                is_pinned: !!data.is_pinned,
            };
            this.showModal = true;
        },

        closeModal() {
            this.showModal = false;
            this.editingDump = null;
        },

        confirmDelete(formId) {
            SwalDanger.fire({
                title: 'Delete Brain Dump?',
                text: 'This thought will be permanently deleted.',
                confirmButtonText: 'Yes, delete!',
                cancelButtonText: 'Cancel',
            }).then(result => {
                if (result.isConfirmed) document.getElementById(formId).submit();
            });
        },
    };
}
</script>

<div x-data="brainDumpApp()">

    @php
        $totalCount    = \App\Models\BrainDump::where('user_id', auth()->id())->where('is_archived', false)->count();
        $pinnedCount   = \App\Models\BrainDump::where('user_id', auth()->id())->where('is_pinned', true)->where('is_archived', false)->count();
        $archivedCount = \App\Models\BrainDump::where('user_id', auth()->id())->where('is_archived', true)->count();
        $todayCount    = \App\Models\BrainDump::where('user_id', auth()->id())->whereDate('created_at', today())->count();
    @endphp

    {{-- Stats Bar --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-4 flex items-center gap-3">
            <div class="w-10 h-10 bg-primary/10 border-2 border-border-dark rounded-button flex items-center justify-center flex-shrink-0">
                <i class="bx bx-brain text-primary text-xl"></i>
            </div>
            <div>
                <p class="text-2xl font-extrabold leading-none">{{ $totalCount }}</p>
                <p class="text-xs font-semibold text-txt-secondary mt-0.5">Total</p>
            </div>
        </div>
        <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-4 flex items-center gap-3">
            <div class="w-10 h-10 bg-yellow-50 border-2 border-border-dark rounded-button flex items-center justify-center flex-shrink-0">
                <i class="bx bx-pin text-yellow-500 text-xl"></i>
            </div>
            <div>
                <p class="text-2xl font-extrabold leading-none">{{ $pinnedCount }}</p>
                <p class="text-xs font-semibold text-txt-secondary mt-0.5">Pinned</p>
            </div>
        </div>
        <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-4 flex items-center gap-3">
            <div class="w-10 h-10 bg-gray-100 border-2 border-border-dark rounded-button flex items-center justify-center flex-shrink-0">
                <i class="bx bx-archive text-gray-500 text-xl"></i>
            </div>
            <div>
                <p class="text-2xl font-extrabold leading-none">{{ $archivedCount }}</p>
                <p class="text-xs font-semibold text-txt-secondary mt-0.5">Archived</p>
            </div>
        </div>
        <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-4 flex items-center gap-3">
            <div class="w-10 h-10 bg-green-50 border-2 border-border-dark rounded-button flex items-center justify-center flex-shrink-0">
                <i class="bx bx-calendar-check text-green-500 text-xl"></i>
            </div>
            <div>
                <p class="text-2xl font-extrabold leading-none">{{ $todayCount }}</p>
                <p class="text-xs font-semibold text-txt-secondary mt-0.5">Today</p>
            </div>
        </div>
    </div>

    {{-- Quick Add + Search Card --}}
    <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-5 mb-6 flex flex-col lg:flex-row gap-4 items-stretch">

        {{-- Quick Dump (kiri) --}}
        <form method="POST" action="{{ route('brain-dumps.store') }}" class="flex gap-3 items-center flex-1">
            @csrf
            <div class="w-9 h-9 bg-primary/10 border-2 border-border-dark rounded-button flex items-center justify-center flex-shrink-0">
                <i class="bx bx-send text-primary text-lg"></i>
            </div>
            <textarea name="content" rows="1" required maxlength="10000"
                class="flex-1 px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none resize-none transition-colors"
                style="height: 50px;"
                placeholder="Tulis pikiranmu di sini..."></textarea>
            <button type="submit"
                class="px-5 py-3 bg-primary text-white font-bold text-sm rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 active:translate-y-1 transition-all flex items-center gap-2 flex-shrink-0">
                <i class="bx bx-send text-lg"></i> Dump it
            </button>
        </form>

        {{-- Divider vertikal --}}
        <div class="hidden lg:flex items-center px-1">
            <div class="w-0.5 h-full bg-gray-100 min-h-[50px]"></div>
        </div>
        <div class="lg:hidden h-0.5 w-full bg-gray-100"></div>

        {{-- Search (kanan) --}}
        <form method="GET" action="{{ route('brain-dumps.index') }}"
              class="flex gap-3 items-center lg:w-80">
            <div class="relative flex-1">
                <i class="bx bx-search absolute left-3 top-1/2 -translate-y-1/2 text-txt-secondary text-lg"></i>
                <input type="text" name="search" value="{{ $search }}"
                    placeholder="Search..."
                    class="w-full pl-10 pr-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none transition-colors">
            </div>
            @if ($search)
                <a href="{{ route('brain-dumps.index') }}"
                    class="p-3 bg-surface rounded-button border-4 border-border-dark shadow-hard-sm hover:-translate-y-0.5 transition-all text-txt-secondary hover:text-danger flex-shrink-0">
                    <i class="bx bx-x text-lg"></i>
                </a>
            @else
                <button type="submit"
                    class="p-3 bg-surface rounded-button border-4 border-border-dark shadow-hard-sm hover:-translate-y-0.5 transition-all flex-shrink-0">
                    <i class="bx bx-search text-lg"></i>
                </button>
            @endif
        </form>
    </div>

    {{-- Flash --}}
    @if (session('success'))
        <div class="mb-5 px-5 py-4 bg-surface border-4 border-green-500 rounded-card shadow-hard flex items-center gap-3">
            <i class="bx bx-check-circle text-green-500 text-2xl"></i>
            <span class="font-semibold text-sm">{{ session('success') }}</span>
        </div>
    @endif

    {{-- Pinned Section --}}
    @if ($pinnedDumps->isNotEmpty())
        <div class="flex items-center gap-2 mb-4">
            <i class="bx bx-pin text-yellow-500 text-xl"></i>
            <h2 class="text-base font-extrabold">Pinned</h2>
            <span class="ml-1 px-2 py-0.5 text-xs font-bold border-2 border-border-dark rounded-full bg-yellow-100 text-yellow-700">
                {{ $pinnedDumps->count() }}
            </span>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 mb-8">
            @foreach ($pinnedDumps as $dump)
                <div class="bg-yellow-50 border-4 border-border-dark rounded-card shadow-hard p-5 flex flex-col transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover">
                    {{-- Pin badge --}}
                    <div class="flex items-start justify-between gap-2 mb-3">
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-bold border-2 border-yellow-500 rounded-full bg-yellow-100 text-yellow-700">
                            <i class="bx bx-pin text-xs"></i> Pinned
                        </span>
                        <span class="text-xs text-txt-secondary">{{ $dump->created_at->diffForHumans() }}</span>
                    </div>

                    <p class="text-sm text-txt-primary line-clamp-4 mb-4 flex-1 leading-relaxed whitespace-pre-line">{{ $dump->content }}</p>

                    <div class="flex items-center gap-1 pt-3 border-t-4 border-border-dark">
                        {{-- Unpin --}}
                        <form method="POST" action="{{ route('brain-dumps.toggle-pin', $dump) }}">
                            @csrf @method('PATCH')
                            <button type="submit" title="Unpin"
                                class="p-2 rounded-button border-2 border-yellow-400 bg-yellow-100 text-yellow-600 hover:bg-yellow-200 transition-colors">
                                <i class="bx bx-pin text-base"></i>
                            </button>
                        </form>
                        {{-- Archive --}}
                        <form method="POST" action="{{ route('brain-dumps.toggle-archive', $dump) }}">
                            @csrf @method('PATCH')
                            <button type="submit" title="Archive"
                                class="p-2 rounded-button border-2 border-border-dark bg-surface text-txt-secondary hover:text-gray-600 hover:bg-gray-100 transition-colors">
                                <i class="bx bx-archive text-base"></i>
                            </button>
                        </form>
                        {{-- Edit --}}
                        <button type="button"
                            @click="openEdit({ id: {{ $dump->id }}, content: {{ Js::from($dump->content) }}, is_pinned: true })"
                            title="Edit"
                            class="p-2 rounded-button border-2 border-border-dark bg-surface text-txt-secondary hover:text-primary hover:bg-primary/10 transition-colors">
                            <i class="bx bx-edit-alt text-base"></i>
                        </button>
                        {{-- Delete --}}
                        <form id="del-{{ $dump->id }}" method="POST" action="{{ route('brain-dumps.destroy', $dump) }}" class="ml-auto">
                            @csrf @method('DELETE')
                            <button type="button"
                                @click="confirmDelete('del-{{ $dump->id }}')"
                                title="Delete"
                                class="p-2 rounded-button border-2 border-border-dark bg-surface text-txt-secondary hover:text-danger hover:bg-red-50 hover:border-danger transition-colors">
                                <i class="bx bx-trash-alt text-base"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- All Dumps --}}
    <div class="flex items-center gap-2 mb-4">
        <i class="bx bx-note text-primary text-xl"></i>
        <h2 class="text-base font-extrabold">All Notes</h2>
        @if ($search)
            <span class="ml-1 px-2.5 py-0.5 text-xs font-bold border-2 border-border-dark rounded-full bg-primary/10 text-primary">
                "{{ $search }}"
            </span>
        @endif
    </div>

    @if ($dumps->isEmpty())
        <div class="flex flex-col items-center justify-center py-20 bg-surface border-4 border-border-dark rounded-card shadow-hard">
            <i class="bx bx-brain text-7xl text-txt-secondary"></i>
            <h3 class="text-xl font-extrabold mt-4">No brain dumps yet</h3>
            <p class="text-txt-secondary mt-2 text-sm">
                @if ($search)
                    No results for "{{ $search }}"
                @else
                    Type your thoughts in the quick dump box above
                @endif
            </p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 mb-6">
            @foreach ($dumps as $dump)
                <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-5 flex flex-col transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover">

                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs text-txt-secondary">{{ $dump->created_at->diffForHumans() }}</span>
                        @if ($dump->updated_at->ne($dump->created_at))
                            <span class="text-xs text-txt-secondary italic">edited</span>
                        @endif
                    </div>

                    <p class="text-sm text-txt-primary line-clamp-4 mb-4 flex-1 leading-relaxed whitespace-pre-line">{{ $dump->content }}</p>

                    <div class="flex items-center gap-1 pt-3 border-t-4 border-border-dark">
                        {{-- Pin --}}
                        <form method="POST" action="{{ route('brain-dumps.toggle-pin', $dump) }}">
                            @csrf @method('PATCH')
                            <button type="submit" title="Pin"
                                class="p-2 rounded-button border-2 border-border-dark bg-surface text-txt-secondary hover:text-yellow-500 hover:bg-yellow-50 hover:border-yellow-400 transition-colors">
                                <i class="bx bx-pin text-base"></i>
                            </button>
                        </form>
                        {{-- Archive --}}
                        <form method="POST" action="{{ route('brain-dumps.toggle-archive', $dump) }}">
                            @csrf @method('PATCH')
                            <button type="submit" title="Archive"
                                class="p-2 rounded-button border-2 border-border-dark bg-surface text-txt-secondary hover:text-gray-600 hover:bg-gray-100 transition-colors">
                                <i class="bx bx-archive text-base"></i>
                            </button>
                        </form>
                        {{-- Edit --}}
                        <button type="button"
                            @click="openEdit({ id: {{ $dump->id }}, content: {{ Js::from($dump->content) }}, is_pinned: {{ $dump->is_pinned ? 'true' : 'false' }} })"
                            title="Edit"
                            class="p-2 rounded-button border-2 border-border-dark bg-surface text-txt-secondary hover:text-primary hover:bg-primary/10 transition-colors">
                            <i class="bx bx-edit-alt text-base"></i>
                        </button>
                        {{-- Delete --}}
                        <form id="del-{{ $dump->id }}" method="POST" action="{{ route('brain-dumps.destroy', $dump) }}" class="ml-auto">
                            @csrf @method('DELETE')
                            <button type="button"
                                @click="confirmDelete('del-{{ $dump->id }}')"
                                title="Delete"
                                class="p-2 rounded-button border-2 border-border-dark bg-surface text-txt-secondary hover:text-danger hover:bg-red-50 hover:border-danger transition-colors">
                                <i class="bx bx-trash-alt text-base"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if ($dumps->hasPages())
            <div class="mt-4">{{ $dumps->links() }}</div>
        @endif
    @endif

    {{-- ============================================================
         MODAL: Edit Brain Dump
    ============================================================ --}}
    <div x-show="showModal"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @keydown.escape.window="closeModal()"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
        style="display:none;">

        <div @click.stop
            x-show="showModal"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="scale-95 opacity-0"
            x-transition:enter-end="scale-100 opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="scale-100 opacity-100"
            x-transition:leave-end="scale-95 opacity-0"
            class="bg-surface border-4 border-border-dark rounded-modal shadow-hard w-full max-w-lg"
            style="display:none;">

            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b-4 border-border-dark">
                <h3 class="text-lg font-extrabold">Edit Brain Dump</h3>
                <button @click="closeModal()" class="text-2xl text-txt-secondary hover:text-danger transition-colors">
                    <i class="bx bx-x"></i>
                </button>
            </div>

            {{-- Form --}}
            <form method="POST"
                  :action="editingDump ? '/brain-dumps/' + editingDump.id : '{{ route('brain-dumps.store') }}'"
                  class="p-6 space-y-4">
                @csrf
                <input type="hidden" name="_method" :value="editingDump ? 'PATCH' : 'POST'">

                <div>
                    <label class="block text-sm font-bold mb-1.5">Content <span class="text-danger">*</span></label>
                    <textarea name="content" x-model="form.content" x-ref="contentArea"
                        rows="6" required maxlength="10000"
                        placeholder="Type your thought..."
                        class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none resize-none transition-colors"></textarea>
                </div>

                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_pinned" value="1" x-model="form.is_pinned"
                        class="w-4 h-4 accent-primary">
                    <span class="text-sm font-medium">Pin this thought</span>
                    <i class="bx bx-pin text-yellow-500 text-base"></i>
                </label>

                <div class="flex justify-end gap-3 pt-2 border-t-4 border-border-dark">
                    <button type="button" @click="closeModal()"
                        class="px-6 py-3 font-bold text-sm rounded-button border-4 border-border-dark bg-surface shadow-hard hover:-translate-y-0.5 transition-all duration-200">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-6 py-3 bg-primary text-white font-bold text-sm rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 active:translate-y-1 active:shadow-hard-pressed transition-all duration-200">
                        <i class="bx bx-save mr-1"></i> Update
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>{{-- end x-data --}}
@endsection
