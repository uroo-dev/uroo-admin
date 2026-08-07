@extends('layouts.app')
@section('title', 'Quality Control')
@section('page-title', 'Quality Control')

@section('content')

{{-- Alpine component — defined BEFORE x-data --}}
<script>
function qcApp() {
    return {
        showModal: false,
        editingId: null,
        form: { title: '', items: [] },

        openCreate() {
            this.editingId = null;
            this.form = { title: '', items: [] };
            this.showModal = true;
            this.$nextTick(() => this.$refs.titleInput && this.$refs.titleInput.focus());
        },

        openEdit(id, title, items) {
            this.editingId = id;
            this.form = {
                title: title || '',
                items: (items || []).map(label => ({ label })),
            };
            this.showModal = true;
        },

        closeModal() {
            this.showModal = false;
            this.editingId = null;
        },

        addItem() {
            this.form.items.push({ label: '' });
            this.$nextTick(() => {
                const inputs = this.$el.querySelectorAll('[data-item-input]');
                if (inputs.length) inputs[inputs.length - 1].focus();
            });
        },

        removeItem(idx) {
            this.form.items.splice(idx, 1);
        },

        confirmDelete(formId, title) {
            SwalDanger.fire({
                title: 'Delete Checklist?',
                html: `"<strong>${title}</strong>" and all its items will be permanently deleted.`,
                confirmButtonText: 'Yes, delete!',
                cancelButtonText: 'Cancel',
            }).then(result => {
                if (result.isConfirmed) document.getElementById(formId).submit();
            });
        },
    };
}
</script>

