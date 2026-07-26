<div>
    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <x-card>
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-primary rounded-button flex items-center justify-center shadow-hard flex-shrink-0">
                    <i class="bx bx-bulb text-white text-[28px]"></i>
                </div>
                <div>
                    <p class="text-3xl font-extrabold">{{ $stats['total'] ?? 0 }}</p>
                    <p class="text-sm font-medium text-txt-secondary">Total Ideas</p>
                </div>
            </div>
        </x-card>
        <x-card>
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-[#F59E0B] rounded-button flex items-center justify-center shadow-hard flex-shrink-0">
                    <i class="bx bx-edit-alt text-white text-[28px]"></i>
                </div>
                <div>
                    <p class="text-3xl font-extrabold">{{ $stats['draft'] ?? 0 }}</p>
                    <p class="text-sm font-medium text-txt-secondary">Draft</p>
                </div>
            </div>
        </x-card>
        <x-card>
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-secondary rounded-button flex items-center justify-center shadow-hard flex-shrink-0">
                    <i class="bx bx-code-alt text-white text-[28px]"></i>
                </div>
                <div>
                    <p class="text-3xl font-extrabold">{{ $stats['development'] ?? 0 }}</p>
                    <p class="text-sm font-medium text-txt-secondary">Development</p>
                </div>
            </div>
        </x-card>
        <x-card>
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-gray-400 rounded-button flex items-center justify-center shadow-hard flex-shrink-0">
                    <i class="bx bx-archive text-white text-[28px]"></i>
                </div>
                <div>
                    <p class="text-3xl font-extrabold">{{ $stats['archived'] ?? 0 }}</p>
                    <p class="text-sm font-medium text-txt-secondary">Archived</p>
                </div>
            </div>
        </x-card>
    </div>

    {{-- Toolbar --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8">
        <div class="flex flex-wrap items-center gap-3">
            <div class="relative">
                <i class="bx bx-search absolute left-4 top-1/2 -translate-y-1/2 text-txt-secondary text-lg"></i>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search ideas..."
                    class="w-64 pl-11 pr-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none transition-colors">
            </div>
            <select wire:model.live="statusFilter"
                class="px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-semibold text-txt-primary outline-none focus:border-primary transition-colors">
                <option value="">All Status</option>
                <option value="draft">Draft</option>
                <option value="development">Development</option>
                <option value="completed">Completed</option>
                <option value="archived">Archived</option>
            </select>
            <select wire:model.live="platformFilter"
                class="px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-semibold text-txt-primary outline-none focus:border-primary transition-colors">
                <option value="">All Platforms</option>
                <option value="web">Web</option>
                <option value="mobile">Mobile</option>
                <option value="desktop">Desktop</option>
                <option value="cli">CLI</option>
            </select>
        </div>
        <x-button @click="$dispatch('open-modal', { id: 'idea-modal' })">
            <i class="bx bx-plus"></i> New Idea
        </x-button>
    </div>

    {{-- Ideas Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($ideas as $idea)
            <x-card>
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <h3 class="text-lg font-extrabold">{{ $idea->name }}</h3>
                        <p class="text-sm text-txt-secondary mt-1">{{ $idea->tagline ?? Str::limit($idea->description, 80) }}</p>
                    </div>
                    <div class="flex gap-1 flex-shrink-0">
                        @php
                            $priorityColors = ['high' => '#EF4444', 'medium' => '#F59E0B', 'low' => '#22C55E'];
                            $color = $priorityColors[$idea->priority] ?? '#9CA3AF';
                        @endphp
                        <span class="w-2 h-2 rounded-full" style="background: {{ $color }}"></span>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-2 mb-4">
                    <x-badge variant="info">{{ $idea->platform }}</x-badge>
                    <x-badge variant="{{ $idea->status === 'development' ? 'development' : ($idea->status === 'completed' ? 'completed' : ($idea->status === 'archived' ? 'archived' : 'warning')) }}">
                        {{ ucfirst($idea->status) }}
                    </x-badge>
                </div>
                @if ($idea->tech_stack)
                    <div class="flex flex-wrap gap-1.5 mb-4">
                        @foreach (array_slice($idea->tech_stack, 0, 5) as $tech)
                            <span class="px-2.5 py-1 text-[11px] font-bold bg-gray-100 border-2 border-border-dark rounded-full">{{ $tech }}</span>
                        @endforeach
                    </div>
                @endif
                <div class="flex items-center gap-2 pt-4 border-t-4 border-border-dark">
                    <x-button variant="ghost" size="sm" wire:click="$dispatch('edit-idea', { id: {{ $idea->id }} })">
                        <i class="bx bx-edit-alt"></i>
                    </x-button>
                    <x-button variant="ghost" size="sm" class="ml-auto" wire:click="delete({{ $idea->id }})"
                        onclick="return confirm('Hapus idea ini?')">
                        <i class="bx bx-trash text-danger"></i>
                    </x-button>
                </div>
            </x-card>
        @empty
            <div class="col-span-full flex flex-col items-center justify-center py-16 bg-surface border-4 border-border-dark rounded-card shadow-hard">
                <i class="bx bx-bulb text-6xl text-txt-secondary"></i>
                <h3 class="text-xl font-extrabold mt-4">No ideas yet</h3>
                <p class="text-txt-secondary mt-2">Create your first app idea</p>
                <button onclick="Livewire.dispatch('open-modal', { id: 'idea-modal' })"
                    class="mt-4 px-6 py-3 bg-primary text-white font-bold text-sm rounded-button border-4 border-border-dark shadow-hard hover:-translate-y-0.5 transition-all duration-200 ease-out">
                    + New Idea
                </button>
            </div>
        @endforelse
    </div>

    @if ($ideas->hasPages())
        <div class="mt-6">
            {{ $ideas->links() }}
        </div>
    @endif

    {{-- Create/Edit Modal --}}
    <x-modal id="idea-modal" title="New Idea">
        @livewire('idea-form')
    </x-modal>
</div>
