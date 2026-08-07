@extends('layouts.app')

@section('title', 'App Ideas')
@section('page-title', 'App Ideas')

@section('content')
<div x-data="ideaApp()">

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-primary rounded-button flex items-center justify-center shadow-hard flex-shrink-0">
                    <i class="bx bx-bulb text-white text-[28px]"></i>
                </div>
                <div>
                    <p class="text-3xl font-extrabold">{{ $stats['total'] }}</p>
                    <p class="text-sm font-medium text-txt-secondary">Total Ideas</p>
                </div>
            </div>
        </div>
        <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-gray-400 rounded-button flex items-center justify-center shadow-hard flex-shrink-0">
                    <i class="bx bx-file text-white text-[28px]"></i>
                </div>
                <div>
                    <p class="text-3xl font-extrabold">{{ $stats['draft'] }}</p>
                    <p class="text-sm font-medium text-txt-secondary">Draft</p>
                </div>
            </div>
        </div>
        <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-secondary rounded-button flex items-center justify-center shadow-hard flex-shrink-0">
                    <i class="bx bx-code-alt text-white text-[28px]"></i>
                </div>
                <div>
                    <p class="text-3xl font-extrabold">{{ $stats['development'] }}</p>
                    <p class="text-sm font-medium text-txt-secondary">In Development</p>
                </div>
            </div>
        </div>
        <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-gray-500 rounded-button flex items-center justify-center shadow-hard flex-shrink-0">
                    <i class="bx bx-archive text-white text-[28px]"></i>
                </div>
                <div>
                    <p class="text-3xl font-extrabold">{{ $stats['archived'] }}</p>
                    <p class="text-sm font-medium text-txt-secondary">Archived</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Toolbar --}}
    <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 mb-8">
        <div class="flex flex-col sm:flex-row gap-4 items-center justify-between">
            <form method="GET" class="flex flex-col sm:flex-row gap-4 w-full sm:w-auto">
                <div class="relative w-full sm:w-64">
                    <i class="bx bx-search absolute left-4 top-1/2 -translate-y-1/2 text-txt-secondary text-xl"></i>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Search ideas..."
                        class="w-full pl-12 pr-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none transition-colors">
                </div>
                <select name="status" onchange="this.form.submit()"
                    class="w-full sm:w-44 px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium text-txt-primary focus:border-primary outline-none transition-colors">
                    <option value="">All Status</option>
                    <option value="draft" {{ $statusFilter === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="research" {{ $statusFilter === 'research' ? 'selected' : '' }}>Research</option>
                    <option value="development" {{ $statusFilter === 'development' ? 'selected' : '' }}>Development</option>
                    <option value="archived" {{ $statusFilter === 'archived' ? 'selected' : '' }}>Archived</option>
                </select>
                <select name="platform" onchange="this.form.submit()"
                    class="w-full sm:w-44 px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium text-txt-primary focus:border-primary outline-none transition-colors">
                    <option value="">All Platforms</option>
                    <option value="web" {{ $platformFilter === 'web' ? 'selected' : '' }}>Web</option>
                    <option value="ios" {{ $platformFilter === 'ios' ? 'selected' : '' }}>iOS</option>
                    <option value="android" {{ $platformFilter === 'android' ? 'selected' : '' }}>Android</option>
                    <option value="desktop" {{ $platformFilter === 'desktop' ? 'selected' : '' }}>Desktop</option>
                    <option value="api" {{ $platformFilter === 'api' ? 'selected' : '' }}>API</option>
                    <option value="cli" {{ $platformFilter === 'cli' ? 'selected' : '' }}>CLI</option>
                    <option value="other" {{ $platformFilter === 'other' ? 'selected' : '' }}>Other</option>
                </select>
            </form>
            <button type="button" @click="openCreate()" class="px-5 py-3 bg-primary text-white font-bold text-sm rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 active:translate-y-1 transition-all duration-200 ease-out flex items-center gap-2 w-full sm:w-auto justify-center">
                <i class="bx bx-plus text-lg"></i> New Idea
            </button>
        </div>
    </div>

    {{-- Idea Cards Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($ideas as $idea)
            <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1 mr-4">
                        <h3 class="text-lg font-extrabold">{{ $idea->name }}</h3>
                        @if ($idea->tagline)
                            <p class="text-sm text-txt-secondary mt-0.5">{{ $idea->tagline }}</p>
                        @endif
                    </div>
                    <div class="flex-shrink-0">
                        @php $pb = $idea->priority === 'high' ? 'bg-[#EF4444]' : ($idea->priority === 'medium' ? 'bg-[#F59E0B]' : 'bg-[#22C55E]'); @endphp
                        <span class="inline-flex items-center px-3 py-1 text-xs font-bold border-2 border-border-dark rounded-full {{ $pb }} text-white">{{ ucfirst($idea->priority) }}</span>
                    </div>
                </div>
                <div class="flex flex-wrap gap-2 mb-4">
                    <x-badge variant="info">{{ ucfirst($idea->platform) }}</x-badge>
                    <x-badge variant="{{ $idea->status === 'development' ? 'development' : ($idea->status === 'archived' ? 'archived' : ($idea->status === 'research' ? 'warning' : 'default')) }}">
                        {{ ucfirst($idea->status) }}
                    </x-badge>
                </div>
                @if ($idea->tech_stack && count($idea->tech_stack))
                    <div class="flex flex-wrap gap-2 mb-4">
                        @foreach ($idea->tech_stack as $tech)
                            <span class="inline-flex items-center px-3 py-1 text-xs font-bold border-2 border-border-dark rounded-full bg-primary/10">{{ $tech }}</span>
                        @endforeach
                    </div>
                @endif
                <div class="flex items-center justify-between pt-4 border-t-4 border-border-dark">
                    <div class="flex gap-2">
                        <button type="button" @click='openEdit(@json($idea))'
                            class="p-2 rounded-lg text-txt-secondary hover:bg-primary/10 hover:text-primary hover:scale-110 active:scale-95 transition-all duration-200 ease-out" title="Edit Idea">
                            <i class="bx bx-edit text-lg"></i>
                        </button>
                        <form id="delIdea-{{ $idea->id }}" method="POST" action="{{ route('ideas.destroy', $idea) }}" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="button" @click="confirmDelete('delIdea-{{ $idea->id }}', @js($idea->name))"
                                class="p-2 rounded-lg text-txt-secondary hover:bg-danger/10 hover:text-danger hover:scale-110 active:scale-95 transition-all duration-200 ease-out" title="Delete Idea">
                                <i class="bx bx-trash text-lg"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-surface border-4 border-border-dark rounded-card shadow-hard p-12">
                <div class="text-center">
                    <i class="bx bx-lightbulb text-6xl text-txt-secondary"></i>
                    <h3 class="text-xl font-extrabold mt-4">No ideas found</h3>
                    <p class="text-txt-secondary mt-2">Create your first app idea</p>
                    <button type="button" @click="openCreate()" class="mt-4 px-6 py-3 bg-primary text-white font-bold text-sm rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 transition-all duration-200 ease-out">
                        + New Idea
                    </button>
                </div>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($ideas->hasPages())
        <div class="mt-8">
            {{ $ideas->links() }}
        </div>
    @endif

    {{-- ============================================================ --}}
    {{-- MODAL: Create / Edit Idea --}}
    {{-- ============================================================ --}}
    <div x-show="showModal"
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 overflow-y-auto"
        style="display:none;">
        <div x-show="showModal"
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="scale-95 opacity-0" x-transition:enter-end="scale-100 opacity-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="scale-100 opacity-100" x-transition:leave-end="scale-95 opacity-0"
            @click.outside="closeModal()"
            class="bg-surface border-4 border-border-dark rounded-modal shadow-hard w-full max-w-2xl my-6 max-h-[90vh] overflow-y-auto"
            style="display:none;">

            {{-- Modal Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b-4 border-border-dark sticky top-0 bg-surface">
                <h3 class="text-lg font-extrabold" x-text="editingIdea ? 'Edit Idea' : 'New Idea'"></h3>
                <button @click="closeModal()" class="text-2xl text-txt-secondary hover:text-danger transition-colors">
                    <i class="bx bx-x"></i>
                </button>
            </div>

            {{-- Form --}}
            <form method="POST" :action="editingIdea ? '/ideas/'+editingIdea.id : '{{ route('ideas.store') }}'" class="p-6 space-y-5">
                @csrf
                <input type="hidden" name="_method" :value="editingIdea ? 'PUT' : 'POST'">

                <div>
                    <label class="block text-sm font-semibold text-txt-primary mb-1.5">Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" x-model="form.name" required maxlength="255" placeholder="Idea name"
                        class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none transition-colors">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-txt-primary mb-1.5">Tagline</label>
                    <input type="text" name="tagline" x-model="form.tagline" maxlength="500" placeholder="Short tagline"
                        class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none transition-colors">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-txt-primary mb-1.5">Description</label>
                    <textarea name="description" x-model="form.description" rows="3" placeholder="Describe the idea..."
                        class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none transition-colors resize-none"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-txt-primary mb-1.5">Features</label>
                    <textarea name="features" x-model="form.features" rows="4" placeholder="One feature per line"
                        class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none transition-colors resize-none"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-txt-primary mb-1.5">Tech Stack</label>
                    <textarea name="tech_stack" x-model="form.tech_stack" rows="4" placeholder="One technology per line"
                        class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none transition-colors resize-none"></textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-txt-primary mb-1.5">Platform</label>
                        <select name="platform" x-model="form.platform"
                            class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium text-txt-primary focus:border-primary outline-none transition-colors">
                            <option value="">Select platform</option>
                            <option value="web">Web</option>
                            <option value="ios">iOS</option>
                            <option value="android">Android</option>
                            <option value="desktop">Desktop</option>
                            <option value="api">API</option>
                            <option value="cli">CLI</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-txt-primary mb-1.5">Status</label>
                        <select name="status" x-model="form.status" required
                            class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium text-txt-primary focus:border-primary outline-none transition-colors">
                            <option value="draft">Draft</option>
                            <option value="research">Research</option>
                            <option value="development">Development</option>
                            <option value="archived">Archived</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-txt-primary mb-1.5">Priority</label>
                        <select name="priority" x-model="form.priority" required
                            class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium text-txt-primary focus:border-primary outline-none transition-colors">
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-txt-primary mb-1.5">Tags</label>
                    <input type="text" name="tags" x-model="form.tags" placeholder="React, Vue, Laravel"
                        class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none transition-colors">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-txt-primary mb-1.5">Notes</label>
                    <textarea name="notes" x-model="form.notes" rows="3" placeholder="Additional notes..."
                        class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none transition-colors resize-none"></textarea>
                </div>

                {{-- Actions --}}
                <div class="flex justify-end gap-3 pt-2 border-t-4 border-border-dark">
                    <button type="button" @click="closeModal()" class="px-6 py-3 font-bold text-sm rounded-button border-4 border-border-dark bg-surface shadow-hard hover:-translate-y-0.5 transition-all duration-200 ease-out">Cancel</button>
                    <button type="submit" class="px-6 py-3 bg-primary text-white font-bold text-sm rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 active:translate-y-1 active:shadow-hard-pressed transition-all duration-200 ease-out">
                        <i class="bx bx-save mr-1"></i> <span x-text="editingIdea ? 'Update Idea' : 'Save Idea'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>{{-- end x-data --}}

<script>
function ideaApp() {
    return {
        showModal: false,
        editingIdea: null,
        form: {
            name: '',
            tagline: '',
            description: '',
            features: '',
            tech_stack: '',
            platform: '',
            status: 'draft',
            priority: 'medium',
            tags: '',
            notes: '',
        },

        openCreate() {
            this.editingIdea = null;
            this.form = {
                name: '', tagline: '', description: '', features: '', tech_stack: '',
                platform: '', status: 'draft', priority: 'medium', tags: '', notes: '',
            };
            this.showModal = true;
        },

        openEdit(idea) {
            this.editingIdea = idea;
            const join = (arr) => Array.isArray(arr) ? arr.join('\n') : (arr || '');
            this.form = {
                name: idea.name || '',
                tagline: idea.tagline || '',
                description: idea.description || '',
                features: join(idea.features),
                tech_stack: join(idea.tech_stack),
                platform: idea.platform || '',
                status: idea.status || 'draft',
                priority: idea.priority || 'medium',
                tags: Array.isArray(idea.tags) ? idea.tags.join(', ') : (idea.tags || ''),
                notes: idea.notes || '',
            };
            this.showModal = true;
        },

        closeModal() {
            this.showModal = false;
            this.editingIdea = null;
        },

        confirmDelete(formId, ideaName) {
            if (typeof SwalDanger !== 'undefined') {
                SwalDanger.fire({
                    title: 'Hapus Idea?',
                    text: 'Idea "' + ideaName + '" akan dihapus permanen.',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal',
                    showCancelButton: true,
                }).then((result) => {
                    if (result.isConfirmed) document.getElementById(formId).submit();
                });
            } else {
                if (confirm('Hapus idea "' + ideaName + '"?')) {
                    document.getElementById(formId).submit();
                }
            }
        },
    };
}
</script>
@endsection