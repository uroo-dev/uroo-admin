@extends('layouts.app')

@section('title', 'Projects')
@section('page-title', 'Projects')

@section('content')
    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-primary rounded-button flex items-center justify-center shadow-hard flex-shrink-0">
                    <i class="bx bx-folder text-white text-[28px]"></i>
                </div>
                <div>
                    <p class="text-3xl font-extrabold" x-text="$wire.totalProjects ?? 0">0</p>
                    <p class="text-sm font-medium text-txt-secondary">Total Projects</p>
                </div>
            </div>
        </div>

        <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-[#F59E0B] rounded-button flex items-center justify-center shadow-hard flex-shrink-0">
                    <i class="bx bx-code-alt text-white text-[28px]"></i>
                </div>
                <div>
                    <p class="text-3xl font-extrabold" x-text="$wire.developmentProjects ?? 0">0</p>
                    <p class="text-sm font-medium text-txt-secondary">Development</p>
                </div>
            </div>
        </div>

        <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-[#22C55E] rounded-button flex items-center justify-center shadow-hard flex-shrink-0">
                    <i class="bx bx-check-double text-white text-[28px]"></i>
                </div>
                <div>
                    <p class="text-3xl font-extrabold" x-text="$wire.completedProjects ?? 0">0</p>
                    <p class="text-sm font-medium text-txt-secondary">Completed</p>
                </div>
            </div>
        </div>

        <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-danger rounded-button flex items-center justify-center shadow-hard flex-shrink-0">
                    <i class="bx bx-archive text-white text-[28px]"></i>
                </div>
                <div>
                    <p class="text-3xl font-extrabold" x-text="$wire.archivedProjects ?? 0">0</p>
                    <p class="text-sm font-medium text-txt-secondary">Archived</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Toolbar --}}
    <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 mb-8">
        <div class="flex flex-col sm:flex-row gap-4 items-center justify-between">
            <div class="flex flex-col sm:flex-row gap-4 w-full sm:w-auto">
                <div class="relative w-full sm:w-64">
                    <i class="bx bx-search absolute left-4 top-1/2 -translate-y-1/2 text-txt-secondary text-xl"></i>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search projects..."
                        class="w-full pl-12 pr-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none transition-colors">
                </div>
                <select wire:model.live="categoryFilter"
                    class="w-full sm:w-44 px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium text-txt-primary focus:border-primary outline-none transition-colors">
                    <option value="">All Categories</option>
                    <option value="web">Web</option>
                    <option value="mobile">Mobile</option>
                    <option value="api">API</option>
                    <option value="desktop">Desktop</option>
                    <option value="design">Design</option>
                    <option value="other">Other</option>
                </select>
                <select wire:model.live="statusFilter"
                    class="w-full sm:w-44 px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium text-txt-primary focus:border-primary outline-none transition-colors">
                    <option value="">All Status</option>
                    <option value="development">Development</option>
                    <option value="testing">Testing</option>
                    <option value="revision">Revision</option>
                    <option value="completed">Completed</option>
                    <option value="archived">Archived</option>
                </select>
            </div>
            <x-button @click="$dispatch('open-modal', { id: 'project-form' })" variant="primary" size="md" class="w-full sm:w-auto">
                <i class="bx bx-plus"></i>
                New Project
            </x-button>
        </div>
    </div>

    {{-- Project Cards Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($projects ?? [] as $project)
            <div class="bg-surface border-4 border-border-dark rounded-card shadow-hard p-6 transition-all duration-200 ease-out hover:-translate-y-1.5 hover:shadow-hard-hover">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-extrabold">{{ $project->name }}</h3>
                        <p class="text-sm text-txt-secondary mt-0.5">{{ $project->client?->name ?? 'No Client' }}</p>
                    </div>
                    <x-badge variant="{{ $project->status }}">{{ ucfirst($project->status) }}</x-badge>
                </div>

                <p class="text-sm text-txt-secondary mb-4 line-clamp-2">{{ $project->description ?? 'No description' }}</p>

                {{-- Tech Stack Tags --}}
                @if ($project->tech_stack ?? false)
                    <div class="flex flex-wrap gap-2 mb-4">
                        @foreach (is_array($project->tech_stack) ? $project->tech_stack : (json_decode($project->tech_stack, true) ?? []) as $tech)
                            <span class="inline-flex items-center px-3 py-1 text-xs font-bold border-2 border-border-dark rounded-full bg-primary/10">
                                {{ $tech }}
                            </span>
                        @endforeach
                    </div>
                @endif

                {{-- Progress --}}
                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="font-medium">Progress</span>
                        <span class="font-extrabold">{{ $project->progress ?? 0 }}%</span>
                    </div>
                    <div class="w-full h-4 bg-gray-200 rounded-full border-2 border-border-dark overflow-hidden">
                        <div class="h-full bg-primary rounded-full transition-all duration-500"
                            style="width: {{ $project->progress ?? 0 }}%"></div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-end gap-2 mt-4 pt-4 border-t-4 border-border-dark">
                    <x-button variant="ghost" size="sm" wire:click="edit({{ $project->id }})">
                        <i class="bx bx-edit text-base"></i>
                    </x-button>
                    <x-button variant="ghost" size="sm" wire:click="view({{ $project->id }})">
                        <i class="bx bx-show text-base"></i>
                    </x-button>
                    <x-button variant="ghost" size="sm" wire:click="confirmDelete({{ $project->id }})">
                        <i class="bx bx-trash text-base text-danger"></i>
                    </x-button>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-surface border-4 border-border-dark rounded-card shadow-hard p-12">
                <div class="text-center">
                    <i class="bx bx-folder-open text-5xl text-txt-secondary"></i>
                    <p class="text-txt-secondary font-medium mt-3">No projects found</p>
                    <x-button variant="primary" size="sm" class="mt-4" @click="$dispatch('open-modal', { id: 'project-form' })">
                        <i class="bx bx-plus"></i>
                        Create First Project
                    </x-button>
                </div>
            </div>
        @endforelse
    </div>

    @if (method_exists($projects ?? [], 'links'))
        <div class="mt-8">
            {{ $projects->links() }}
        </div>
    @endif

    {{-- Modal Form --}}
    <x-modal id="project-form" title="Project Form" maxWidth="max-w-2xl">
        <form wire:submit="save" class="space-y-5">
            <x-input label="Project Name" name="name" placeholder="Project name" wire:model="form.name" />

            <div class="space-y-1.5">
                <label class="block text-sm font-semibold text-txt-primary">Description</label>
                <textarea wire:model="form.description" rows="3" placeholder="Project description..."
                    class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none transition-colors resize-none"></textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="space-y-1.5">
                    <label class="block text-sm font-semibold text-txt-primary">Client</label>
                    <select wire:model="form.client_id"
                        class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium text-txt-primary focus:border-primary outline-none transition-colors">
                        <option value="">Select Client</option>
                        @foreach ($clients ?? [] as $client)
                            <option value="{{ $client->id }}">{{ $client->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="block text-sm font-semibold text-txt-primary">Category</label>
                    <select wire:model="form.category"
                        class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium text-txt-primary focus:border-primary outline-none transition-colors">
                        <option value="">Select Category</option>
                        <option value="web">Web</option>
                        <option value="mobile">Mobile</option>
                        <option value="api">API</option>
                        <option value="desktop">Desktop</option>
                        <option value="design">Design</option>
                        <option value="other">Other</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="space-y-1.5">
                    <label class="block text-sm font-semibold text-txt-primary">Status</label>
                    <select wire:model="form.status"
                        class="w-full px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium text-txt-primary focus:border-primary outline-none transition-colors">
                        <option value="development">Development</option>
                        <option value="testing">Testing</option>
                        <option value="revision">Revision</option>
                        <option value="completed">Completed</option>
                        <option value="archived">Archived</option>
                    </select>
                </div>

                <x-input label="Progress (%)" name="progress" type="number" min="0" max="100" wire:model="form.progress" />
            </div>

            {{-- Tech Stack --}}
            <div x-data="{
                techStack: @entangle('form.tech_stack'),
                newTech: '',
                addTech() {
                    if (this.newTech.trim() && !this.techStack.includes(this.newTech.trim())) {
                        this.techStack.push(this.newTech.trim());
                        $wire.set('form.tech_stack', this.techStack);
                        this.newTech = '';
                    }
                },
                removeTech(index) {
                    this.techStack.splice(index, 1);
                    $wire.set('form.tech_stack', this.techStack);
                }
            }">
                <label class="block text-sm font-semibold text-txt-primary mb-2">Tech Stack</label>
                <div class="flex items-center gap-2 mb-3">
                    <input type="text" x-model="newTech" @keydown.enter.prevent="addTech" placeholder="Add technology..."
                        class="flex-1 px-4 py-3 rounded-input border-4 border-border-dark bg-surface text-sm font-medium placeholder:text-txt-secondary focus:border-primary outline-none transition-colors">
                    <x-button variant="secondary" size="sm" type="button" @click="addTech">
                        <i class="bx bx-plus"></i>
                    </x-button>
                </div>
                <div class="flex flex-wrap gap-2">
                    <template x-for="(tech, index) in techStack" :key="index">
                        <span class="inline-flex items-center gap-1 px-3 py-1 text-xs font-bold border-2 border-border-dark rounded-full bg-primary/10">
                            <span x-text="tech"></span>
                            <button type="button" @click="removeTech(index)" class="text-danger hover:text-red-700">
                                <i class="bx bx-x"></i>
                            </button>
                        </span>
                    </template>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <x-button variant="secondary" type="button" @click="$dispatch('close-modal', { id: 'project-form' })">
                    Cancel
                </x-button>
                <x-button variant="primary" type="submit">
                    <i class="bx bx-check"></i>
                    Save Project
                </x-button>
            </div>
        </form>
    </x-modal>
@endsection