<div x-data="qcApp()">

    @php
        $totalChecklists = $checklists->count();
        $totalItems      = $checklists->sum(fn($c) => $c->items->count());
        $totalChecked    = $checklists->sum(fn($c) => $c->items->where('is_checked', true)->count());
        $readyCount      = $checklists->filter(fn($c) => $c->items->count() > 0 && $c->items->where('is_checked',true)->count() === $c->items->count())->count();
    @endphp

    {{-- Stats Bar --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-4 flex items-center gap-3">
            <div class="w-10 h-10 bg-primary/10 border-2 border-border-dark rounded-button flex items-center justify-center flex-shrink-0">
                <i class="bx bx-list-check text-primary text-xl"></i>
            </div>
            <div>
                <p class="text-2xl font-extrabold leading-none">{{ $totalChecklists }}</p>
                <p class="text-xs font-semibold text-txt-secondary mt-0.5">Checklists</p>
            </div>
        </div>
        <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-4 flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-50 border-2 border-border-dark rounded-button flex items-center justify-center flex-shrink-0">
                <i class="bx bx-check-square text-blue-500 text-xl"></i>
            </div>
            <div>
                <p class="text-2xl font-extrabold leading-none">{{ $totalItems }}</p>
                <p class="text-xs font-semibold text-txt-secondary mt-0.5">Total Items</p>
            </div>
        </div>
        <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-4 flex items-center gap-3">
            <div class="w-10 h-10 bg-green-50 border-2 border-border-dark rounded-button flex items-center justify-center flex-shrink-0">
                <i class="bx bx-check-circle text-green-500 text-xl"></i>
            </div>
            <div>
                <p class="text-2xl font-extrabold leading-none">{{ $totalChecked }}</p>
                <p class="text-xs font-semibold text-txt-secondary mt-0.5">Checked</p>
            </div>
        </div>
        <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-4 flex items-center gap-3">
            <div class="w-10 h-10 bg-yellow-50 border-2 border-border-dark rounded-button flex items-center justify-center flex-shrink-0">
                <i class="bx bxs-star text-yellow-400 text-xl"></i>
            </div>
            <div>
                <p class="text-2xl font-extrabold leading-none">{{ $readyCount }}</p>
                <p class="text-xs font-semibold text-txt-secondary mt-0.5">100% Ready</p>
            </div>
        </div>
    </div>

    {{-- Toolbar Card --}}
    <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-5 mb-6 flex items-center justify-between gap-4">
        <div>
            <h2 class="font-extrabold text-base">Deployment Checklists</h2>
            <p class="text-txt-secondary text-sm mt-0.5">Track readiness before every deployment</p>
        </div>
        <button type="button" @click="openCreate()"
            class="px-5 py-3 bg-primary text-white font-bold text-sm rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 active:translate-y-1 transition-all duration-200 ease-out flex items-center gap-2 flex-shrink-0">
            <i class="bx bx-plus text-lg"></i> New Checklist
        </button>
    </div>

    {{-- Flash --}}
    @if (session('success'))
        <div class="mb-5 px-5 py-4 bg-surface border-4 border-green-500 rounded-card shadow-hard flex items-center gap-3">
            <i class="bx bx-check-circle text-green-500 text-2xl"></i>
            <span class="font-semibold text-sm">{{ session('success') }}</span>
        </div>
    @endif

    {{-- Empty state --}}
    @if ($checklists->isEmpty())
        <div class="flex flex-col items-center justify-center py-20 bg-surface border-4 border-border-dark rounded-card shadow-hard">
            <i class="bx bx-check-shield text-7xl text-txt-secondary"></i>
            <h3 class="text-xl font-extrabold mt-4">No checklists yet</h3>
            <p class="text-txt-secondary mt-2 text-sm">Create your first deployment checklist</p>
            <button type="button" @click="openCreate()"
                class="mt-5 px-6 py-3 bg-primary text-white font-bold text-sm rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 transition-all duration-200 ease-out">
                <i class="bx bx-plus mr-1"></i> New Checklist
            </button>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            @foreach ($checklists as $checklist)
                @php
                    $total   = $checklist->items->count();
                    $checked = $checklist->items->where('is_checked', true)->count();
                    $pct     = $total > 0 ? round(($checked / $total) * 100) : 0;

                    if ($pct === 100 && $total > 0) {
                        $badge = ['label' => '✓ All Done', 'class' => 'bg-green-500 text-white border-green-600'];
                    } elseif ($pct >= 60) {
                        $badge = ['label' => 'Almost Ready', 'class' => 'bg-yellow-400 text-gray-900 border-yellow-600'];
                    } elseif ($pct >= 30) {
                        $badge = ['label' => 'In Progress', 'class' => 'bg-blue-400 text-white border-blue-600'];
                    } else {
                        $badge = ['label' => 'Not Ready', 'class' => 'bg-gray-200 text-txt-secondary border-gray-400'];
                    }
                @endphp

                <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-5 flex flex-col transition-all duration-200 ease-out hover:-translate-y-1 hover:shadow-hard-hover">

                    {{-- Card Header --}}
                    <div class="flex items-start justify-between gap-3 mb-4">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-11 h-11 rounded-button flex items-center justify-center flex-shrink-0 bg-primary/10 border-2 border-border-dark">
                                <i class="bx bx-list-check text-primary text-xl"></i>
                            </div>
                            <div class="min-w-0">
                                <h3 class="font-extrabold text-sm leading-snug">{{ $checklist->title }}</h3>
                                <p class="text-xs text-txt-secondary mt-0.5">{{ $total }} items &bull; {{ $checked }} checked</p>
                            </div>
                        </div>
                        <span class="text-xs font-bold px-2.5 py-1 border-2 rounded-full flex-shrink-0 {{ $badge['class'] }}">
                            {{ $badge['label'] }}
                        </span>
                    </div>

                    {{-- Progress Bar --}}
                    <div class="mb-4">
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-xs font-semibold text-txt-secondary">Progress</span>
                            <span class="text-xs font-extrabold {{ $pct === 100 ? 'text-green-500' : 'text-txt-primary' }}">{{ $pct }}%</span>
                        </div>
                        <div class="w-full h-3 bg-gray-100 border-2 border-border-dark rounded-full overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-500 {{ $pct === 100 ? 'bg-green-500' : ($pct >= 60 ? 'bg-yellow-400' : 'bg-primary') }}"
                                 style="width: {{ $pct }}%"></div>
                        </div>
                    </div>

                    {{-- Checklist Items --}}
                    @if ($checklist->items->count() > 0)
                    <div class="space-y-1.5 mb-4 flex-1">
                        @foreach ($checklist->items->sortBy('sort_order') as $item)
                            <form method="POST" action="{{ route('quality-control.toggle-checked', $item) }}">
                                @csrf @method('PATCH')
                                <button type="submit"
                                    class="w-full flex items-center gap-3 px-3 py-2.5 rounded-button border-2 border-border-dark text-left transition-all duration-150 hover:bg-gray-50 {{ $item->is_checked ? 'bg-green-50 border-green-300' : '' }}">
                                    <span class="w-5 h-5 flex-shrink-0 rounded border-2 border-border-dark flex items-center justify-center {{ $item->is_checked ? 'bg-green-500 border-green-600' : 'bg-white' }}">
                                        @if ($item->is_checked)
                                            <i class="bx bx-check text-white text-sm font-bold"></i>
                                        @endif
                                    </span>
                                    <span class="text-sm font-medium flex-1 text-left {{ $item->is_checked ? 'line-through text-txt-secondary' : 'text-txt-primary' }}">
                                        {{ $item->label }}
                                    </span>
                                </button>
                            </form>
                        @endforeach
                    </div>
                    @else
                        <p class="text-sm text-txt-secondary italic mb-4 flex-1">No items yet. Edit to add items.</p>
                    @endif

                    {{-- Card Actions --}}
                    <div class="flex items-center gap-2 pt-4 border-t-4 border-border-dark mt-auto">
                        <button type="button"
                            @click="openEdit(
                                {{ $checklist->id }},
                                {{ Js::from($checklist->title) }},
                                {{ Js::from($checklist->items->sortBy('sort_order')->pluck('label')) }}
                            )"
                            class="flex-1 flex items-center justify-center gap-1.5 px-4 py-2.5 text-sm font-bold rounded-button border-4 border-border-dark bg-surface shadow-hard-sm hover:-translate-y-0.5 transition-all duration-200 ease-out">
                            <i class="bx bx-edit-alt text-base"></i> Edit
                        </button>
                        <form id="del-qc-{{ $checklist->id }}" action="{{ route('quality-control.destroy', $checklist) }}" method="POST">
                            @csrf @method('DELETE')
                            <button type="button"
                                @click="confirmDelete('del-qc-{{ $checklist->id }}', {{ Js::from($checklist->title) }})"
                                class="px-4 py-2.5 text-sm font-bold rounded-button border-4 border-border-dark bg-surface shadow-hard-sm hover:-translate-y-0.5 hover:bg-danger hover:text-white transition-all duration-200 ease-out flex items-center gap-1.5">
                                <i class="bx bx-trash-alt text-base"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- ============================================================
         MODAL: Create / Edit Checklist
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
            <div class="flex items-center justify-between px-6 py-4 border-b-4 border-border-dark sticky top-0 bg-surface rounded-t-modal z-10">
                <h3 class="text-lg font-extrabold" x-text="editingId ? 'Edit Checklist' : 'New Checklist'"></h3>
                <button @click="closeModal()" class="text-2xl text-txt-secondary hover:text-danger transition-colors">
                    <i class="bx bx-x"></i>
                </button>
            </div>

            {{-- Form --}}
            <form method="POST"
                  :action="editingId ? '/quality-control/' + editingId : '{{ route('quality-control.store') }}'"
                  class="p-6 space-y-5">
                @csrf
                <input type="hidden" name="_method" :value="editingId ? 'PATCH' : 'POST'">

                {{-- Title --}}
                <div>
                    <label class="block text-sm font-bold mb-1.5">
                        Checklist Title <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="title" x-model="form.title" x-ref="titleInput" required maxlength="255"
                        placeholder="E.g. Production Deployment v2.0"
                        class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none transition-colors">
                </div>

                {{-- Items --}}
                <div>
                    <label class="block text-sm font-bold mb-2">Checklist Items</label>

                    <div class="space-y-2 mb-3">
                        <template x-for="(item, idx) in form.items" :key="idx">
                            <div class="flex items-center gap-2">
                                <span class="w-6 h-6 rounded border-2 border-border-dark bg-gray-100 flex items-center justify-center flex-shrink-0">
                                    <i class="bx bx-dots-vertical-rounded text-txt-secondary text-sm"></i>
                                </span>
                                <input type="text" :name="`items[${idx}][label]`"
                                    x-model="item.label"
                                    data-item-input
                                    placeholder="Item description..."
                                    required
                                    class="flex-1 px-3 py-2.5 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none transition-colors">
                                <button type="button" @click="removeItem(idx)"
                                    class="p-2 text-txt-secondary hover:text-danger rounded-button border-2 border-transparent hover:border-danger/30 transition-colors flex-shrink-0">
                                    <i class="bx bx-x text-lg"></i>
                                </button>
                            </div>
                        </template>
                    </div>

                    <button type="button" @click="addItem()"
                        class="flex items-center gap-1.5 px-4 py-2 text-sm font-bold text-primary border-2 border-dashed border-primary/50 rounded-button hover:bg-primary/5 transition-colors w-full justify-center">
                        <i class="bx bx-plus text-lg"></i> Add Item
                    </button>
                </div>

                {{-- Footer --}}
                <div class="flex justify-end gap-3 pt-2 border-t-4 border-border-dark">
                    <button type="button" @click="closeModal()"
                        class="px-6 py-3 font-bold text-sm rounded-button border-4 border-border-dark bg-surface shadow-hard hover:-translate-y-0.5 transition-all duration-200">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-6 py-3 bg-primary text-white font-bold text-sm rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 active:translate-y-1 active:shadow-hard-pressed transition-all duration-200">
                        <i class="bx bx-save mr-1"></i>
                        <span x-text="editingId ? 'Update' : 'Save'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>{{-- end x-data --}}
@endsection
